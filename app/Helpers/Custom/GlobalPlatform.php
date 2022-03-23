<?php


namespace App\Helpers\Custom;


use App\Enums\NotificationMessageType;
use App\Models\Districts;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\GroupUser;
use App\Models\User;
use App\Models\GlobalNormRef;
use App\Notifications\EventChannel;
use App\Services\App\UserService;
use App\Services\NotificationMessageService;
use App\Types\Tba\AnnexType;

use App\Services\BlobMediaService;

trait GlobalPlatform
{
    /**
     * 群組ID 轉頻道ID
     *
     * @param integer $groupId
     * @return int
     */
    public static function convertGroupIdToChannelId(int $groupId): int
    {
        return (int)GroupChannel::query()->where('group_id', $groupId)->pluck('id')->first();
    }

    /**
     * 頻道ID 轉 群組ID
     *
     * @param integer $channelId
     * @return int
     */
    public static function convertChannelIdToGroupId(int $channelId): int
    {
        return (int)GroupChannel::query()->where('id', $channelId)->pluck('group_id')->first();
    }

    /**
     * 學校簡碼 轉 群組ID
     *
     * @param $school_code
     * @return mixed
     */
    public static function convertSchoolCodeToGroupId($school_code)
    {
        return Group::query()->where('school_code', $school_code)->pluck('id')->first();
    }

    /**
     *  轉換 年級
     *
     * @param integer $stage
     * @param integer $grade
     * @return int|null
     */
    public static function convertGrade(int $stage, int $grade): ?int
    {
        switch ($stage) {
            case 3:
                return $grade <= 3 ? $grade + 6 : $grade;
            case 4:
            case 5:
                return $grade <= 3 ? $grade + 9 : $grade;
            default :
                return $grade;
        }
    }

    /**
     * 取得頻道使用者身份
     *
     * @param int $groupId
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder|\LaravelIdea\Helper\App\Models\_GroupUserQueryBuilder|mixed
     */
    public static function getMemberDuty(int $groupId, int $userId)
    {
        return GroupUser::query()->where('group_id', $groupId)
            ->where('user_id', $userId)
            ->where('member_status', 1)
            ->value('member_duty');
    }

    /**
     * 無效不顯示  invalid (0, 0) 1  取消無效不顯示
     * 頻道內觀摩  valid (1, 0)  2
     * 全平台分享  share (1, 1) 3
     * 尚待審核中  pending (2, 0) 4
     * @param integer $statusId
     * @return array
     */
    public static function convertChannelStatusToSql(int $statusId): array
    {
        switch ($statusId) {
            case 1:
                $status = [
                    'content_status' => 0,
                    'content_public' => 0
                ];
                break;
            case 2:
                $status = [
                    'content_status' => 1,
                    'content_public' => 0
                ];
                break;
            case 3:
                $status = [
                    'content_status' => 1,
                    'content_public' => 1
                ];
                break;
            default:
                $status = [
                    'content_status' => 2,
                    'content_public' => 0
                ];
                break;
        }
        return $status;
    }

    /**
     * 學區簡碼 轉 群組ID
     * @param string $abbr
     * @return mixed
     */
    public static function convertDistrictToGroupId(string $abbr)
    {
        return Districts::query()->where('abbr', $abbr)->get()->first()->districtGroups->map(function ($q) {
            return $q->groups_id;
        });
    }

    /**
     * 學區資訊
     * @param string $abbr
     * @return mixed
     */
    public static function convertAbbrToDistrictInfo(string $abbr)
    {
        return Districts::query()->with('districtLang')->where('abbr', $abbr)->get()->first();
    }

    /**
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public static function getUserInfo(int $userId)
    {
        return User::query()->findOrFail($userId);
    }

    /**
     * @param string $value
     * @return string
     */
    public static function convertAnnexType(string $value): string
    {
        switch ($value) {
            case AnnexType::HiTeachNote:
                $text = 'hiTeachNote';
                break;
            case AnnexType::LessonPlan:
                $text = 'lessonPlan';
                break;
            case AnnexType::Material:
                $text = 'material';
                break;
            case AnnexType::Link:
                $text = 'link';
                break;
            default:
                $text = 'other';
        }
        return $text;
    }

    /**
     * 依據條件顯示影片 並排除重複的
     * @param \Illuminate\Support\Collection $tbaIds
     * @param array $content_public
     * @param array $content_status
     * @return GroupChannelContent|\Illuminate\Database\Eloquent\Builder|int
     */
    public static function getGroupChannelContentTotal(\Illuminate\Support\Collection $tbaIds, array $content_public, array $content_status)
    {
        return GroupChannelContent::query()->distinct('content_id')->whereIn('content_id', $tbaIds)
            ->whereIn('content_public', $content_public)
            ->whereIn('content_status', $content_status);
    }

    /**
     * 依據條件顯示影片 並排除重複的
     * 針對數據畫像使用
     * @param \Illuminate\Support\Collection $tbaIds
     * @param array $content_public
     * @param array $content_status
     * @return GroupChannelContent|\Illuminate\Database\Eloquent\Builder|int
     */
    public static function getGroupChannelContentForTotalAndTbaIds(\Illuminate\Support\Collection $tbaIds, array $content_public, array $content_status)
    {
        $select = "group_concat(distinct(content_id)) tba_ids,count(content_id) as total";
        return GroupChannelContent::query()->selectRaw($select)->distinct('content_id')->whereIn('content_id', $tbaIds)
            ->whereIn('content_public', $content_public)
            ->whereIn('content_status', $content_status)->get()->first();

    }

    /**
     * 取得影片長度
     * @param $full_video_path
     * @return int
     */
    public static function getDuration($full_video_path): int
    {
        if (!is_file($full_video_path)) {
            return 0;
        }

        $getID3           = new \getID3;
        $file             = $getID3->analyze($full_video_path);
        $playtime_seconds = $file['playtime_seconds'];
//        $duration = date('H:i:s.v', $playtime_seconds);
        return (int)$playtime_seconds;
    }

    /**
     * Get media link from Evaluate event file
     * @param $tbaEvaluateEventFile
     * @return array
     */
    public static function getAttachmentData(object $tbaEvaluateEventFile)
    {
        $blobMediaService = new BlobMediaService();

        // extract attachment (image or media)
        $attachmentType = null;
        $attachmentURL  = null;
        if (is_object($tbaEvaluateEventFile) && !is_null($tbaEvaluateEventFile->image_url)) {
            $attachmentType = "img";
            $attachmentURL  = $tbaEvaluateEventFile->image_url;
        } else if (is_object($tbaEvaluateEventFile) && !is_null($tbaEvaluateEventFile->name) && !is_null($tbaEvaluateEventFile->ext)) {
            $eventId        = $tbaEvaluateEventFile->tba_evaluate_event_id;
            $blobName       = $tbaEvaluateEventFile->name . "." . $tbaEvaluateEventFile->ext;
            $blobPath       = $eventId . "/" . $blobName;
            $attachmentType = "media";
            $attachmentURL  = (is_null($blobPath)) ? null : $blobMediaService->getBlobLink($blobPath, true);
        }
        return array("url" => $attachmentURL, "type" => $attachmentType);
    }

    /**
     * 送通知
     * @param int $group_id
     * @param array $user_ids
     * @param int $type
     * @return bool
     */
    public static function sendNotify(int $group_id, array $user_ids, int $type): bool
    {
        $isSuccessful = true;
        try {
            $notification_message = app(notificationMessageService::class)->firstWhere(['group_id' => $group_id, 'type' => $type]);
            if ($type === NotificationMessageType::Join && $notification_message === null) {
                $notification_message = app(notificationMessageService::class)->firstWhere(['group_id' => null, 'type' => NotificationMessageType::Join]);
            }

            $json_decode_message                            = json_decode($notification_message->content, true);
            $json_decode_message['notification_message_id'] = $notification_message->id;
            app(userService::class)->findWhereIn('id', $user_ids)->map(function ($user) use ($notification_message, $json_decode_message) {
                if ($user->notifications()->whereJsonContains('data->notification_message_id', $notification_message->id)->get()->isEmpty()) {
                    $user->notify(new EventChannel($json_decode_message));
                }
            });
        } catch (\Exception $exception) {
            $isSuccessful = false;
        }
        return $isSuccessful;
    }

    /**
     * Get Yearly Global Norm Ref Data
     * This function will get the global norm reference data for the latest year.
     * @return GlobalNormRef
     */
    public static function getGlobalNormRefData(): GlobalNormRef
    {
        return GlobalNormRef::latest('year')->first();
    }
    
    /**
     * Get the current station (ORG, CN)
     * @return string
     */
    public static function getCurrentStation(): string
    {
        return strtoupper(getenv('APP_STATION', 'ORG'));
    }
}

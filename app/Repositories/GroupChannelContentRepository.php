<?php

namespace App\Repositories;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\GradeLang;
use App\Models\GroupSubjectFields;
use App\Models\Rating;
use App\Types\Group\DutyType;
use LogicException;
use InvalidArgumentException;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Types\Cms\CmsType;
use Yish\Generators\Foundation\Repository\Repository;

class GroupChannelContentRepository extends Repository
{
    //
    /**
     * @var GroupChannelContent
     */
    protected $otherSortValue = 99999; // Give a big number to be sorted to the last
    protected $groupChannelContent;

    public function __construct(GroupChannelContent $groupChannelContent)
    {
        $this->groupChannelContent = $groupChannelContent;
    }

    //
    public function getContent($channelId, $contentId, $contentType)
    {
        return GroupChannelContent::where([
            'group_channel_id' => $channelId,
            'content_id'       => $contentId,
            'content_type'     => $contentType,
        ])->firstOrFail();
    }

    //
    public function createContent($channelId, $content, $extra_info)
    {
        if (!CmsType::checkContent($content['content_type'])) {
            throw new InvalidArgumentException('group channel content type is illegal');
        }

        $exist = GroupChannelContent::where([
            'group_channel_id' => $channelId,
            'content_id'       => $content['content_id'],
            'content_type'     => $content['content_type'],
        ])->exists();

        if ($exist) {
            throw new LogicException('group channel content is already exist');
        }

        $group   = GroupChannel::findOrFail($channelId)->group()->firstOrFail();
        $subject = isset($extra_info['subject']) ? GroupSubjectFields::where(['groups_id' => $group->id, 'subject' => $extra_info['subject']])->first() : null;
        $rating  = Rating::where(['groups_id' => $group->id, 'type' => 0])->first();

        $content['group_id']                = $group->id;
        $content['group_channel_id']        = $channelId;
        $content['content_status']          = ($group->review_status == '1') ? 2 : 1;
        $content['content_public']          = 0;
        $content['group_subject_fields_id'] = isset($subject) ? $subject->id : null;
        $content['grades_id']               = isset($extra_info['educational_stage_id'], $extra_info['grade']) ? GlobalPlatform::convertGrade($extra_info['educational_stage_id'], $extra_info['grade']) : null;
        $content['ratings_id']              = $rating->id;
        $content['share_status']            = 1;

        return GroupChannelContent::create($content);
    }

    /**
     * 計算學科 數量
     *
     * @param int $channelId
     * @param int $userId
     * @param null $ratingId
     * @return mixed
     */
    public function getSubjectCount(int $channelId, int $userId, $ratingId = null)
    {
        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId($channelId);

        GlobalPlatform::getMemberDuty($convertChannelIdToGroupId, $userId) === null
            ? $content_public = [1]
            : $content_public = [1, 0];

        $groupChannelContents = $this->groupChannelContent->query()
            ->selectRaw("count(*) as total, group_channel_contents.group_subject_fields_id , alias as subject ,`order`")
            ->leftJoin('group_subject_fields', 'group_subject_fields.id', 'group_channel_contents.group_subject_fields_id')
//            ->with([
//                'groupSubjectFields' => function ($q) {
//                    $q->selectRaw("id, alias as subject ,`order`")->orderBy('order', 'DESC');
//                }
//            ])
            ->where('group_id', $convertChannelIdToGroupId)
            ->whereIn('content_public', $content_public)
            ->where('content_status', 1)
            ->groupBy('group_subject_fields_id')
            ->orderBy('order', 'ASC')
            ->orderByRaw("ISNULL(group_subject_fields_id),group_subject_fields_id ASC");

        // 增加 $ratingId 條件連動
        if (is_numeric($ratingId)) {
            $groupChannelContents->where('ratings_id', $ratingId);
        }
        
        $res = $groupChannelContents->get()->map(function ($groupChannelContent) {
            if ($groupChannelContent->group_subject_fields_id === null)
                return [
                        'text' => __('app/subject-field.Other'),
                        'value' => $groupChannelContent->total,
                        'id' => 'Other',
                        'order' => $this->otherSortValue
                    ];
            return [
                'text' => $groupChannelContent->subject,
                'value' => $groupChannelContent->total,
                'id' => $groupChannelContent->group_subject_fields_id,
                'order' => $groupChannelContent->order
            ];
        });

        // Sort by order (ASC)
        $res = collect($res)->sortBy('order');

        return $res->values()->all();
    }

    /**
     * 計算年級 數量
     *
     * @param int $channelId
     * @param int $userId
     * @param null $group_subject_fields_id
     * @param null $ratingId
     * @return mixed
     */
    public function getGradeCount(int $channelId, int $userId, $group_subject_fields_id = null, $ratingId = null)
    {

        $lang                      = new \App\Libraries\Lang\Lang();
        $locales_id                = $lang->getConvertByLangString(\App::getLocale());
        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId($channelId);
        GlobalPlatform::getMemberDuty($convertChannelIdToGroupId, $userId) === null
            ? $content_public = [1]
            : $content_public = [1, 0];

        $groupChannelContents = $this->groupChannelContent->query()
            ->selectRaw("count(*) as total, group_channel_contents.grades_id")
            ->where('group_id', $convertChannelIdToGroupId)
            ->whereIn('content_public', $content_public)
            ->where('content_status', 1)
            ->groupBy('group_channel_contents.grades_id')
            ->orderByRaw("ISNULL(group_channel_contents.grades_id),group_channel_contents.grades_id ASC");

        // 增加 $group_subject_fields_id 條件連動
        if (is_numeric($group_subject_fields_id)) {
            $groupChannelContents->where('group_subject_fields_id', $group_subject_fields_id);
        }
        // 增加 $ratingId 條件連動
        if (is_numeric($ratingId)) {
            $groupChannelContents->where('ratings_id', $ratingId);
        }
        // 增加 $group_subject_fields_id 條件連動
        if ($group_subject_fields_id === 'Other') {
            $groupChannelContents->whereNull('group_subject_fields_id');
        }

        $gradeLangs = GradeLang::query()->where('locales_id', $locales_id)->get();


        $result = collect();
        $groupChannelContents->get()->map(function ($groupChannelContent) use ($gradeLangs, $result) {
            $gradeLangs->where('grades_id', $groupChannelContent->grades_id)->map(function ($gradeLang) use ($groupChannelContent, $result) {
                $result->push(['text' => $gradeLang->name, 'value' => $groupChannelContent->total, 'id' => $groupChannelContent->grades_id]);
            });

            if ($groupChannelContent->grades_id === null) {
                $result->push([
                    'text' => __('app/subject-field.Other'),
                    'value' => $groupChannelContent->total,
                    'id' => $this->otherSortValue
                ]);
            }
        });

        // Sort by id (ASC)
        $result = collect($result)->sortBy('id');

        return $result->values()->all();
    }

    /**
     * .教研(評等) 數量
     *
     * @param int $channelId
     * @param int $userId
     * @return mixed
     */
    public function getRatingCount(int $channelId, int $userId)
    {
        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId($channelId);

        GlobalPlatform::getMemberDuty($convertChannelIdToGroupId, $userId) === null
            ? $content_public = [1]
            : $content_public = [1, 0];

        $groupChannelContents = $this->groupChannelContent->query()
            ->with([
                'groupRatingFields' => function ($q) {
                    $q->select('id', 'type', 'name');
                }
            ])
            ->selectRaw("count(*) as total, group_channel_contents.ratings_id")
            ->where('group_id', $convertChannelIdToGroupId)
            ->whereIn('content_public', $content_public)
            ->where('content_status', 1)
            ->groupBy('group_channel_contents.ratings_id')
            ->orderBy('group_channel_contents.ratings_id', 'ASC')
            ->whereNotNull('group_channel_contents.ratings_id')
            ->get();


        $result = $groupChannelContents->map(function ($q) {
            $rating = $q->groupRatingFields->first();
            return [
                'text' => $rating->name,
                'value' => $q->total,
                'id' => $q->ratings_id,
                'type' => $rating->type
            ];
        });

        // Sort by type (ASC)
        $result = collect($result)->sortBy('type');

        return $result->values()->all();
    }

    public function getContentAndGroupSubjectFields($contentId, $groupId)
    {
        return $this->groupChannelContent->query()
            ->where('content_id', $contentId)
            ->where('group_id', $groupId)
            ->with([
                'groupSubjectFields' => function ($q) {
                    $q->with([
                        'subjectFields' => function ($q) {
                            $q->select('subject_fields_id', 'name', 'locales_id');
                        }
                    ]);
                }
            ])
            ->get();
    }

}

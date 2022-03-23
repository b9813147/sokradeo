<?php

namespace App\Repositories;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Lang\Lang;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectFields;
use App\Models\Rating;
use App\Models\TbaAnnex;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Models\User;
use App\Models\ExhibitionCmsSet;
use App\Models\Tba;
use App\Models\Video;
use App\Models\TbaFavorite;
use App\Models\TbaHistory;
use App\Models\TbaComment;
use App\Repositories\CommentRepository;
use App\Repositories\TbaRepository;
use App\Repositories\RecommendedVideoRepository;
use App\Types\Cms\CmsType;
use App\Types\Tba\AnnexType;
use App\Types\Video\Encoder;

use Illuminate\Support\Facades\DB;

class ExhibitionRepository
{
    protected $latestVideosLimit = 50;
    protected $commentRepository;
    protected $tbaRepository;

    public function __construct(CommentRepository $commentRepository, TbaRepository $tbaRepository, RecommendedVideoRepository $recommendedVideoRepository)
    {
        $this->commentRepository = $commentRepository;
        $this->tbaRepository = $tbaRepository;
        $this->recommendedVideoRepository = $recommendedVideoRepository;
    }

    //
    public function checkCmsSet($cmsId, $cmsType, $setTypes = [])
    {
        $query = ExhibitionCmsSet::where([
            'cms_id'   => $cmsId,
            'cms_type' => $cmsType,
        ]);

        if (!empty($setTypes)) {
            $query->where(function ($q) use ($setTypes) {
                foreach ($setTypes as $v) {
                    $q->orWhere('type', $v);
                }
            });
        }

        return $query->exists();
    }

    //
    public function getCmsSets($cmsType, $setType)
    {
        switch ($cmsType) {
            case CmsType::Video:
                return Video::query()->join('exhibition_cms_sets', 'exhibition_cms_sets.cms_id', 'videos.id')
                    ->select('videos.*', 'exhibition_cms_sets.thumbnail as cms_thumb', 'exhibition_cms_sets.channel_id')
                    ->where('exhibition_cms_sets.cms_type', $cmsType)
                    ->where('type', $setType)->orderBY('order', 'DESC')->get();
            case CmsType::Tba:
            case CmsType::TbaVideo:
                return Tba::query()
                    ->join('exhibition_cms_sets', 'exhibition_cms_sets.cms_id', 'tbas.id')
                    ->select('tbas.*', 'exhibition_cms_sets.thumbnail as cms_thumb', 'exhibition_cms_sets.channel_id')
                    ->with(['videos' => function ($q) {
                        // Get videos -> resource -> vods to find rdata for video duration display
                        $q->select('resource_id');
                        $q->with(['resource' => function ($q) {
                            $q->select('id');
                            $q->with(['vod' => function ($q) {
                                $q->select('resource_id', 'rdata');
                            }]);
                        }]);
                    }])
                    ->where('exhibition_cms_sets.cms_type', $cmsType)
                    ->where('type', $setType)->orderBY('order', 'DESC')->get();
            default:
                assert(false);
        }

        return null;
    }

    //
    public function getGroupChannelSets($cmsType): \Illuminate\Support\Collection
    {
        $contentType = ($cmsType === CmsType::TbaVideo) ? CmsType::Tba : $cmsType;
        $select      = [
            'group_channels.id', 'group_channels.name', 'group_channels.description', 'group_channels.thumbnail', 'groups.public',
            'sum(IF(group_channel_contents.content_public = 1, 1, 0)) as total_content',
            'sum(IF(group_channel_contents.content_public = 1 or group_channel_contents.content_public = 0, 1, 0)) as total_content_all'
        ];
        $select      = DB::raw(implode(',', $select));
        // 公開頻道
        return DB::table('group_channels')
            ->join('groups', 'groups.id', '=', 'group_channels.group_id')
            ->where('group_channels.cms_type', $cmsType)
            ->where('group_channels.public', 1)
            ->join('group_channel_contents', 'group_channels.id', '=', 'group_channel_contents.group_channel_id')
            ->where('group_channel_contents.content_type', $contentType)
            ->where('group_channel_contents.content_status', 1)
            ->whereIn('group_channel_contents.content_public', [1, 0])
            ->select($select)
            ->groupBy('group_channels.id')
            ->orderBy('groups.public', 'DESC')
            ->orderBy('total_content', 'DESC')
            ->get();
    }

    // 針對學區使用
    public function getGroupChannelSetsByDistrict($cmsType, $setType, $groupIds = null)
    {
        $lang = (new Lang())->getConvertByLangString(\App::getLocale());

        $contentType = ($cmsType === CmsType::TbaVideo) ? CmsType::Tba : $cmsType;

//        $select = ['MAX(exhibition_group_channel_sets.order) AS order_set', 'group_channels.id', 'group_channels.name', 'group_channels.description', 'group_channels.thumbnail'];
        $select = ['MAX(list_order) AS order_set', 'group_channels.id', 'group_langs.name', 'group_langs.description', 'group_channels.thumbnail', 'list_top'];
        $select = DB::raw(implode(',', $select));

        $exhibition_group_channel_sets = GroupChannel::query()
            ->join('group_langs', 'group_langs.groups_id', 'group_channels.group_id')
            ->join('district_groups', 'district_groups.groups_id', 'group_channels.group_id')
            ->where('group_langs.locales_id', $lang)
            ->where('cms_type', $cmsType)
            ->where('status', 1)
            ->select($select)
            ->groupBy('group_channels.group_id')
            ->whereIn('district_groups.groups_id', $groupIds)
            ->orderBy('list_order', 'asc')
            ->get();

        // 公開頻道
        $total_public = $this->getChannelByCount($exhibition_group_channel_sets, $contentType, [1], [1], 'total_content');
        // 不分公開與不公開
        $total_all = $this->getChannelByCount($exhibition_group_channel_sets, $contentType, [1, 2], [0, 1, 2], 'total_content_all');

        return $exhibition_group_channel_sets->map(function ($q) use ($total_public) {
            foreach ($total_public as $item) {
                if ($q->id === $item->group_channel_id) {
                    $q->total_content = $item->total_content;
                }
            }
            return $q;
        })->map(function ($q) use ($total_all) {
            foreach ($total_all as $item) {
                if ($q->id === $item->group_channel_id) {
                    $q->total_content_all = $item->total_content_all;
                }
            }
            return $q;
        });
    }

    // 僅限個人
    public function getGroupChannelSetsByUser($cmsType, $setType, $groupIds = null)
    {
        $lang = (new Lang())->getConvertByLangString(\App::getLocale());

        $contentType = ($cmsType === CmsType::TbaVideo) ? CmsType::Tba : $cmsType;

//        $select = ['MAX(exhibition_group_channel_sets.order) AS order_set', 'group_channels.id', 'group_channels.name', 'group_channels.description', 'group_channels.thumbnail'];
        $select = ['group_channels.id', 'group_channels.group_id', 'group_langs.name', 'group_langs.description', 'group_channels.thumbnail', 'groups.school_upload_status'];
        $select = DB::raw(implode(',', $select));

        $exhibition_group_channel_sets = GroupChannel::query()
            ->join('group_langs', 'group_langs.groups_id', 'group_channels.group_id')
            ->join('groups', 'groups.id', 'group_channels.group_id', 'groups.school_upload_status')
            ->where('group_langs.locales_id', $lang)
            ->where('group_channels.cms_type', $cmsType)
            ->where('group_channels.status', 1)
            ->where('groups.public', '!=', 1)
            ->select($select)
            ->groupBy('group_channels.group_id')
            ->whereIn('group_id', $groupIds)
//            ->orderBy('l', 'asc')
            ->get();

        // 公開頻道
        $total_public = $this->getChannelByCount($exhibition_group_channel_sets, $contentType, [1], [1], 'total_content');
        // 不分公開與不公開
        $total_all = $this->getChannelByCount($exhibition_group_channel_sets, $contentType, [1, 2], [0, 1, 2], 'total_content_all');

        return $exhibition_group_channel_sets->map(function ($q) use ($total_public) {
            foreach ($total_public as $item) {
                if ($q->id === $item->group_channel_id) {
                    $q->total_content = $item->total_content;
                }
            }
            return $q;
        })->map(function ($q) use ($total_all) {
            foreach ($total_all as $item) {
                if ($q->id === $item->group_channel_id) {
                    $q->total_content_all = $item->total_content_all;
                }
            }
            return $q;
        });
    }

    /**
     * 統計學區
     * @param int $userId
     * @return array
     */
    public function getUserVideoCount(int $userId): array
    {
        $user    = User::query()->findOrFail($userId);
        $tba_ids = Tba::query()->where('habook_id', $user->habook)->pluck('id');

        // 全頻道公開
        $publicTotal = GlobalPlatform::getGroupChannelContentForTotalAndTbaIds($tba_ids, [1], [1]);

        //  影片總數
        $total = GlobalPlatform::getGroupChannelContentForTotalAndTbaIds($tba_ids, [0, 1], [1, 2]);

        // 個人影片
        $contentIds = GlobalPlatform::getGroupChannelContentForTotalAndTbaIds($tba_ids, [0, 1], [1, 2]);

        // For tbaStatistics Sql
        $select = 'MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T
                  ,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P
                  ,tba_statistics.tba_id
                  ,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C';

        $mapTba = Tba::query()->whereIn('id', explode(',', $contentIds->tba_ids))->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) use ($select) {
                $q->selectRaw($select)
                    ->groupBy('tba_statistics.tba_id');
            }
        ])->orderBy('lecture_date', 'DESC');
        // 雙綠燈
        $doubleGreenLightTotal            = collect();
        $doubleGreenLightTotal['tba_ids'] = collect();
        $doubleGreenLightTotal['total']   = $mapTba->get()->filter(function ($tba) use ($doubleGreenLightTotal) {
            if ($tba->tbaStatistics->isNotEmpty()) {
                if ((int)$tba->tbaStatistics->first()->T >= 70 && (int)$tba->tbaStatistics->first()->P >= 70) {
                    $doubleGreenLightTotal['tba_ids']->push($tba->tbaStatistics->first()->tba_id);
                    return $tba->tbaStatistics;
                }
            }
        })->count();

        // Comments
        $publicMark          = (object)[];
        $publicComments      = collect($this->commentRepository->getCommentsByUser($userId, 1));
        $publicMark->total   = number_format($publicComments->count());
        $publicMark->tba_ids = $publicComments->pluck('tba_id');

        // 本人標記
        $privateMark          = (object)[];
        $privateComments      = collect($this->commentRepository->getCommentsByUser($userId, 0));
        $privateMark->total   = number_format($privateComments->count());
        $privateMark->tba_ids = $privateComments->pluck('tba_id');

        // 影片公開 標記數點評的數量
        $isMark                 = (object)[];
        $receivedComments       = collect($this->commentRepository->getReceivedCommentsByUser($userId));
        $isMark->total          = number_format($receivedComments->count());
        $isMark->tba_ids        = $receivedComments->pluck('tba_id');

        // 公開影片數
        $public = (object)[];

        $public->total   = number_format($publicTotal->total);
        $public->tba_ids = explode(',', $publicTotal->tba_ids);

        // 影片總數
        $all          = (object)[];
        $all->total   = number_format($total->total);
        $all->tba_ids = explode(',', $total->tba_ids);

        // 雙綠燈
        $doubleGreenLight          = (object)[];
        $doubleGreenLight->total   = number_format($doubleGreenLightTotal->get('total'));
        $doubleGreenLight->tba_ids = $doubleGreenLightTotal->get('tba_ids')->toArray();

        // 點閱數
        $hits_total          = (object)[];
        $hits_total->tba_ids = $mapTba->where('hits', '!=', 0)->pluck('id');
        $hits_total->total   = number_format((int)$mapTba->sum('hits'));

        // Annex file
        $material          = $this->tbaAnnex($tba_ids, AnnexType::Material);
        $material->total   = number_format((int)$material->total);
        $lessonPlan        = $this->tbaAnnex($tba_ids, AnnexType::LessonPlan);
        $lessonPlan->total = number_format((int)$lessonPlan->total);

        // Favorite Videos
        $favoriteVideos = (object)[];
        $favoriteVideos->total = number_format(TbaFavorite::query()->where('user_id', $userId)->count());

        // Watch History
        $watchHistory = (object)[];
        $watchHistory->total = number_format(TbaHistory::query()->where('user_id', $userId)->where('url', '!=', null)->count());

        // General Observation
        $generalObsrv = (object)[];
        $tbaIdColl = Video::query()
            ->where('user_id', $userId)
            ->where('encoder', Encoder::FILE_UPLOAD)
            ->with(['tbas' => function ($q) {
                $q->select('id', 'tba_id');
            }])->get()->pluck('tbas')->flatten()->pluck('tba_id');
        $generalObsrv->tba_ids = $tbaIdColl->toArray();
        $generalObsrv->total = number_format($tbaIdColl->count());

        // Videos Observed
        $videosObserved = (object)[];
        $videosObserved->total = (int)
            count($this->tbaRepository->getVideosObsrvedTbaIds($userId));
        
        // Recommended Videos
        $recommendedVideos = (object)[];
        $recommendedVideos->total = (int) $this->recommendedVideoRepository->getAllRecommendedVideos()->count();

        // Latest Videos
        $latestVideos = (object)[];
        $latestVideos->limit = (int) $this->latestVideosLimit;

        return [
            'public_total'           => $public,
            'hits_total'             => $hits_total,
            'all_total'              => $all,
            'doubleGreenLight_total' => $doubleGreenLight,
            'private_mark'           => $privateMark,
            'total_mark'             => $isMark,
            'public_mark'            => $publicMark,
            'material'               => $material,
            'lessonPlan'             => $lessonPlan,
            'favoriteVideos'         => $favoriteVideos,
            'watchHistory'           => $watchHistory,
            'generalObsrv'           => $generalObsrv,
            'videosObserved'         => $videosObserved,
            'recommendedVideos'      => $recommendedVideos,
            'latestVideos'           => $latestVideos,
        ];
    }

    /**
     * Get all comments for a user
     * @param int $userId
     * @param string $mode
     * @param $filter
     * @param int $size
     * @return Object
     */
    public function getUserComments(int $userId, string $mode, $filter = "", int $size): object
    {
        $comments = (object)[];
        if (!$userId || !$mode) return $comments;

        // Get commet data based on a given mode
        if ($mode === "public")
            $comments = $this->getPublicCommentsQueryByUser($userId);
        else if ($mode === "private")
            $comments = $this->getPrivateCommentsQueryByUser($userId);
        elseif ($mode === "isMark") {
            $comments = $this->getIsMarkCommentsQueryByUser($userId);
        }


        // Create tbaEvaluateEvent
        $tbaEvaluateEvent = $comments
            ->with('tba', 'tbaEvaluateEventMode', 'tbaEvaluateEventFiles', 'tbaEvaluateUser')
            ->orderBy('tba_evaluate_events.updated_at', 'desc');

        // Filter based on keyword
        if ($filter && !empty($filter) && is_string($filter))
            $tbaEvaluateEvent = $tbaEvaluateEvent->where('text', 'LIKE', '%' . $filter . '%');

        // pagination
        if ($size)
            $tbaEvaluateEvent = $tbaEvaluateEvent->paginate($size);

        return $tbaEvaluateEvent;
    }

    // Get categorical choices
    public function getCategoricalChoices(string $groupId): object
    {
        $group_subject_fields = Rating::query()
            ->where('groups_id', $groupId)
            ->where('type', '!=', 1)
            ->orderBy('id', 'ASC')
            ->get();

        return $group_subject_fields;
    }

    // Get subject choices
    public function getSubjectChoices(string $groupId): object
    {
        $group_subject_fields = GroupSubjectFields::query()
            ->where('groups_id', $groupId)
            ->orderBy('id', 'ASC')
            ->get();

        return $group_subject_fields;
    }

    /**
     * 計算影片數量
     * @param \Illuminate\Support\Collection $tbaIds
     * @param array $content_public
     * @param array $content_status
     * @return GroupChannelContent|\Illuminate\Database\Eloquent\Builder|int
     */
    private function getGroupChannelContentTotal(\Illuminate\Support\Collection $tbaIds, array $content_public, array $content_status)
    {
        return GroupChannelContent::query()->distinct('content_id')->whereIn('content_id', $tbaIds)
            ->whereIn('content_public', $content_public)
            ->whereIn('content_status', $content_status);
    }

    /**
     * 僅限頻道使用
     * @param \Illuminate\Support\Collection $exhibition_group_channel_sets
     * @param string $contentType
     * @param array $content_status
     * @param array $content_public
     * @param string $total
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    private function getChannelByCount(\Illuminate\Support\Collection $exhibition_group_channel_sets, string $contentType, $content_status, $content_public, string $total)
    {
        return GroupChannelContent::query()
            ->selectRaw("COUNT(content_id) AS $total ,group_channel_id")
            ->whereIn('group_channel_id', $exhibition_group_channel_sets->pluck('id'))
            ->where('content_type', $contentType)
            ->whereIn('content_status', $content_status)
            ->whereIn('content_public', $content_public)
            ->groupBy('group_channel_id')
            ->get();
    }

    /**
     * 指定附件 類別 統計 及 細目
     * @param $tba_ids
     * @param string $AnnexType
     * @return mixed
     */
    private function tbaAnnex($tba_ids, string $AnnexType)
    {
        $select = "COUNT(CASE WHEN type = '$AnnexType' THEN tba_id END)   AS total,
                   group_concat(distinct(tba_id)) tba_ids";
        return TbaAnnex::query()->selectRaw($select)->where('type', "$AnnexType")
            ->whereIn('tba_id', $tba_ids)->get()->first();
    }

    private function getDistinctSqlForComments()
    {
        return 'distinct group_channel_contents.content_id, id';
    }

    private function getPublicCommentsQueryByUser($userId)
    {
        $distinctSql                   = $this->getDistinctSqlForComments();
        $tbaEvaluateEventUserIdByOwner = TbaEvaluateUser::query()
            ->selectRaw($distinctSql)
            ->join('group_channel_contents', 'group_channel_contents.content_id', 'tba_evaluate_users.tba_id')
            ->where('user_id', $userId)
            ->pluck('id');
        $publicMark                    = TbaEvaluateEvent::query()->whereIn('tba_evaluate_user_id', $tbaEvaluateEventUserIdByOwner);
        return $publicMark;
    }

    /**
     * 被標記數
     *
     * @param $userId
     * @return TbaEvaluateEvent|\Illuminate\Database\Eloquent\Builder
     */
    private function getIsMarkCommentsQueryByUser($userId)
    {
        $user       = User::query()->findOrFail($userId);
        $tba_ids    = Tba::query()->where('habook_id', $user->habook)->pluck('id');
        $contentIds = GlobalPlatform::getGroupChannelContentForTotalAndTbaIds($tba_ids, [0, 1], [1, 2]);
        // 影片公開 標記數點評的數量
        $tbaEvaluateEventUserId = TbaEvaluateUser::query()->whereIn('tba_id', explode(',', $contentIds->tba_ids))->where('identity', '!=', 'G')->pluck('id');

        return TbaEvaluateEvent::query()->whereIn('tba_evaluate_user_id', $tbaEvaluateEventUserId);
    }

    private function getPrivateCommentsQueryByUser($userId)
    {
        $privateMark = TbaEvaluateEvent::query()->where('user_id', $userId);
        return $privateMark;
    }

}

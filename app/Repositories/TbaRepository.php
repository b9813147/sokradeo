<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

use App\Helpers\Custom\GlobalPlatform;
use App\Libraries\Lang\Lang;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\Tba;
use App\Models\TbaAnalysisEvent;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaEvaluateUser;
use App\Models\TbaComment;
use App\Models\User;
use App\Types\Tba\AnnexType;
use App\Types\Tba\IdentityType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpDocumentor\Reflection\Types\Integer;

class TbaRepository
{
    protected $model;

    //
    public function __construct(Tba $tba)
    {
        $this->model = $tba;
    }

    /*
     * tba
     * */

    //
    public function list($page = 1)
    {
        return Tba::paginate(null, ['*'], 'page', $page);
    }

    //
    public function listByUserId($userId, $page = 1)
    {
        return User::findOrFail($userId)->tbas()->paginate(null, ['*'], 'page', $page);
    }

    //
    public function getTbasByUserId($userId, $limit = 10, $orders = [], $conds = [])
    {
        $query = User::findOrFail($userId)->tbas()->where($conds)->limit($limit);

        foreach ($orders as $order) {
            $query->orderBy($order['col'], $order['dir']);
        }

        return $query->get();
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     * @Description 取得自己所在的群組
     */
    public function getMyChannel($userId)
    {
        // $query = \DB::table('users')
        //     ->join('group_user', 'users.id', 'group_user.user_id')
        //     ->join('groups', 'groups.id', 'group_user.group_id')
        //     ->where('users.id', $userId)
        //     ->orderBy('groups.created_at','Desc')
        //     ->get();
        $query = User::query()->where('id', $userId)
            ->with([
                'groups' => function ($q) {
                    $q->with([
                        'channels' => function ($q) {
                            $q->select('*')->orderBy('created_at', 'Desc');
                        }
                    ]);
                }
            ])
            ->first();

        return $query;
    }

    /**
     * //取得群組裡面的影片
     * @param object $groupIds
     * @param array $content_status
     * @param array $content_public
     * @param null $group_status
     * @param integer $paginate
     * @param false $district
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getGroupChannel($groupIds, $content_status = [], $content_public = [], $group_status = null, $paginate = 0, $district = false)
    {
        if (!is_object($groupIds))
            $groupIds = collect([$groupIds]);

        // Get Tbas
        $mapTba = null;
        if ($district) {
            $query      = DB::table('district_channel_contents')
                ->join('groups', 'district_channel_contents.groups_id', '=', 'groups.id')
                ->whereIn('district_channel_contents.groups_id', $groupIds);
            $contentIds = $query->pluck('content_id')->toArray();
            $mapTba     = $this->getTbaQuery($contentIds);
        } else {
            $query      = DB::table('group_channel_contents')
                ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
                ->where('content_type', 'Tba')
                ->whereIn('content_status', $content_status)
                ->whereIn('group_channel_contents.content_public', $content_public)
                ->where('group_channel_contents.group_id', $groupIds);
            $contentIds = $query->pluck('content_id')->toArray();
            $mapTba     = $this->getTbaQuery($contentIds, array($groupIds->first()));
        }

        if ($paginate)
            return $mapTba->paginate($paginate);

        return $mapTba->get();
    }

    /**
     * 僅限於學校頻道內部篩選
     *
     * @param array|int $groupIds
     * @param array $content_status
     * @param array $content_public
     * @param null $group_status
     * @param array $resultFilter
     * @param integer $paginate
     * @param bool $district
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getGroupByFilter($groupIds = null, $content_status = [], $content_public = [], $group_status = null, $resultFilter = [], $paginate = 0, $district = false)
    {
        $lang       = new Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());

        $sql      = collect();
        $sqlByTba = collect();
        $resultFilter->each(function ($v, $k) use (&$sql, &$sqlYear, &$sqlByTba) {
            if ($k === 'eduStages') {
                $k = 'educational_stage_id';
            }
            if ($k === 'grades') {
                $k = 'grades_id';
            }
            if ($k === 'subjectFields') {
                $k = 'group_subject_fields_id';
            }
            if ($k === 'districtSubjectFields') {
                $k = 'district_subjects_id';
            }
            if ($k === 'lectureTypes') {
                $k = 'lecture_type';
            }
            if ($k === 'years') {
                $k = 'lecture_date';
            }
            if ($k === 'rating') {
                $k = 'ratings_id';
            }
            if ($k === 'search') {
                $sqlByTba->put($k, $v);
            }

            $sql->put($k, $v);
        });
        $query = null;
        if ($district) {
            $query = DB::table('district_channel_contents')
                ->join('groups', 'district_channel_contents.groups_id', '=', 'groups.id')
                ->leftJoin('district_group_subjects', 'district_group_subjects.group_subject_fields_id', 'district_channel_contents.group_subject_fields_id')
                ->leftJoin('district_subjects', 'district_subjects.id', 'district_group_subjects.district_subjects_id')
                ->whereIn('district_channel_contents.groups_id', $groupIds);
        } else {
            $query = DB::table('group_channel_contents')
                ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
                ->where('content_type', 'Tba')
                ->whereIn('content_status', $content_status)
                ->whereIn('group_channel_contents.content_public', $content_public)
                ->where('group_channel_contents.group_id', $groupIds)
                ->where('groups.status', $group_status);
        }

        if ($sql->has('group_subject_fields_id') && $sql['group_subject_fields_id'] === 'Other') {
            $query->whereNull('group_subject_fields_id');
            $sql->forget('group_subject_fields_id');
        }
        //todo 待修改
        if ($sql->has('district_subjects_id') && $sql['district_subjects_id'] === 'Other') {
            $query->whereNull('district_subjects.id');
            $sql->forget('district_subjects_id');
        }

        if ($sql->has('grades_id') && $sql['grades_id'] === 'Other') {
            $query->whereNull('grades_id');
            $sql->forget('grades_id');
        }

        if ($sql->has('group_subject_fields_id') || $sql->has('grades_id') || $sql->has('district_subjects_id')) {
            $sql->forget('search');
            $query->where($sql->toArray());
        }
        if ($sql->has('ratings_id')) {
            $query->where($sql->toArray());
        }

        // Get tbas
        $contentIds = $query->pluck('content_id')->toArray();
        $mapTba     = $this->getTbaQuery($contentIds);

        if ($sqlByTba->has('search')) {
            $sql->forget('search');
            $search = $sqlByTba->pull('search');
            $mapTba->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%$search%")
                    ->orWhere('teacher', 'like', "%$search%");
            });
        }
        if ($sql->has('lecture_date')) {
            $mapTba->whereYear('lecture_date', $sql->pull('lecture_date'));
        }
        if ($sql->isNotEmpty()) {
            $query->where($sql->toArray());
        }
        if ($paginate) {
            return $mapTba->paginate($paginate);
        }
        return $mapTba->get();
    }

    public function getTbasInChannel($channelId, $conditions = [])
    {
        $query = DB::table('tbas')
            ->join('group_channel_contents', 'group_channel_contents.content_id', 'tbas.id')
            ->join('group_channels', 'group_channels.id', 'group_channel_contents.group_channel_id')
            ->where('group_channel_contents.group_channel_id', $channelId)
            ->whereIn('group_channel_contents.content_status', [1, 2])
            ->where($conditions)
            ->select('tbas.id', 'tbas.user_id',
                'group_channel_contents.content_status', 'group_channel_contents.content_public', 'group_channel_contents.content_update_limit',
                'group_channels.upload_limit', 'group_channels.upload_ended_at')
            ->get();

        return $query;
    }

    /**
     * 頻道內部 統計用
     * @param $channel
     * @return array
     */
    public function getChannelByCount($channel)
    {
        //影片狀態
        $content_status = [1];
        // 發布狀態
        $content_public = [1];
        $group_status   = 1;

        // 全平台公開
        $publicTotal = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', [1])
            ->whereIn('group_channel_contents.content_public', $content_public)
            ->where('group_channel_contents.group_channel_id', $channel)
            ->where('groups.status', $group_status)->count();

        // 全部影片
        $query = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', [1])
            ->whereIn('group_channel_contents.content_public', [1, 0])
            ->where('group_channel_contents.group_channel_id', $channel);

        $contentIds = $query->pluck('content_id');

        $mapTba = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) {
                $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                    ->groupBy('tba_statistics.tba_id');
            }
        ])->orderBy('lecture_date', 'DESC');

        $doubleGreenLightTotal = $mapTba->get()->filter(function ($tba) {
            if ($tba->tbaStatistics->isNotEmpty()) {
                if ((int)$tba->tbaStatistics->first()->T >= 70 && (int)$tba->tbaStatistics->first()->P >= 70) {
                    return $tba->tbaStatistics;
                }
            }
        })->count();


        $result = [
            'public_total'           => number_format($publicTotal),
            'hits_total'             => number_format((int)$mapTba->sum('hits')),
            'all_total'              => number_format($mapTba->count()),
            'doubleGreenLight_total' => number_format($doubleGreenLightTotal)
        ];
        return $result;
    }

    /**
     * @param null $userId
     * @param array $content_status
     * @param null $content_public
     * @param null $group_status
     * @param int $paginate
     * @return Tba[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getMyMovies($userId = null, $content_status = [], $content_public = null, $group_status = null, $paginate = 0)
    {
        $habookId   = User::query()->findOrFail($userId)->habook;
        $tbasIds    = Tba::query()->where('habook_id', $habookId)->pluck('id');
        $contentIds = GlobalPlatform::getGroupChannelContentTotal($tbasIds, [0, 1], [1, 2])->pluck('content_id')->toArray();
        $mapTba     = $this->getTbaQuery($contentIds);
        if ($paginate) return $mapTba->paginate($paginate);
        return $mapTba->get();
    }

    /**
     * @param int $userId
     * @param int $contentId
     * @return Tba[]
     */
    public function getMyMovie(int $userId = null, int $contentId)
    {
        $habookId = User::query()->findOrFail($userId)->habook;
        if (!$habookId) return [];
        $mapTba = $this->getTbaQuery(array($contentId));
        return $mapTba->get();
    }

    /**
     * @param array $contentIds
     * @param int $paginate
     * @return Tba[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getFilterMovie(array $contentIds, int $paginate = 0)
    {
        $mapTba = $this->getTbaQuery($contentIds);
        if ($paginate && $paginate > 0)
            return $mapTba->paginate($paginate);
        return $mapTba->get();
    }

    /**
     * Get My Observed Movies (the videos that have this user's comments)
     * @param int $userId
     * @param int $paginate
     * @return Tba[]|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getMyObservedMovies(int $userId, int $paginate = 0)
    {
        $tbaIds = $this->getVideosObsrvedTbaIds($userId);
        $mapTba = $this->getTbaQuery($tbaIds)
            ->with([
                'tbaComment' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ]);

        if ($paginate && $paginate > 0)
            return $mapTba->paginate($paginate);

        return $mapTba->get();
    }

    /**
     * Get Tba ids for Videos Observed
     * @param int $userId
     * @return array
     */
    public function getVideosObsrvedTbaIds(int $userId): array
    {
        // Extract tba ids from tba comments
        // Note: Only get public comments
        $tbaIds = TbaComment::query()
            ->where('user_id', $userId)
            ->where('public', 1)
            ->orderBy('updated_at', 'desc')
            ->distinct()
            ->pluck('tba_id')
            ->toArray();
        
        // Check if tba ids are valid from group channel content
        $filteredTbaIds = GroupChannelContent::query()
            ->whereIn('content_id', $tbaIds)
            ->orderBy('updated_at', 'desc')
            ->distinct()
            ->pluck('content_id')
            ->toArray();

        return $filteredTbaIds;
    }

    /**
     * Get Tba Query builder
     * $filterGroupIds will be used to filter groupChannels (used in myChannel page)
     * @param array $contentIds
     * @param array $filterGroupIds [optional]
     * @return Object
     */
    private function getTbaQuery(array $contentIds, array $filterGroupIds = [])
    {
        $lang       = new Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());

        $mapTba = Tba::query()->whereIn('id', $contentIds)
            ->with([
                'user'                => function ($q) {
                    $q->select('users.id', 'users.name');
                },
                'tbaPlaylistTracks'   => function ($q) {
                    $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                        ->orderBy('tba_playlist_tracks.list_order');
                },
                'groupChannels'       => function ($q) use ($filterGroupIds) {
                    $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description');
                    // If groupIds are provided, filter groupChannels by groupIds
                    if (count($filterGroupIds) > 0)
                        $q->whereIn('group_channels.group_id', $filterGroupIds);
                },
                'tbaStatistics'       => function ($q) {
                    $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                        ->groupBy('tba_statistics.tba_id');
                },
                'groupChannelContent' => function ($q) use ($locales_id) {
                    $q->select('content_id', 'group_subject_fields_id', 'ratings_id', 'grades_id')->with([
                        'groupSubjectFields' => function ($q) {
                            $q->selectRaw("alias as subject, id");
                        },
                        'groupRatingFields'  => function ($q) {
                            $q->select('name', 'id');
                        },
                        'grade'              => function ($q) use ($locales_id) {
                            $q->where('locales_id', $locales_id)->select('name', 'grades_id');
                        }
                    ]);
                },
                'tbaEvaluateEvents'   => function ($q) {
                    $q->selectRaw('tba_id, COUNT(id) as total')->whereBetween('tba_evaluate_event_mode_id', [1, 10])->groupBy('tba_id');
                },
                'tbaAnnexs'           => function ($q) {
                    // Only select LessonPlan and Material for checking eligibility
                    $q->select('type', 'tba_id')
                        ->where('type', AnnexType::LessonPlan)
                        ->orWhere('type', AnnexType::Material);
                },
                'videos'              => function ($q) {
                    // Get videos -> resource -> vods to find rdata for video duration display
                    $q->select('resource_id');
                    $q->with([
                        'resource' => function ($q) {
                            $q->select('id');
                            $q->with([
                                'vod' => function ($q) {
                                    $q->select('resource_id', 'rdata');
                                }
                            ]);
                        }
                    ]);
                },
            ])
            ->whereHas('groupChannels') // ensure groupChannels are not empty
            ->orderBy('lecture_date', 'DESC')
            ->orderBy('created_at', 'DESC');

        return $mapTba;
    }

    /**
     * @param int $channelId
     * @return GroupChannel|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|Collection|\Illuminate\Database\Eloquent\Model|object
     */
    public function getChannelInfo($channelId)
    {
        $lang       = new Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());
        return GroupChannel::with([
            'DistrictGroup' => function ($q) use ($locales_id) {
                $q->select('id', 'groups_id', 'districts_id');
                $q->with([
                    'districts'    => function ($q) {
                        $q->select('id', 'abbr', 'school_code');
                    },
                    'districtLang' => function ($q) use ($locales_id) {
                        $q->where('locales_id', $locales_id)->select('name', 'locales_id', 'districts_id');
                    },
                ]);
            },
            'Group'         => function ($q) {
                $q->select('id', 'event_data');
            }
        ])->where('id', $channelId)->first();
    }

    public function getDistrictChannelByCount($abbr)
    {
        $groupIds     = GlobalPlatform::convertDistrictToGroupId($abbr);
        $districtInfo = GlobalPlatform::convertAbbrToDistrictInfo($abbr);
        // 全平台公開
        $publicTotal = DB::table('district_channel_contents')
            ->join('groups', 'district_channel_contents.groups_id', '=', 'groups.id')
            ->where('district_channel_contents.districts_id', $districtInfo->id);
//            ->count();

        // 全部影片
        $query = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', [1, 2])
            ->whereIn('group_channel_contents.content_public', [1, 0])
            ->whereIn('group_channel_contents.group_id', $groupIds);

        $contentIds = $publicTotal->pluck('content_id');


        $mapTba = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) {
                $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                    ->groupBy('tba_statistics.tba_id');
            }
        ])->orderBy('lecture_date', 'DESC');

        $doubleGreenLightTotal = $mapTba->get()->filter(function ($tba) {
            if ($tba->tbaStatistics->isNotEmpty()) {
                if ((int)$tba->tbaStatistics->first()->T >= 70 && (int)$tba->tbaStatistics->first()->P >= 70) {
                    return $tba->tbaStatistics;
                }
            }
        })->count();


        $result = [
            'public_total'           => number_format($publicTotal->count()),
            'hits_total'             => number_format((int)$mapTba->sum('hits')),
            'all_total'              => number_format($query->count()),
            'doubleGreenLight_total' => number_format($doubleGreenLightTotal)
        ];
        return $result;
    }

    /**
     * @param int $groupId
     * @return Collection
     */
    public function getGroupInfo($groupId)
    {
        return Group::query()->findOrFail($groupId);
    }


    //
    public function getTba($tbaId)
    {
        return Tba::query()->with('videos')->findOrFail($tbaId);
    }

    //
    public function setTba($tbaId, $tbaData)
    {
        $tba = Tba::findOrFail($tbaId);
        $tba->fill($tbaData);
        $tba->save();
    }

    //
    public function hitTba($tbaId)
    {
        $tba = Tba::findOrFail($tbaId);
        $tba->hits++;
        $tba->save();
    }

    //
    public function createTba($userId, $tba)
    {
        $tba['user_id'] = $userId;
        return Tba::create($tba);
    }

    //
    public function updateTba($tbaId, $tba)
    {
        return Tba::whereId($tbaId)->update($tba);
    }

    /*
     * anal event
     * */

    //
    public function getAnalEvent($eventId)
    {
        return TbaAnalysisEvent::findOrFail($eventId);
    }

    /*
     * eval event
     * */

    //
    public function getEvalEvent($eventId)
    {
        return TbaEvaluateEvent::findOrFail($eventId);
    }

    /**
     * Get tba eval events from tbaId as query
     * @param int $tbaId
     * @return EloquentBuilder
     */
    public function getEvalEventsByTbaIdAsQuery(int $tbaId): EloquentBuilder
    {
        return TbaEvaluateEvent::query()->where('tba_id', $tbaId);
    }

    /**
     * Create tba eval user from tba comment
     * @param object $commentData
     * @return TbaEvaluateUser
     */
    public function createEvalUserFromComment(object $commentData)
    {
        if (empty($commentData) || !$commentData->user_id) return null;

        return TbaEvaluateUser::firstOrCreate([
            'tba_id' => $commentData->tba_id,
            'user_id' => $commentData->user_id,
            'identity' => IdentityType::User,
        ]);
    }

    /**
     * Create Tba Eval Event from Tba Comment
     * @param object $commentData
     * @param object $tagData
     * @param object||null $evalUser
     * @return TbaEvaluateEvent
     */
    public function createEvalEventFromComment(object $commentData, object $tagData, $evalUser = null)
    {
        if (empty($commentData) || empty($tagData))
            return false;
            
        return TbaEvaluateEvent::create([
            'tba_id' => $commentData->tba_id,
            'tba_evaluate_user_id' => $evalUser
                ? $evalUser->id
                : null,
            'tba_evaluate_event_mode_id' => $tagData->is_positive // Please see TbaEvaluateEventModesTableSeeder
                ? 15 // identity -> U, event -> UgPos, mode -> UgAgree
                : 16, // identity -> U, event -> UgNeg, mode -> UgDisAgr
            'user_id' => $commentData->public
                ? null
                : $commentData->user_id, // public -> null, private -> user_id
            'group_id' => $commentData->group_id,
            'time_point' => $commentData->time_point,
            'tba_comment_id' => $commentData->id,
            'text' => $commentData->text,
        ]);
    }

    /**
     * Update Tba Eval Event from Tba Comment
     * @param int $commentId
     * @param string $text
     * @return TbaEvaluateEvent
     */
    public function updateEvalEventFromComment(int $commentId, string $text)
    {
        if (empty($commentId) || empty($text))
            return false;
        
        return TbaEvaluateEvent::where('tba_comment_id', '=', $commentId)
            ->update([
                'text' => $text,
            ]);
    }

    /**
     * @param int $groupChannelId
     * @param int $contentId
     * @return Tba[]|\Illuminate\Database\Eloquent\Builder[]|Collection
     */
    public function getTbaInfo(int $groupChannelId, int $contentId)
    {
        $contentId                 = GroupChannelContent::query()->where('group_channel_id', $groupChannelId)->where('content_id', $contentId)->pluck('content_id');
        $concernedStatisticalTypes = [49, 50, 53, 61, 52, 54, 31, 47, 48]; # In order based on Norm table appearance
        return Tba::query()->with([
            'tbaEvaluateEvents' => function ($q) {
                $q->with([
                    'tbaEvaluateEventMode'  => function ($q) {
                        $q->select('mode', 'event', 'id');
                    },
                    'tbaEvaluateUser'       => function ($q) {
                        $q->with([
                            'user' => function ($q) {
                                $q->select('name', 'id', 'habook');
                            }
                        ])->where('identity', '!=', 'G');
                    },
                    'tbaEvaluateEventFiles' => function ($q) {
                        $q->select('name', 'ext', 'image_url', 'tba_evaluate_event_id');
                    },
                ])->whereNotNull('tba_evaluate_user_id')->orderBy('time_point', 'ASC')->selectRaw("text,time_point, tba_id, tba_evaluate_event_mode_id, tba_evaluate_user_id, id");
            },
            'user'              => function ($q) {
                $q->select('name', 'id', 'habook');
            },
            'tbaAnnexs'         => function ($q) {
                $q->select('type', 'tba_id');
            },
            'groupChannels'     => function ($q) use ($groupChannelId) {
                $q->where('id', $groupChannelId);
            },
            'tbaStatistics'     => function ($q) use ($concernedStatisticalTypes) {
                $q->whereIn('type', $concernedStatisticalTypes)->distinct()->get();
            }
        ])->where('id', $contentId)->get();
    }

    /**
     * @param int $tba_id
     * @param int $channel_id
     * @param array $attributes
     * @return bool
     */
    public function createGroupChannelContent(int $tba_id, int $channel_id, array $attributes = []): bool
    {
        $isSuccessful = true;
        try {
            $morphToMany = $this->model->query()->find($tba_id)->groupChannels();
            // 判斷影片存不存在
            if ($morphToMany->where('group_channel_id', $channel_id)->exists()) {
                return true;
            }
            $morphToMany->attach($channel_id, $attributes);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }

        return $isSuccessful;
    }

    /**
     * @param int $tba_id
     * @param int $channel_id
     * @return bool
     */
    public function deleteGroupChannelContent(int $tba_id, int $channel_id): bool
    {

        $isSuccessful = true;
        try {
            $morphToMany = $this->model->query()->find($tba_id)->groupChannels();
            // 判斷影片存不存在
            if (!$morphToMany->where('group_channel_id', $channel_id)->exists()) {
                return false;
            }
            $morphToMany->detach($channel_id);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
        return $isSuccessful;
    }

    /**
     * Delete private group channel content
     * @param int $channelId
     * @param int $contentId
     * @return bool
     */
    public function deletePrivateGroupChannelContent(int $channelId, int $contentId): bool
    {
        $isSuccessful = false;
        try {
            $groupChannelContent = GroupChannelContent::query()
                ->where('group_channel_id', $channelId)
                ->where('content_id', $contentId)
                ->where('content_status', 2)
                ->where('content_public', 0)
                ->where('share_status', 1)
                ->first();

            if (!$groupChannelContent->exists())
                throw new \Exception('Group channel content not found');

            $isSuccessful = $this->deleteGroupChannelContent($contentId, $channelId);
            
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
        return $isSuccessful;
    }

    /**
     * 取得頻道內的 Video
     * @param int $channel_id
     * @param string $habook_id
     * @return GroupChannelContent[]|\Illuminate\Database\Eloquent\Builder[]|Collection|\Illuminate\Support\Collection|string
     */
    public function getShareVideos(int $channel_id, string $habook_id)
    {
        try {
            // Get shared content id list by user
            $contentIdList = $this->model->query()
                ->select('id')->with('groupChannels')
                ->where('habook_id', $habook_id)->get()
                ->filter(function ($q) use ($channel_id) {
                    if ($q->groupChannels()->where('id', $channel_id)->exists())
                        return $q;
                })
                ->pluck('id')
                ->toArray();

            // Get group_channel_content_id data list
            return GroupChannelContent::query()
                ->where('group_channel_id', $channel_id)
                ->whereIn('content_id', $contentIdList)
                ->get();

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return $exception->getMessage();
        }
    }
}

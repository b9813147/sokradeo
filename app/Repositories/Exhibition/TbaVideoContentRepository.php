<?php

namespace App\Repositories\Exhibition;

use App\Libraries\Lang\Lang;
use App\Models\Districts;
use App\Models\GroupChannelContent;
use Illuminate\Support\Facades\DB;
use App\Models\Tba;
use App\Models\User;
use App\Types\Cms\CmsType;
use App\Types\Exhibition\OrderType;
use App\Types\Tba\StatisticType;

class TbaVideoContentRepository extends ContentRepository
{
    private $contentType = CmsType::Tba;

    //
    public function __construct()
    {
        parent::__construct(CmsType::TbaVideo);
    }

    //
    public function listWithOrderType($page = 1, $perPage = null, $userId = null, $conds = [], $opts = [], $tbaFeatures = [], $order = null, $abbr = null)
    {
        $groupIds = null;
        if (!is_null($userId)) {
            $groupIds = User::findOrFail($userId)->groups()->where('status', 1)->select('id')->get()->map(function ($v) {
                return $v->id;
            })->toArray();
        }
        // 有傳學區簡碼學區篩選，會優先用學區頻道篩選
        if (!empty($abbr)) {
            $model    = Districts::query()->with('districtGroups')->where('abbr', $abbr)->first();
            $groupIds = $model->districtGroups->map(function ($v) {
                return $v->groups_id;
            })->toArray();
        }
        $lang       = new Lang();
        $locales_id = $lang->getConvertByLangString(\App::getLocale());
//        [63,97,64,96]

        /* 說明
         * $sql = '(group_channel_contents.content_public = 1 OR group_channel_contents.group_id IN ('.implode(',', $groupIds).'))';
         * */
        $sql = '(group_channel_contents.content_public = 1' . (!empty($groupIds) ? ' OR group_channel_contents.group_id IN (' . implode(',', $groupIds) . ')' : '') . ')';

        $query = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->join('group_subject_fields', 'group_channel_contents.group_id', '=', 'group_subject_fields.groups_id')
            ->where('content_type', $this->contentType)
            ->where('content_status', 1)
            ->where('content_public', 1)
            ->whereRaw($sql)
            ->where('groups.status', 1);
        //->join('tba_video', 'group_channel_contents.content_id', '=', 'tba_video.tba_id');

        $selects = ['content_id'];

        if (!empty($conds) || !empty($opts) || !is_null($order)) {
            $query->join('tbas', 'group_channel_contents.content_id', '=', 'tbas.id');
        }

        // conds
        foreach ($conds as $cond) {
            $query->where($cond['col'], $cond['op'], $cond['val']);
        }

        // opts
        if (!empty($opts)) {
            $query->where(function ($q) use ($opts) {
                foreach ($opts as $opt) {
                    $q->orWhere($opt['col'], $opt['op'], $opt['val']);
                }
            });
        }

        // tba features
        if (!empty($tbaFeatures)) {
            $query->join('tba_tba_feature', 'group_channel_contents.content_id', '=', 'tba_tba_feature.tba_id');
            $query->whereIn('tba_feature_id', $tbaFeatures);
        }

        // order type
        if (!is_null($order)) {

            switch ($order['type']) {
                case OrderType::Hits:
                    $query->orderBy('tbas.hits', $order['dir']);
                    array_push($selects, 'tbas.hits');
                    break;
                case OrderType::AddedTime:
                    // 定義:因為牽扯多群組多頻道審核通過上架, 所以無法判斷以何群組頻道為實際上架時間, 故先以cms新增時間為上架時間依據
                    $query->orderBy('tbas.created_at', $order['dir']);
                    array_push($selects, 'tbas.created_at');
                    break;
                case OrderType::TbaTechInteractIdx:
                    $query->join('tba_statistics', 'tbas.id', '=', 'tba_statistics.tba_id')
                        ->where('tba_statistics.type', StatisticType::TechDex)
                        ->orderBy('TbaStatIdx', $order['dir']);
                    array_push($selects, DB::raw('MAX(tba_statistics.idx) AS TbaStatIdx'));
                    break;
                case OrderType::TbaMethodAnal:
                    $query->join('tba_statistics', 'tbas.id', '=', 'tba_statistics.tba_id')
                        ->where('tba_statistics.type', StatisticType::PedaDex)
                        ->orderBy('TbaStatIdx', $order['dir']);
                    array_push($selects, DB::raw('MAX(tba_statistics.idx) AS TbaStatIdx'));
                    break;
            }
        }

        if (is_null($order)) {
            $query->select($selects)->distinct();
        } else {
            $query->select($selects)->groupBy('content_id');
        }

//        $paginate   = $query->paginate($perPage, ['*'], 'page', $page);
        $limit      = $query->limit(32);
        $contentIds = $limit->pluck('content_id');

        $mapTba = Tba::whereIn('id', $contentIds)->with([
            'user'                => function ($q) {
                $q->select('users.id', 'users.name');
            },
            'tbaPlaylistTracks'   => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')->orderBy('tba_playlist_tracks.list_order');
            },
            'groupChannels'       => function ($q) {
                $q->where('public', 1)->where('status', 1);
                $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description');
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
            }
        ])->orderBy('tbas.lecture_date', 'DESC');

        return $mapTba->paginate($perPage, ['*'], 'page', $page);
//        $paginate->transform(function ($item) use ($mapTba) {
//
//            $mapIdx = $mapTba->search(function ($tba) use ($item) {
//                return $item->content_id === $tba->id;
//            });
//
//            return $mapTba[$mapIdx];
//        });
//
//        return $paginate;
    }

    //
    public function listByContentIds($contentIds, $page = 1, $perPage = null, $orders = [])
    {
        $query = Tba::whereIn('id', $contentIds)->with([
            'user'              => function ($q) {
                $q->select('users.id', 'users.name');
            },
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')->orderBy('tba_playlist_tracks.list_order');
            },
            'groupChannels'     => function ($q) {
                $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description');
            },
        ]);

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $query->orderBy($order['col'], $order['dir']);
            }
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * @param $contentIds
     * @param int $page
     * @param null $perPage
     * @param null $order
     * @return mixed
     */
    public function listByContentIdsWithOrderType($contentIds, $page = 1, $perPage = null, $order = null)
    {
        $query = Tba::whereIn('tbas.id', $contentIds)->with([
            'user'              => function ($q) {
                $q->select('users.id', 'users.name');
            },
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')->orderBy('tba_playlist_tracks.list_order');
            },
            'groupChannels'     => function ($q) {
                $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description');
            },
        ]);

        switch ($order['type']) {
            case OrderType::Hits:
                $query->orderBy('tbas.hits', $order['dir']);
                break;
            case OrderType::AddedTime:
                // 定義:因為牽扯多群組多頻道審核通過上架, 所以無法判斷以何群組頻道為實際上架時間, 故先以cms新增時間為上架時間依據
                $query->orderBy('tbas.created_at', $order['dir']);
                break;
            case OrderType::TbaTechInteractIdx:
                $query->join('tba_statistics', 'tbas.id', '=', 'tba_statistics.tba_id')
                    ->where('tba_statistics.type', StatisticType::TechDex)
                    ->orderBy('tba_statistics.idx', $order['dir']);
                break;
            case OrderType::TbaMethodAnal:
                $query->join('tba_statistics', 'tbas.id', '=', 'tba_statistics.tba_id')
                    ->where('tba_statistics.type', StatisticType::PedaDex)
                    ->orderBy('tba_statistics.idx', $order['dir']);
                break;
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    //
    public function getContentsByIds($contentIds, $orders = [])
    {
        $query = Tba::whereIn('id', $contentIds)->with([
            'user'                => function ($q) {
                $q->select('users.id', 'users.name');
            },
            'tbaPlaylistTracks'   => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')->orderBy('tba_playlist_tracks.list_order');
            },
            'groupChannels'       => function ($q) {
                $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description')->where(['content_status' => 1, 'content_public' => 1]);
            },
            'tbaStatistics'       => function ($q) {
                $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                    ->groupBy('tba_statistics.tba_id');
            },
            'groupChannelContent' => function ($q) {
                $q->select('content_id', 'group_subject_fields_id', 'ratings_id', 'grades_id')->with([
                    'groupSubjectFields' => function ($q) {
                        $q->selectRaw("alias as subject, id");
                    },
                    'groupRatingFields'  => function ($q) {
                        $q->select('name', 'id');
                    },
                    'grade'              => function ($q) {
                        $q->where('locales_id', Lang::getConvertByLangString(\App::getLocale()))->select('name', 'grades_id');
                    }
                ]);
            },
            'videos' => function ($q) {
                // Get videos -> resource -> vods to find rdata for video duration display
                $q->select('resource_id');
                $q->with(['resource' => function ($q) {
                    $q->select('id');
                    $q->with(['vod' => function ($q) {
                        $q->select('resource_id', 'rdata');
                    }]);
                }]);
            },
        ]);

        if (!empty($orders)) {
            foreach ($orders as $order) {
                $query->orderBy($order['col'], $order['dir']);
            }
        }

        return $query->get();
    }

    //
    public function getPageContentIds($page = 1, $perPage = null, $userId = null, $orders = [], $conds = [], $opts = [], $tbaFeatures = [])
    {
        $groupIds = null;
        if (!is_null($userId)) {
            $groupIds = User::findOrFail($userId)->groups()->where('status', 1)->select('id')->get()->map(function ($v) {
                return $v->id;
            })->toArray();
        }

        /* 說明
         * $sql = '(group_channel_contents.content_public = 1 OR group_channel_contents.group_id IN ('.implode(',', $groupIds).'))';
         * */
        $sql   = '(group_channel_contents.content_public = 1' . (!empty($groupIds) ? ' OR group_channel_contents.group_id IN (' . implode(',', $groupIds) . ')' : '') . ')';
        $query = DB::table('group_channel_contents')
            ->where('content_type', $this->contentType)
            ->where('content_status', 1)
            ->whereRaw($sql)
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('groups.status', 1);
        //->join('tba_video', 'group_channel_contents.content_id', '=', 'tba_video.tba_id');

        $selects = ['content_id'];

        if (!empty($orders) || !empty($conds) || !empty($opts)) {

            $query->join('tbas', 'group_channel_contents.content_id', '=', 'tbas.id');

            foreach ($orders as $order) {
                $query->orderBy($order['col'], $order['dir']);
                array_push($selects, $order['col']);
            }

            foreach ($conds as $cond) {
                $query->where($cond['col'], $cond['op'], $cond['val']);
            }

            if (!empty($opts)) {
                $query->where(function ($q) use ($opts) {
                    foreach ($opts as $opt) {
                        $q->orWhere($opt['col'], $opt['op'], $opt['val']);
                    }
                });
            }
        }

        if (!empty($tbaFeatures)) {
            $query->join('tba_tba_feature', 'group_channel_contents.content_id', '=', 'tba_tba_feature.tba_id');
            $query->whereIn('tba_feature_id', $tbaFeatures);
        }

        if (empty($orders)) {
            $query->select($selects)->distinct();
        } else {
            $query->select($selects)->groupBy('content_id');
        }

        return $query->offset($perPage * ($page - 1))->limit($perPage)->get()->map(function ($v) {
            return $v->content_id;
        });
    }

    //
    public function getRankContentIds($limit = 1, $orders = [], $groupIds = null)
    {
        $query = DB::table('group_channel_contents')
            ->where('content_type', $this->contentType)
            ->where('content_status', 1)
            ->where('content_public', 1)
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('groups.status', 1)
            //->join('tba_video', 'group_channel_contents.content_id', '=', 'tba_video.tba_id')
            ->join('tbas', 'group_channel_contents.content_id', '=', 'tbas.id');

        if ($groupIds) {
            $query->whereIn('groups.id', $groupIds);
        }

        $selects = ['content_id'];

        foreach ($orders as $order) {
            $query->orderBy($order['col'], $order['dir']);
            array_push($selects, $order['col']);
        }

        return $query->select($selects)->groupBy('content_id')->limit($limit)->get()->map(function ($v) {
            return $v->content_id;
        });
    }

    /**
     * @param $contentId
     * @return array
     */
    public function getContent($contentId): array
    {
        $tba = Tba::with([
            'user'              => function ($q) {
                $q->select('users.id', 'users.name', 'users.habook');
            },
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')->orderBy('tba_playlist_tracks.list_order');
            },
            'subjectField'      => function ($q) {
                $q->select('subject_fields.id', 'subject_fields.type');
            },
            'educationalStage'  => function ($q) {
                $q->select('educational_stages.id', 'educational_stages.type');
            },
            'locale'            => function ($q) {
                $q->select('locales.id', 'locales.type');
            },
            'tbaFavorites'     => function ($q) {
                $q->select('tba_favorites.user_id', 'tba_favorites.channel_id', 'tba_favorites.group_id', 'tba_favorites.tba_id');
            },
        ])->findOrFail($contentId);

        return [
            'tba' => $tba,
        ];
    }

//    影片詳細資料
    public function getContentInfo($contentId, $groupId)
    {
        $content = GroupChannelContent::query()
            ->select('content_status', 'grades_id', 'ratings_id')
            ->where('content_id', $contentId)
            ->where('group_id', $groupId)
            ->first();
        return $content;
    }

    // Update content info tba
    public function setContentInfo(string $contentId, string $groupId, array $contentData): ?bool
    {
        $isSuccessful = true;
        $tbaData      = [
            'name'              => $contentData['title'],
            'description'       => $contentData['desc'],
            'course_core'       => $contentData['courseCore'],
            'observation_focus' => $contentData['observationFocus']
        ];
        if ($contentData['thumbnail']) {
            $tbaData['thumbnail'] = $contentData['thumbnail'];
        }

        try {
            // Update tba
            DB::table('tbas')
                ->where('id', $contentId)
                ->where('habook_id', $contentData['habookId'])
                ->update($tbaData);

            // Update subject and grade
            DB::table('group_channel_contents')
                ->where('group_id', $groupId)
                ->where('content_id', $contentId)
                ->update([
                    'group_subject_fields_id' => $contentData['subjectId'],
                    'grades_id'               => $contentData['grade']
                ]);
        } catch (Exception $e) {
            $isSuccessful = false;
        }
        return $isSuccessful;
    }

    // Update content info annex
    public function setContentInfoAnnex(string $contentId, string $groupId, string $userId, object $contentDataAnnex)
    {
        // To be written
    }
}

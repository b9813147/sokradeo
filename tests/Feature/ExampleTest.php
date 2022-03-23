<?php

namespace Tests\Feature;

use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\GroupSubjectFields;
use App\Models\Tba;
use App\Services\Cms\TbaService;
use App\Types\Cms\CmsType;
use App\Types\Tba\AnnexType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use function React\Promise\map;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $content_status = [1];
        $content_public = [1];
        $channel        = 1;
        $group_status   = 1;

        // 公開
        $publicTotal = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', $content_status)
            ->whereIn('group_channel_contents.content_public', $content_public)
            ->where('group_channel_contents.group_channel_id', $channel)
            ->where('groups.status', $group_status)->count();

        // total
        $query = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', [1, 2])
            ->whereIn('group_channel_contents.content_public', [1, 0])
            ->where('group_channel_contents.group_channel_id', $channel);

        $contentIds = $query->pluck('content_id');

        $mapTba = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
        ])->orderBy('lecture_date', 'DESC');


        $result = [
            'public_total' => $publicTotal,
            'hits_total'   => (int)$mapTba->sum('hits'),
            'all_total'    => $mapTba->count()
        ];

        dd($result);
//        dd($publicTotal);
//        $response->assertStatus(200);
    }

    public function testGetMyGroupFilter()
    {
        $t       = 0;
        $builder = GroupChannel::query()->where('id', 2131231232132)->first();

        dd($builder instanceof GroupChannel);

        $content_status = [1];
        $content_public = [1];
        $channel        = 1;
        $group_status   = 1;
        $sql            = collect();
        $resultFilter   = collect(["eduStages" => null, "grades" => null, "subjectFields" => 1, "lectureTypes" => null, "tbaFeatures" => null, "years" => null, "search" => null]);
        $resultFilter->each(function ($v, $k) use (&$sql, &$sqlYear) {
            if ($k === 'eduStages') {
                $k = 'educational_stage_id';
            }
            if ($k === 'grades') {
                $k = 'grade';
            }
            if ($k === 'subjectFields') {
                $k = 'subject_field_id';
            }
            if ($k === 'lectureTypes') {
                $k = 'lecture_type';
            }
            if ($k === 'years') {
                $k = 'lecture_date';
            }

            if ($k === 'tbaFeatures') {
                $k = 'tba_feature_id';
            }
            $sql->put($k, $v);
        });
//        dd($resultFilter,$sql);
        $query = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', $content_status)
            ->whereIn('group_channel_contents.content_public', $content_public)
            ->where('group_channel_contents.group_channel_id', $channel)
            ->where('groups.status', $group_status);

        $contentIds = $query->pluck('content_id');


        $mapTba = Tba::query()->whereIn('id', $contentIds)
            ->with([
                'user'              => function ($q) {
                    $q->select('users.id', 'users.name');
                },
                'tbaPlaylistTracks' => function ($q) {
                    $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                        ->orderBy('tba_playlist_tracks.list_order');
                },
                'groupChannels'     => function ($q) {
                    $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description')
                        ->orderBy('group_channels.created_at', 'ASC');
                },
            ])->orderBy('lecture_date', 'DESC');

        if ($sql->has('search')) {
            $search = $sql->pull('search');
            $mapTba->where('name', 'like', "%$search%");
        }
        if ($sql->has('lecture_date')) {
            $mapTba->whereYear('lecture_date', $sql->pull('lecture_date'));
        }
        // tba features

        $query->join('tba_tba_feature', 'group_channel_contents.content_id', '=', 'tba_tba_feature.tba_id')->select('tba_feature_id')->first();

        if ($sql->isNotEmpty()) {
            $mapTba->where($sql->toArray());
        }
    }

    public function testGroupChannelContent()
    {
        $content_status = [1];
        $content_public = [1, 0];
        $channel        = 7;
        $group_status   = 1;
        $query          = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', $content_status)
            ->whereIn('group_channel_contents.content_public', $content_public)
            ->where('group_channel_contents.group_channel_id', $channel)
            ->where('groups.status', $group_status);

        $contentIds = $query->pluck('content_id');

        $mapTba = Tba::query()->whereIn('id', $contentIds)
            ->with([
                'user'                => function ($q) {
                    $q->select('users.id', 'users.name');
                },
                'tbaPlaylistTracks'   => function ($q) {
                    $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                        ->orderBy('tba_playlist_tracks.list_order');
                },
                'groupChannels'       => function ($q) use ($channel) {
                    $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description')
                        ->where('group_channels.id', $channel)
                        ->orderBy('group_channels.created_at', 'ASC');
                },
                'tbaStatistics'       => function ($q) {
                    $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id,MAX(CASE WHEN type = 55 THEN CAST(idx AS signed) ELSE 0 END) AS C')
                        ->groupBy('tba_statistics.tba_id');
                },
                'groupChannelContent' => function ($q) {
                    $q->select('content_id', 'group_subject_fields_id', 'ratings_id', 'grades_id')->with([
                        'groupSubjectFields'   => function ($q) {
                            $q->select('subject', 'id');
                        }, 'groupRatingFields' => function ($q) {
                            $q->select('name', 'id');
                        }
                    ]);
                }
            ])->orderBy('tbas.lecture_date', 'DESC')->orderBy('tbas.created_at', 'DESC')->paginate(15);
//        $builders = GroupChannelContent::with('groupSubjectFields', 'groupRatingFields')->where('group_id', 7)->get();
//        $builders->push($mapTba);
//        $mapTba->push($builders);
//        $mapTba->push($builders);
//        dd($mapTba->push($builders));

        dd($mapTba->toArray());
    }

    public function testData()
    {
        $tbaService    = app(TbaService::class);
        $groupChannels = $tbaService->getGroupChannel(131, 948, false);

        $result = [];
        $groupChannels->each(function ($v, $key) use (&$result) {
            $lessonPlan = $v->tbaAnnexs->filter(function ($tbaAnnex) {
                return $tbaAnnex->type === AnnexType::LessonPlan;
            });
            $material   = $v->tbaAnnexs->filter(function ($tbaAnnex) {
                return $tbaAnnex->type === AnnexType::Material;
            });

            $result[] = [
                'id'           => $v->id,
                'key'          => $key + 1,
                'status'       => $v->groupChannels->first()->pivot->content_public === 1 ? 'V' : '',
                'doubleGreen'  => $v->tbaStatistics->isNotEmpty() ? $v->tbaStatistics->first()->T >= 70 && $v->tbaStatistics->first()->P >= 70 ? 'V' : '' : '',
                'channel_name' => $v->name,
                'lecture_date' => $v->lecture_date,
                'teacher'      => $v->teacher,
                'rating'       => $v->groupChannelContent->first()->groupRatingFields->isNotEmpty() ? $v->groupChannelContent->first()->groupRatingFields->first()->name : 'x',
                'subject'      => $v->groupChannelContent->first()->groupSubjectFields->isNotEmpty() ? $v->groupChannelContent->first()->groupSubjectFields->first()->subject : __('app/subject-field.Other'),
                'grade'        => $v->groupChannelContent->first()->grades_id,
                'lessonPlan'   => $lessonPlan->isNotEmpty() ? 'V' : '',
                'material'     => $material->isNotEmpty() ? 'V' : '',
                't'            => $v->tbaStatistics->isNotEmpty() ? (string)$v->tbaStatistics->first()->T : (string)0,
                'p'            => $v->tbaStatistics->isNotEmpty() ? (string)$v->tbaStatistics->first()->P : (string)0,
                'c'            => $v->tbaStatistics->isNotEmpty() ? (string)$v->tbaStatistics->first()->C > (string)0 ? $v->tbaStatistics->first()->C : '' : '',
                'event_total'  => $v->tbaEvaluateEvents->isNotEmpty() ? (string)$v->tbaEvaluateEvents->first()->total : (string)0,
            ];
        });

        dd($result);
    }

    public function testAlias()
    {
        $groupSubjectFields = GroupSubjectFields::all();
        $groupSubjectFields->each(function ($q, $k) {
            $q->order = $k + 1;
            $q->save();
        });

    }

    public function testAuth()
    {
        auth()->loginUsingId(948);
        $check = auth()->check();
        dd($check);
    }

    public function testApi()
    {
        $name = '';
        $var  = GroupChannelContent::with('groupSubjectFields')->select('content_id')->where(['content_status' => 1, 'content_public' => 1])->get()->take(100);
        dd($var->toArray());

        $groupChannelContents = GroupChannelContent::query()->with(['tbas', 'groupSubjectFields'])->where(['content_status' => 1, 'content_public' => 1])
            ->orWhere('name', 'like', "%$name%")
            ->orWhere('teacher', 'like', "%$name%")
            ->orWhere('subject', 'like', "%$name%")
            ->orWhere('channelName', 'like', "%$name%")
            ->get();

        dd($groupChannelContents);

    }

    public function testDiff()
    {
        $p2 = collect([15345, 15350, 15353, 15356, 15359, 15360, 15361, 15363, 15362, 15043, 15368, 15370, 15371, 15373, 15375, 15376, 15367, 15364, 15623, 15624, 15625, 15626, 15627, 15635, 15643, 15653, 15652, 15648, 15665, 15666, 15667, 15664, 15661, 15659, 15668, 15690, 15706]);
        $p  = [15376, 15375, 15371, 15373, 15370, 15368, 15367, 15364, 15363, 15362, 15361, 15360, 15359, 15356, 15353, 15350, 15345, 15043, 15623, 15624, 15625, 15627, 15626, 15643, 15635];
        dd($p2->diff($p)->all());
    }

    public function testExhibit()
    {
        $cmsType     = CmsType::TbaVideo;
        $contentType = ($cmsType === CmsType::TbaVideo) ? CmsType::Tba : $cmsType;
        $select      = [
            'group_channels.id', 'group_channels.name', 'group_channels.description', 'group_channels.thumbnail', 'group_channel_contents.content_public',
            'sum(IF(group_channel_contents.content_public = 1, 1, 0)) as total_content',
            'sum(IF(group_channel_contents.content_public = 1 or group_channel_contents.content_public = 0, 1, 0)) as total_content_all'
        ];
        $select      = DB::raw(implode(',', $select));
        // 公開頻道
        return DB::table('group_channels')
            ->join('groups', 'groups.id', '=', 'group_channels.group_id')
            ->where('group_channels.cms_type', $cmsType)
            ->where('group_channels.status', 1)
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
}











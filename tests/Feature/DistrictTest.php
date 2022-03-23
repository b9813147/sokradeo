<?php

namespace Tests\Feature;

use App\Helpers\Custom\GlobalPlatform;
use App\Models\DistrictChannelContent;
use App\Models\DistrictGroupSubject;
use App\Models\Districts;
use App\Models\DistrictSubject;
use App\Models\DistrictSubjectField;
use App\Models\GradeLang;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\GroupChannelContent;
use App\Models\GroupLang;
use App\Models\GroupSubjectFields;
use App\Models\Rating;
use App\Models\SubjectField;
use App\Models\Tba;
use App\Models\TbaStatistic;
use App\Repositories\DistrictRepository;
use App\Services\Exhibition\ExhibitionService;
use App\Services\GradeService;
use App\Services\Habook\ApiService;
use App\Types\Cms\CmsType;
use App\Types\Group\DutyType;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DistrictTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $test = [];


        $districtRep = app(DistrictRepository::class);
//        dd($districtRep->getDistrictCount());

        $exhibition = app(ExhibitionService::class);
//        dd($exhibition->getCmsSetsByDistrict('TbaVideo','top')->toArray());
//        Districts::with('districtLang', 'districtGroups')
//            ->where('abbr', $abbr)
//            ->get();

        $abbr = 'HKTEVENT';
        $lang = 'tw';

        $builders = Districts::query()
            ->select('district_langs.lang', 'district_langs.name', 'district_langs.description', 'district_groups.group_id')
            ->join('district_groups', 'districts.id', 'district_groups.district_id')
            ->join('district_langs', 'districts.id', 'district_langs.district_id')
            ->where('abbr', $abbr)
            ->where('district_langs.lang', $lang)
            ->get();


        $this->assertTrue(true);
    }

    public function testDistrictData()
    {
        $abbr      = 'HKTEVENT';
        $lang      = 'tw';
        $districts = Districts::with([
            'districtLang'   => function ($q) use ($lang) {
                $q->where('lang', $lang);
            },
            'districtGroups' => function ($q) {

            }
        ])
            ->where('abbr', $abbr)->first();

        $districts->districtGroups->map(function ($item) use (&$groups) {
            array_push($groups, $item->group_id);
        });

        $data                = [];
        $groups              = [];
        $data['name']        = $districts->districtLang->first()->name;
        $data['description'] = $districts->districtLang->first()->description;
        $data['groupId']     = $groups;

        dd($data);
    }

    /**
     * 學區頻道 統計用
     * @param $groupIds
     * @return array
     */
    public function testGetChannelByCount()
    {
        $abbr = 'HKTEVENT';

        $lang     = 'tw';
        $groupIds = [];
        //影片狀態
        $content_status = [1];
        // 發布狀態
        $content_public = [1];
        $group_status   = 1;

        $districts = Districts::with([
            'districtLang'   => function ($q) use ($lang) {
                $q->where('lang', $lang);
            },
            'districtGroups' => function ($q) {
                $q->select('id', 'list_order');
            }
        ])
            ->where('abbr', $abbr)->first();

        $districts->districtGroups->map(function ($item) use (&$groupIds) {
            array_push($groupIds, $item->group_id);
        });
//        dd($districts->toArray());

        // 公開
        $publicTotal = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', $content_status)
            ->whereIn('group_channel_contents.content_public', $content_public)
            ->where('group_channel_contents.group_channel_id', $groupIds)
            ->whereIn('groups.id', $groupIds)
            ->where('groups.status', $group_status)->count();

        // total
        $query = DB::table('group_channel_contents')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', [1, 2])
            ->whereIn('group_channel_contents.content_public', [1, 0])
            ->where('groups.id', $groupIds);

        $contentIds = $query->pluck('content_id');

        $mapTba = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
        ])->orderBy('lecture_date', 'DESC');


        $result = [
            'name'         => $districts->districtLang->first()->name,
            'description'  => $districts->districtLang->first()->description,
            'public_total' => $publicTotal,
            'hits_total'   => (int)$mapTba->sum('hits'),
            'all_total'    => $mapTba->count()
        ];

        dd($result);
//        return $result;
    }

    public function testApi()
    {
        // 學區
        $district = Districts::with('districtGroups', 'districtSubjectField')->first();
        $lang     = $district->districtSubjectField;
//        dd($lang->toArray());
        $groupIds = $district->districtGroups->map(function ($q) {
            return $result[] = $q->group_id;
        });
        // 頻道群組
//        $groups = $districts->districtGroups->map(function ($q) {
//            return $result[] = $q->group_id;
//        });
        // 頻道內容ID
        $contentIds = GroupChannelContent::query()->select('content_id')->whereIn('group_id', $groupIds)->get()->map(function ($q) {
            return $result[] = $q->content_id;
        });
        // 原始檔案
//        $tbaIds              = $groupChannelContent->map(function ($q) {
//            return $result[] = $q->content_id;
//        });

        $subjectFields         = SubjectField::query()->where('');
        $districtSubjectFields = DistrictSubjectField::all();

//        $tbaContent  = Tba::query()->whereIn('id', $contentIds);
//        $mathematics = Tba::query()->whereIn('id', $contentIds)->where('subject_field_id', 2)->count();
//        $mandarin    = Tba::query()->whereIn('id', $contentIds)->where('subject_field_id', 1)->count();
//        $other       = Tba::query()->whereIn('id', $contentIds)->where('subject_field_id', 4)->count();
//        $english     = Tba::query()->whereIn('id', $contentIds)->where('subject_field_id', 3)->count();

//        dd($mathematics, $mandarin, $other, $english, $tbaContent->select('subject_field_id')->get()->toArray());

        /*$application = app(ExhibitionService::class);
        \Lang::setLocale('tw');
        $collection = collect(\Lang::get('app/grade'))->map(function ($v, $k) {
            return ['type' => $k, 'value' => $k, 'text' => $v];
        });
        dd($collection, \Lang::get('app/grade'));
        dd($application->getGradeFilter());*/
    }

    public function testLang()
    {
        $lang        = new \App\Libraries\Lang\Lang();
        $locales_id  = $lang->getConvertByLangString(\App::getLocale());
        $application = app(GradeService::class);
//        $districts_id = $this->districtRepository->firstBy('abbr', $abbr)->id ?? null;
        $gradeFieldsServices = $application->getBy('locales_id', $locales_id);
        dd($gradeFieldsServices->toArray());
    }

    // 批次處理 GroupChannelContent
    public function testStatistics()
    {
        $groups = Group::all();
//
        foreach ($groups as $group) {
            $query = DB::table('group_channel_contents')
                ->where('content_type', 'Tba')
                ->where('content_status', 1)
                ->where('content_public', 1)
                ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
                ->where('groups.status', 1)
                ->join('tbas', 'group_channel_contents.content_id', '=', 'tbas.id')
                ->where('groups.id', $group->id);


            $selects            = ['content_id'];
            $contentIds         = $query->select($selects)->groupBy(['content_id'])->get()->map(function ($v) {
                return $v->content_id;
            });
            $tbaSelect          = ['id', 'subject', 'grade', 'educational_stage_id'];
            $tbas               = Tba::query()->select($tbaSelect)->whereIn('id', $contentIds)->get();
            $GroupSubjectFields = GroupSubjectFields::where('groups_id', $group->id)->get();

            $tbas->each(function ($tba) use ($group, $GroupSubjectFields) {
                $GroupSubjectFields->each(function ($q) use ($tba, $group) {
                    if ($q->subject === $tba->subject) {
                        GroupChannelContent::query()->where([
                            'content_id' => $tba->id,
                            'group_id'   => $group->id
                        ])->update([
                            'group_subject_fields_id' => $q->id,
                            'grades_id'               => GlobalPlatform::convertGrade($tba->educational_stage_id, $tba->grade)
                        ]);
                    }
                });
//                echo $tba->educational_stage_id . '/' . $tba->grade . "\n";
            });
        }
    }


    public function testSql()
    {
        $lang         = 37;
        $districtLang = 'en';
        $abbr         = 'CDHT';

        $districts     = Districts::with([
            'districtLang' => function ($q) use ($lang) {
                return $q->where('locales_id', $lang);
            },
            'districtTbaInfo'
        ])->where('abbr', $abbr)->first();
        $subjectFields = SubjectField::all();

        $test          = $subjectFields->flatMap(function ($q) use ($districts) {
            return [$q->type => $this->getSubjectCount($districts, $q->id)];
        })->toArray();
        $test['Other'] = $this->getSubjectCount($districts, 0);

        $result = [
            'Language'           => $this->getSubjectCount($districts, 1),
            'Mathematics'        => $this->getSubjectCount($districts, 2),
            'Social-Humanities'  => $this->getSubjectCount($districts, 3),
            'Science-Technology' => $this->getSubjectCount($districts, 4),
            'Arts'               => $this->getSubjectCount($districts, 5),
            'Physical'           => $this->getSubjectCount($districts, 6),
            'Comprehensive'      => $this->getSubjectCount($districts, 7),
            'Technology'         => $this->getSubjectCount($districts, 8),
            'Other'              => $this->getSubjectCount($districts, 0),
        ];
        dd($result);
    }


    /**
     * 統計學科
     * @param object $districts
     * @param int $subject_fields_id
     * @return mixed
     */
    private function getSubjectCount($districts, $subject_fields_id)
    {
        return $districts->districtTbaInfo->filter(function ($q) use ($subject_fields_id) {
            return $q->subject_fields_id == $subject_fields_id;
        })->count();
    }

    public function testGetGroupChannelSetsByDistrict($cmsType = 'TbaVideo', $setType = 'Excellent', $groupIds = [25, 26, 29, 218, 452, 40, 647, 306, 524, 525, 648, 649, 650, 651, 652])
    {
        $contentType = ($cmsType === CmsType::TbaVideo) ? CmsType::Tba : $cmsType;

        $select = ['MAX(exhibition_group_channel_sets.order) AS order_set', 'group_channels.id', 'group_channels.name', 'group_channels.description', 'group_channels.thumbnail'];
        $select = DB::raw(implode(',', $select));

        $exhibition_group_channel_sets = DB::table('exhibition_group_channel_sets')
            ->where('type', $setType)
            ->join('group_channels', 'exhibition_group_channel_sets.group_channel_id', '=', 'group_channels.id')
            ->where('cms_type', $cmsType)
            ->where('status', 1)
            ->select($select)
            ->groupBy('group_channels.id')
            ->whereIn('group_channels.group_id', $groupIds)
            ->orderBy('order_set', 'asc')->get();

        // 公開頻道
        $total_public = GroupChannelContent::query()
            ->selectRaw('COUNT(content_id) AS total_content ,group_channel_id')
            ->whereIn('group_channel_id', $exhibition_group_channel_sets->pluck('id'))
            ->where('content_type', $contentType)
            ->where('content_status', 1)
            ->where('content_public', 1)
            ->groupBy('group_channel_id')
            ->get();


        // 不分公開與不公開
        $total_all = GroupChannelContent::query()
            ->selectRaw('COUNT(content_id) AS total_content_all ,group_channel_id')
            ->whereIn('group_channel_id', $exhibition_group_channel_sets->pluck('id'))
            ->where('content_type', $contentType)
            ->whereIn('content_status', [1, 2])
            ->whereIn('content_public', [0, 1, 2])
            ->groupBy('group_channel_id')
            ->get();

        $result = $exhibition_group_channel_sets->map(function ($q) use ($total_public) {
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
        dd($result);
        // 計算全部狀態的總數
        $data2 = DB::table('exhibition_group_channel_sets')
            ->where('type', $setType)
            ->join('group_channels', 'exhibition_group_channel_sets.group_channel_id', '=', 'group_channels.id')
            ->where('cms_type', $cmsType)
            ->where('status', 1)
//            ->where('public', 1)
            ->join('group_channel_contents', 'exhibition_group_channel_sets.group_channel_id', '=', 'group_channel_contents.group_channel_id')
            ->where('content_type', $contentType)
            ->whereIn('content_status', [1, 2])
            ->whereIn('content_public', [0, 1, 2])
            ->selectRaw("COUNT(group_channel_contents.content_id) AS total_content_all,group_channels.id")
            ->whereIn('group_channels.group_id', $groupIds)
            ->groupBy('group_channels.id')
            ->get();


        return $data1->each(function ($q) use ($data2) {
            foreach ($data2 as $item) {
                if ($item->id === $q->id) {
                    $q->total_content_all = $item->total_content_all;
                }
            }
        });
    }


    // 批次處理 GroupLang
    public function testCrateGroupLang()
    {
        $json_decode = json_decode(file_get_contents(public_path('group_lang_cn.json')), true);
        foreach ($json_decode as $item) {
            GroupLang::query()->create($item);
        }
    }

    // 批次處理 GroupSubject
    public function testCrateGroupSubject()
    {
        $json_decode = json_decode(file_get_contents(public_path('group_lang_global.json')), true);
        foreach ($json_decode as $item) {
            GroupLang::query()->create($item);
        }
    }

    public function testGroupSubjectField()
    {
        $select     = "count(subject_fields_id), subject";
        $collection = DB::table('group_subject_fields')
            ->selectRaw($select)
            ->where('groups_id', 6)
            ->groupBy('subject_fields_id')
            ->get();
        dd($collection);
//       dd(GroupSubjectFields::selectRaw('subject_fields_id','subject')->where('groups_id', 6)->get());
    }

    public function testAA()
    {
        $tbaQueryBuilder = Tba::paginate(15);
        dd($tbaQueryBuilder->toArray());

    }

    public function testDoubleGreenLight()
    {
        $tbaStatistic = TbaStatistic::first();
        $contentIds   = [
            13, 14, 15, 16, 17, 18, 19, 39, 40, 2276, 2278, 2280, 2371, 2373, 2374, 2375, 2535, 2536, 2538, 2539, 2540, 2545, 2587, 2588, 2604, 2605, 2606,
            2607, 2608, 2616, 2618, 2619, 2621, 2625, 2641, 2705, 2752, 2760, 2812, 2819, 2820, 2898, 2899, 2900, 3033, 3054, 3091, 3143, 3146, 3160, 3171,
            3172, 3173, 3174, 3178, 3179, 3180, 3181, 3186, 3194, 3195, 3197, 3198, 3250, 3251, 3256, 3283, 3287, 3291, 3292, 3295, 3298, 3302, 3306, 3308,
            3309, 3312, 3440, 3483, 3487, 3490, 3491, 3498, 3547, 3548, 3563, 3566, 3567, 3583, 3585, 3615, 3616, 3617, 3618, 3619, 3633, 3635, 3636, 3637,
            3638, 3639, 3642, 3748, 3850, 3851, 3852, 3853, 3854, 3855, 3871, 3876, 3912, 3974, 4004, 4005, 4033, 4034, 4040, 4047, 4048, 4060, 4070, 4071,
            4073, 4094, 4096, 4112, 4113, 4116, 4122, 4123, 4127, 4173, 4179, 4205, 4208, 4218, 4219, 4220, 4221, 4225, 4233, 4236, 4251, 4254, 4256, 4261,
            4264, 4265, 4266, 4363, 4366, 4367, 4368, 4720, 4721, 4734, 4735, 4738, 4743, 4745, 4764, 4788, 4804, 4805, 4806, 4807, 4808, 4835, 4845, 4846,
            4375, 1371, 1295, 1290, 150, 149, 4855, 4868, 4874, 4896, 4902, 4903, 4906, 4912, 4913, 4918, 4942, 4943, 4944, 4945, 4950, 4951, 4961, 4962, 4964,
            4967, 4970, 4977, 4998, 5014, 5018, 5023, 5024, 5027, 5030, 5034, 5037, 5038, 5039, 5040, 5041, 5042, 5045, 5051, 5052, 5056, 5057, 5059, 5060, 5063,
            5070, 5071, 5076, 5077, 5078, 5079, 5080, 5081, 5083, 5085, 5086, 5088, 5089, 5091, 5095, 5096, 5097, 5098, 5103, 5108, 5109, 5110, 5111, 5112, 5113, 5115,
            5117, 5122, 5123, 5124, 5132, 5137, 5138, 5139, 5349, 5351, 5352, 5371, 5393, 5394, 5395, 5408, 5457, 5458, 5460, 5483, 5498, 5536, 5553, 5591, 5592, 5596,
            5636, 5652, 5653, 5654, 5655, 5658, 5673, 5674, 5678, 5681, 5700, 5702, 5825, 5862, 5864, 5903, 5911, 6059, 6087, 6088, 6089, 6092, 6109, 6137, 6138, 6171,
            6181, 6410, 6488, 6542, 6543, 6544, 6546, 6547, 6552, 6589, 6780, 6781, 6829, 6830, 6846, 6852, 6854, 6855, 6856, 6857, 6859, 6862, 6863, 6864, 6865, 6866,
            6867, 6868, 6869, 6870, 6871, 6872, 6873, 6878, 6879, 6880, 6881, 6886, 6887, 6888, 6889, 6890, 6898, 6899, 6901, 6902, 6903, 6928, 6938, 6939, 6940, 6949,
            6951, 6952, 6953, 6958, 6963, 6964, 6969, 6985
        ];


        $mapTba = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) {
                $q->select('tba_statistics.tba_id', 'tba_statistics.type', 'tba_statistics.idx')->whereIn('tba_statistics.type', [47, 48,]);
            }
        ])->orderBy('lecture_date', 'DESC');


        $resoult = $mapTba->get()->filter(function ($tba) {
            if ($tba->tbaStatistics->isNotEmpty()) {
                if ((int)$tba->tbaStatistics[0]->idx >= 70 && (int)$tba->tbaStatistics[1]->idx >= 70) {
                    \Log::info($tba->tbaStatistics->toArray());
                    return $tba->tbaStatistics;
                }
            }
        })->count();


        dd($resoult);
    }

    public function testTba()
    {
        //影片狀態
        $content_status = [1];
        // 發布狀態
        $content_public = [1, 0];
        $group_status   = 1;
        $channel        = 109;

        $groupChannelContents = GroupChannelContent::query()
            ->selectRaw("count(*) as total, group_channel_contents.grades_id")
            ->where('group_id', 3)
            ->whereIn('content_public', $content_public)
            ->where('content_status', 1)
            ->groupBy('group_channel_contents.grades_id')->get();

        $gradeLangs = GradeLang::query()->where('locales_id', 65)->get();
        $result     = $groupChannelContents->map(function ($groupChannelContent) use ($gradeLangs) {
            $data = [];
            $all  = $gradeLangs->where('grades_id', $groupChannelContent->grades_id)->map(function ($gradeLang) use ($groupChannelContent) {
                return $data = ['text' => $gradeLang->name, 'value' => $groupChannelContent->total, 'id' => $groupChannelContent->grades_id];
            });
            if ($groupChannelContent->grades_id === null) {
                return $data = ['text' => 'Other', 'value' => $groupChannelContent->total, 'id' => 'Other'];
            }

            return $all;
        });
        /*return $gradeLangs->filter(function ($gradeLang) use ($groupChannelContent) {
//                $data = [];
//                dd($groupChannelContent->grades_id === $gradeLang->grades_id,$gradeLang->grades_id,$groupChannelContent->grades_id,is_null($groupChannelContent->grades_id));
//                dd($groupChannelContent);
            var_dump($groupChannelContent->grades_id);
            if ($groupChannelContent->grades_id === $gradeLang->grades_id) {
               return ['text' => $gradeLang->name, 'value' => $groupChannelContent->total, 'id' => $groupChannelContent->grades_id];
            }
            if ($groupChannelContent->grades_id === null) {
                return ['text' => 'Other', 'value' => $groupChannelContent->total, 'id' => 'Other'];
            }
//                dd($data);
//                return $data;
        });*/


        dd($result->toArray());


        $select = "tbas.*, group_channel_contents.content_status, group_channel_contents.content_public, group_channel_contents.group_subject_fields_id, group_channel_contents.grades_id";
//            "group_channel_contents.content_status, group_channel_contents.content_public, group_channel_contents.group_subject_fields_id, group_channel_contents.grades_id,
//            tbas.name, tbas.description, tbas.thumbnail, tbas.hits, tbas.created_at, tbas.updated_at, tbas.teacher, tbas.lecture_date";

//        dd($select);
        $mapTba = Tba::query()
            ->selectRaw($select)
            ->join('group_channel_contents', 'group_channel_contents.content_id', 'tbas.id')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', $content_status)
            ->whereIn('group_channel_contents.content_public', $content_public)
            ->where('group_channel_contents.group_channel_id', $channel)
            ->where('groups.status', $group_status)
            ->with([
                'user'              => function ($q) {
                    $q->select('users.id', 'users.name');
                },
                'tbaPlaylistTracks' => function ($q) {
                    $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                        ->orderBy('tba_playlist_tracks.list_order');
                },
                'groupChannels'     => function ($q) use ($channel) {
                    $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description')
                        ->where('group_channels.id', $channel)
                        ->orderBy('group_channels.created_at', 'ASC');
                },
            ])->orderBy('tbas.lecture_date', 'DESC')->orderBy('tbas.created_at', 'DESC')
            ->get();

        dd($mapTba->toArray());
    }

    public function testGetTicket()
    {
        //影片狀態
        $content_status = [1];
        // 發布狀態
        $content_public = [1, 0];
        $group_status   = 1;
        $channel        = 109;
        $select         = "tbas.*, group_channel_contents.content_status, group_channel_contents.content_public, group_channel_contents.group_subject_fields_id, group_channel_contents.grades_id";
        $mapTba         = Tba::query()
            ->selectRaw($select)
            ->join('group_channel_contents', 'group_channel_contents.content_id', 'tbas.id')
            ->join('groups', 'group_channel_contents.group_id', '=', 'groups.id')
            ->where('content_type', 'Tba')
            ->whereIn('content_status', $content_status)
            ->whereIn('group_channel_contents.content_public', $content_public)
            ->where('group_channel_contents.group_channel_id', $channel)
            ->where('groups.status', $group_status)
            ->with([
                'user'              => function ($q) {
                    $q->select('users.id', 'users.name');
                },
                'tbaPlaylistTracks' => function ($q) {
                    $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                        ->orderBy('tba_playlist_tracks.list_order');
                },
                'groupChannels'     => function ($q) use ($channel) {
                    $q->select('group_channels.id', 'group_channels.group_id', 'group_channels.name', 'group_channels.description')
                        ->where('group_channels.id', $channel)
                        ->orderBy('group_channels.created_at', 'ASC');
                },
            ])->orderBy('tbas.lecture_date', 'DESC')->orderBy('tbas.created_at', 'DESC')
            ->get();
    }

    // 預設教研等級
    public function testRating()
    {
        $arr      = Lang::get('exhibition/rating');
        $groupIds = Group::query()->get()->pluck('id');
        $groupIds->each(function ($q) {
            for ($i = 1; $i <= 5; $i++) {
                Rating::query()->create([
                    'groups_id' => $q,
                    'type'      => $i
                ]);
            }
        });


        dd($arr);
    }

    // GroupChannelContent Default Rating
    public function testRatingAddForGroupChannelContent()
    {
        $ratings = Rating::query()->groupBy('groups_id')->get();
        $ratings->each(function ($rating) {
            GroupChannelContent::query()->where('group_id', $rating->groups_id)->update([
                'ratings_id' => $rating->id
            ]);
        });
    }

    public function testGetRating()
    {

//        $lang                      = new \App\Libraries\Lang\Lang();
//        $locales_id                = $lang->getConvertByLangString(\App::getLocale());
        $channelId = 109;
        $userId    = 948;

        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId($channelId);
        GlobalPlatform::getMemberDuty($convertChannelIdToGroupId, $userId) === DutyType::Admin && DutyType::Expert && DutyType::General
            ? $content_public = [1, 0]
            : $content_public = [1];

        $groupChannelContents = GroupChannelContent::query()
            ->with([
                'groupRatingFields' => function ($q) {
                    $q->select('id', 'type', 'name');
                }
            ])
            ->selectRaw("count(*) as total, group_channel_contents.ratings_id")
            ->where('group_id', $convertChannelIdToGroupId)
            ->whereIn('content_public', $content_public)
            ->where('content_status', $content_public)
            ->groupBy('group_channel_contents.ratings_id')
            ->orderBy('group_channel_contents.ratings_id', ' ASC')
            ->whereNotNull('group_channel_contents.ratings_id')
            ->get();

        $result = $groupChannelContents->map(function ($q) {
            $langKey = 'exhibition/rating.' . $q->groupRatingFields->first()->type;
            return $data = ['text' => ($q->name) ? $q->name : Lang::get($langKey), 'value' => $q->total, 'id' => $q->ratings_id];
        });
        dd($result);
//        if (is_numeric($group_subject_fields_id)) {
//            $groupChannelContents->where('group_subject_fields_id', $group_subject_fields_id);
//        }
//        if ($group_subject_fields_id === 'Other') {
//            $groupChannelContents->whereNull('grades_id');
//        }
//
//        $gradeLangs = GradeLang::query()->where('locales_id', $locales_id)->get();

//        $result = $groupChannelContents->get()->map(function ($groupChannelContent) use ($gradeLangs) {
//            $data = [];
//            $all  = $gradeLangs->where('grades_id', $groupChannelContent->grades_id)->flatMap(function ($gradeLang) use ($groupChannelContent) {
//                return $data = ['text' => $gradeLang->name, 'value' => $groupChannelContent->total, 'id' => $groupChannelContent->grades_id];
//            });
//
//            if ($groupChannelContent->grades_id === null) {
//                return $data = ['text' => __('app/subject-field.Other'), 'value' => $groupChannelContent->total, 'id' => 'Other'];
//            }
//            return $all;
//        });

//        return $result->toArray();
    }

    public function testArray()
    {
        $channelId                 = 3;
        $userId                    = 948;
        $ratingId                  = null;
        $group_subject_fields_id   = 41;
        $locales_id                = 37;
        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId($channelId);
        GlobalPlatform::getMemberDuty($convertChannelIdToGroupId, $userId) === DutyType::Admin && DutyType::Expert && DutyType::General
            ? $content_public = [1, 0]
            : $content_public = [1];

        $groupChannelContents = GroupChannelContent::query()
            ->selectRaw("count(*) as total, group_channel_contents.grades_id")
            ->whereIn('content_public', $content_public)
            ->where('group_id', $convertChannelIdToGroupId)
            ->where('content_status', 1)
            ->groupBy('group_channel_contents.grades_id')
            ->orderByRaw("ISNULL(group_channel_contents.grades_id),group_channel_contents.grades_id ASC");


        // 增加 $group_subject_fields_id 條件連動
        if (is_numeric($group_subject_fields_id)) {
            $groupChannelContents->where(['group_subject_fields_id' => $group_subject_fields_id]);
        }
        // 增加 $ratingId 條件連動
        if (is_numeric($ratingId)) {
            $groupChannelContents->where('ratings_id', $ratingId);
        }
        // 增加 $group_subject_fields_id 條件連動
        if ($group_subject_fields_id === 'Other') {
            $groupChannelContents->whereNull('grades_id');
        }
        dd($groupChannelContents->toSqlBinding());

        $gradeLangs = GradeLang::query()->where('locales_id', $locales_id)->get();


        $result = collect();


        $builders = $groupChannelContents->get()->map(function ($groupChannelContent) use ($gradeLangs) {
            dd($groupChannelContent);
//            dd($gradeLangs->where('grades_id', $groupChannelContent->grades_id)->toArray());
//            $gradeLangs->where('grades_id', $groupChannelContent->grades_id)->each(function ($gradeLang) use ($groupChannelContent, $result) {
//                $result->push(['text' => $gradeLang->name, 'value' => $groupChannelContent->total, 'id' => $groupChannelContent->grades_id]);
//            });

//            if ($groupChannelContent->grades_id === null) {
//                $result->push(['text' => __('app/subject-field.Other'), 'value' => $groupChannelContent->total, 'id' => 'Other']);
//            }
        });
        dd($builders);
//        dd($result->toArray());
    }

    public function testOptimization()
    {
        $contentIds = [
            6872,
            6873,
            6878,
            6879,
            6880,
            6881,
            6886,
            6887,
            6888,
            6889,
            6890,
            6898,
            6899,
            6901,
            6902,
            6903,
            6928,
            6938,
            6939,
            6940,
            6949,
            6951,
            6952,
            6953,
            6958,
            6963,
            6964,
            6969,
            6985,
            7377,
            7448,
            7613,
            7629,
            7655,
            7670,
            7673,
            7698,
            7701,
            7823,
            7825,
            7833,
            7839,
            7847,
            8001,
            8351,
        ];
        $mapTba     = Tba::query()->whereIn('id', $contentIds)->with([
            'tbaPlaylistTracks' => function ($q) {
                $q->select('tba_playlist_tracks.id', 'tba_playlist_tracks.tba_id', 'tba_playlist_tracks.ref_tba_id')
                    ->orderBy('tba_playlist_tracks.list_order');
            },
            'tbaStatistics'     => function ($q) {
//                $q->select('tba_statistics.tba_id', 'tba_statistics.type', 'tba_statistics.idx');
                $q->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id')
//                    ->whereIn('tba_statistics.type', [47, 48])
                    ->groupBy('tba_statistics.tba_id');
            }
        ])->orderBy('lecture_date', 'DESC');
//        dd($mapTba->get()->toArray());
        $builders = TbaStatistic::query()
            ->selectRaw('MAX(CASE WHEN type = 47 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS T,MAX(CASE WHEN type = 48 THEN CAST(tba_statistics.idx AS signed) ELSE 0 END) AS P,tba_statistics.tba_id')
            ->whereIn('tba_id', $contentIds)
//            ->whereIn('tba_statistics.type', [47, 48])
            ->groupBy('tba_statistics.tba_id')
            ->get();
        dd($builders->toArray());

    }

    public function testGroup()
    {
        $convertChannelIdToGroupId = GlobalPlatform::convertChannelIdToGroupId(3);
//        dd($convertChannelIdToGroupId);
        $builders = GroupChannel::with([
            'DistrictGroup' => function ($q) {
                $q->select('id', 'groups_id', 'districts_id');
                $q->with([
                    'districts' => function ($b) {
                        $b->select('id', 'abbr', 'school_code');
                    }
                ]);
            }
        ])->where('id', $convertChannelIdToGroupId)->get();
        dd($builders->toArray());
    }

    /**
     * 建立學區 district_channel_content
     */
    public function testGetDistrictByChannelMovie()
    {
        $abbr      = 'JXLMPT';
        $districts = $this->getDistrict($abbr);

        $districts->each(function ($district) {
            // 找出學區 $groupIds
            $groupIds = $district->districtGroups->map(function ($q) {
                return $q->groups_id;
            });
            $rating   = Rating::query()->where('districts_id', $district->id)->first();

            GroupChannelContent::query()->whereIn('group_id', $groupIds)->where(['content_status' => 1, 'content_public' => 1])->get()->each(function ($q) use ($district, $rating) {

                DistrictChannelContent::query()->updateOrInsert([
                    'content_id'   => $q->content_id,
                    'districts_id' => $district->id,
                    'groups_id'    => $q->group_id,
                ], [
                    'ratings_id'              => $rating->id,
                    'grades_id'               => $q->grades_id,
                    'group_subject_fields_id' => $q->group_subject_fields_id,
                ]);
            });
//            dd($groupChannelContents->count());
        });
        dd(1);

    }

    /**
     * 建立學區學科
     */
    public function testDistrictSubject()
    {
        $abbr      = 'TTTA';
        $districts = $this->getDistrict($abbr);

        $districts->each(function ($district) {
            // 找出學區 $groupIds
            $groupIds = $district->districtGroups->map(function ($q) {
                return $q->groups_id;
            });

            GroupSubjectFields::query()->whereIn('groups_id', $groupIds)->groupBy('subject')->get()->each(function ($q, $key) use ($district) {
                DistrictSubject::query()->updateOrInsert([
                    'subject'      => $q->subject,
                    'alias'        => $q->alias,
                    'districts_id' => $district->id,
//                    'subject_fields_id' => $q->subject_fields_id,
                ], [
                    'subject' => $q->subject,
                    'alias'   => $q->alias,
                    'order'   => $key + 1,
//                    'districts_id'      => $district->id,
//                    'subject_fields_id' => $q->subject_fields_id,
                ]);
            });
        });
    }

    /**
     * 建立 學區與頻道學科合併
     */
    public function testDistrictGroupSubject()
    {
        $abbr      = 'JXLMPT';
        $districts = $this->getDistrict($abbr);

        $districts->each(function ($district) {
            $groupIds = [];
            // 找出學區 $groupIds
            $groupIds = $district->districtGroups->map(function ($q) {
                return $q->groups_id;
            });

            GroupSubjectFields::query()->whereIn('groups_id', $groupIds)->get()->each(function ($groupSubjectField) use ($district) {
                DistrictGroupSubject::query()->updateOrInsert([
                    'group_subject_fields_id' => $groupSubjectField->id,
                ], [
                    'group_subject_fields_id' => $groupSubjectField->id,
                    'district_subjects_id'    => DistrictSubject::query()->where('districts_id', $district->id)
                            ->where('subject', $groupSubjectField->subject)->pluck('id')->first() ?? null
                ]);
            });
        });
    }

    /**
     * @param $abbr
     * @return Districts[]|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getDistrict($abbr)
    {
        if ($abbr) {
            return $districts = Districts::with('districtGroups')->where('abbr', $abbr)->get();
        } else {
            return $districts = Districts::with('districtGroups')->get();
        }
    }

}


<?php

namespace Tests\Feature;

use App\Http\Controllers\Exhibition\TbaVideoController;
use App\Models\Group;
use App\Models\GroupChannel;
use App\Models\Tba;
use App\Models\TbaAnnex;
use App\Models\TbaStatistic;
use App\Services\Cms\TbaService;
use App\Services\Exhibition\ExhibitionService;
use App\Types\Cms\CmsType;
use App\Types\Exhibition\SetType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Matrix\Builder;
use Tests\TestCase;

class TbaTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testRunMakeDoubleGreenLight()
    {
        $tbaStatistics = TbaStatistic::query()->selectRaw("
        MAX(CASE WHEN type = 47 THEN CAST(idx AS signed) ELSE 0 END) AS T,
        MAX(CASE WHEN type = 48 THEN CAST(idx AS signed) ELSE 0 END) AS P,tba_id
        ")->groupBy('tba_id')->get();

        $tbaIds = [];
        $tbaStatistics->each(function ($q) use (&$tbaIds) {
            if ($q->T >= 70 && $q->P >= 70) {
                $tbaIds[] = $q->tba_id;
            }
        });

        Tba::query()->whereIn('id', $tbaIds)->update(['double_green_light_status' => 1]);
    }

    public function testTbaStatistics()
    {
        $json_decode  = json_decode(file_get_contents(public_path('test.json')), true);
        $data['list'] = [
            ['type' => 'TechDex', 'idx' => 70.5644],
            ['type' => 'PedaDex', 'idx' => 70],
            ['type' => 45, 'idx' => 86.8287],
            ['type' => 42, 'idx' => 86],
            ['type' => 44, 'idx' => 89.9626],
            ['type' => 41, 'idx' => 91],
            ['type' => 65, 'idx' => 67.3564],
            ['type' => 18, 'idx' => 67],
        ];
        $collection   = collect($data['list']);


        $collection  = collect($json_decode['stat']['list']);
        $collection1 = $collection->whereIn('type', ['TechDex', 'PedaDex'])->where('idx', '>=', 70)->count();

        if ($collection1 === 2) {
            return 1;
        }

    }

    public function testGroupByUser()
    {
        $userIds = [];
        $users   = Group::query()->findOrFail('7')->users->map(function ($q) use (&$userIds) {
            return $userIds[] = $q->id;
        });

        dd($users);
    }

    public function testExistForGroupContent()
    {
        $group_id = 10;
        $tba      = Tba::query()->select('id')->with('groupChannels')->where('habook_id', auth()->user()->habook)->get()->filter(function ($q) {
            if ($q->groupChannels()->where('id', 130)->exists()) {
                return $q;
            }
        })->toArray();
//        GroupChannel::query()->findOrFail(3)->
        dd($tba);

    }

    public function testTP()
    {
        $collection = TbaStatistic::query()->where('tba_id', 8816)->whereIn('type', [47, 48])->where('idx', '!=', 0)->exists();
        dd($collection);
    }

    public function testCount()
    {
        $tba_ids = Tba::query()
//            ->withCount([
//            'tbaAnnexs as lesson_plan' => function ($q) {
//                $q->where('type', 'Material');
//            },
//            'tbaAnnexs as material'    => function ($q) {
//                $q->where('type', 'LessonPlan');
//            }
//        ])
            ->where('habook_id', 'rice#5402')->pluck('id');
        $select  = "COUNT(CASE WHEN type = 'Material' THEN tba_id END)   AS material,
                   COUNT(CASE WHEN type = 'LessonPlan' THEN tba_id END) AS lessonPlan";
        $toArray = TbaAnnex::query()->selectRaw($select)->whereIn('tba_id', $tba_ids)->get()->first()->toArray();

        dd($toArray);
    }


    public function testCnApi()
    {
        \App::setLocale('tw');
        $locale = \App::getLocale();

        auth()->loginUsingId(948);
        $groupIds = auth()->user()->groups->pluck('id');
//        dd($groupIds->toArray());
        $data['channel']['excs'] = app(ExhibitionService::class)->getGroupChannelSetsByUser(CmsType::TbaVideo, SetType::Excellent, $groupIds->toArray());
        dd($data);
//        $data['channel']['hits'] = []; // 待修改:訂閱數為參考依據
        $data['channel']['data'] = app(ExhibitionService::class)->getUserVideoCount(auth()->id());
        dd(dd($data));

    }

    /**
     * 上傳影片
     * 1 Tba
     * 2 tba_analysis_events
     * 3 tba_statistics
     * 4
     *
     */
    public function testCreateTba()
    {
        //886 台灣
        //86 大陸
        //1 美國

        $country_code = 886;

        $info = [];
        // tba_analysis_events
        $anal      = collect();
        $eventJson = collect(json_decode(file_get_contents(public_path('tba_info/event.json'))));
        $eventJson->groupBy('Event')->each(function ($q) use (&$anal) {
            $data = [];
            foreach ($q as $item) {
                $data[] = [
                    $item->TimeStrt ?? 0,
                    $item->TimeEnd ?? 0,
                ];
            }
            $anal->push([
                'event' => $q->first()->Event ?? null,
                'mode'  => $q->first()->Mode ?? null,
                'data'  => $data
            ]);
        });
        $info['anal'] = $anal->toArray();

        $eval         = collect();
        $AutoPedaJson = collect(json_decode(file_get_contents(public_path('tba_info/Auto_Peda.json'))));
        $AutoPedaJson->each(function ($q) use ($country_code, $eval) {
            $eval->push([
                'time' => $q->TimePoint,
                'text' => $this->convertEval($country_code, $q->Type),
            ]);
        });
        $info['eval']['data'] = $eval->toArray();

        $stat            = collect();
        $DataFreqJson    = collect(json_decode(file_get_contents(public_path('tba_info/Data_Freq.json'))));
        $DataPedaDexJson = collect(json_decode(file_get_contents(public_path('tba_info/Data_PedaDex.json'))));
        $DataFreqJson->each(function ($q) use ($stat) {
            $stat->push([
                'type'     => $q->Event,
                'freq'     => $q->Freq,
                'duration' => 0,
                'idx'      => 0,
            ]);
        });
        $DataPedaDexJson->each(function ($q) use ($stat) {
            $stat->push([
                'type'     => $q->Event,
                'freq'     => 0,
                'duration' => 0,
                'idx'      => $q->Index,
            ]);
        });
        $DataTimeJson = collect(json_decode(file_get_contents(public_path('tba_info/Data_Time.json'))));
        $DataTimeJson->each(function ($q) use ($stat) {
            $stat->push([
                'type'     => $q->Event,
                'freq'     => 0,
                'duration' => $q->Duration,
                'idx'      => 0,
            ]);
        });
        $info['stat']['list'] = $stat->toArray();

        dd($info);


        $annex = collect();


    }

    /**
     * @param $country_code
     * @param $type
     * @return array|\ArrayAccess|mixed|string
     */
    public function convertEval($country_code, $type)
    {
        switch ($type) {
            case 'LTK':
                $result = Arr::get(['886' => '學習任務', '86' => '学习任务', '1' => 'Learning assignment'], $country_code);
                break;
            case 'SCD':
                $result = Arr::get(['886' => '生本決策', '86' => '生本决策', '1' => 'Student - center decision'], $country_code);
                break;
            case 'WCT':
                $result = Arr::get(['886' => '全班測驗', '86' => '全班测验', '1' => 'Whole -class assessment'], $country_code);
                break;
            case 'WCI':
                $result = Arr::get(['886' => '全班互動', '86' => '全班互动', '1' => 'Whole -class interaction'], $country_code);
                break;
            default:
                $result = Arr::get(['886' => '同步差異', '86' => '同步差异', '1' => 'Synchronous differentiated instruction'], $country_code);
        }
        return $result;
    }

}

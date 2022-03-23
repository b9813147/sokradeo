<?php

namespace App\Repositories\Tba;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\TbaStatistic;
use App\Types\Tba\StatisticType;

class StatisticRepository extends BaseRepository
{
    //
    /**
     * @var TbaStatistic
     */
    protected $model;

    public function __construct(TbaStatistic $tbaStatistic)
    {
        $this->model = $tbaStatistic;
    }

    //
    public function getStats($tbaId)
    {
        // 待實作
    }

    //
    public function getStat($statId)
    {
        // 待實作
    }

    //
    public function createStats($tbaId, $stats) // 註:因為此功能會修改stats值 故該參數不使用參考
    {
        $timestamp = date("Y-m-d H:i:s");

        foreach ($stats as $i => $v) {
            $stats[$i]['tba_id']     = $tbaId;
            $stats[$i]['type']       = StatisticType::getConstant($v['type']);
            $stats[$i]['created_at'] = $timestamp;
            $stats[$i]['updated_at'] = $timestamp;
        }
        DB::table('tba_statistics')->insert($stats);
    }

    //
    public function deleteStats($tbaId) // 註:因為此功能會修改stats值 故該參數不使用參考
    {
        DB::table('tba_statistics')->where('tba_id', $tbaId)->delete();
    }

    //
    public function getTechFuns($tbaId)
    {
        $labels    = collect();
        $freqs     = collect();
        $durations = collect();

        $mapStatIdx = Lang::get('app/tba/statistic-index');

        TbaStatistic::where('tba_id', $tbaId)
            ->whereIn('type', StatisticType::getTechFunResults())
            ->select(['type', 'freq', 'duration'])
            ->orderBy('type')
            ->get()
            ->each(function ($v) use (& $mapStatIdx, $labels, $freqs, $durations) {
                $labels->push($mapStatIdx[$v->type]);
                $freqs->push($v->freq);
                $durations->push($v->duration);
            });

        return [
            'labels'    => $labels,
            'freqs'     => $freqs,
            'durations' => $durations,
        ];
    }

    //
    public function getTechInteractIdx($tbaId)
    {
        $tbaStat = TbaStatistic::where('tba_id', $tbaId)
            ->where('type', StatisticType::getTechInteractIdx())
            ->select(['idx'])
            ->first();

        return [
            'idx' => (isset($tbaStat->idx)) ? $tbaStat->idx : null
        ];
    }

    //
    public function getMethodAnal($tbaId)
    {
        $methodAnal = [
            'value' => null,
            'items' => [
                'labels' => collect(),
                'values' => collect(),
            ],
        ];

        $types      = StatisticType::getMethodAnal();
        $mapStatIdx = Lang::get('app/tba/statistic-index');

        foreach ($types['items'] as $type) {
            $tbaStat = TbaStatistic::where('tba_id', $tbaId)
                ->where('type', $type)
                ->select(['type', 'idx'])
                ->first();
            if ($tbaStat === null) {
                $methodAnal['items']['labels']->push($mapStatIdx[$type]);
                $methodAnal['items']['values']->push(0);
            } else {
                $methodAnal['items']['labels']->push($mapStatIdx[$type]);
                $methodAnal['items']['values']->push($tbaStat->idx);
            }
        }

        $tbaStat             = TbaStatistic::where('tba_id', $tbaId)
            ->where('type', $types['value'])
            ->select(['type', 'idx'])
            ->first();
        $methodAnal['value'] = is_null($tbaStat) ? null : $tbaStat->idx;

//        $tbaStat = TbaStatistic::where('tba_id', $tbaId)
//            ->whereIn('type', array_merge([$types['value']], $types['items']))
//            ->select(['type', 'idx'])
//            ->get()
//            ->each(function ($v) use (& $types, & $mapStatIdx, & $methodAnal) {
//                if ($v->type === $types['value']) {
//                    $methodAnal['value'] = $v->idx;
//                } else {
//                    $methodAnal['items']['labels']->push($mapStatIdx[$v->type]);
//                    $methodAnal['items']['values']->push($v->idx);
//                }
//        });

        return $methodAnal;
    }

    //
    public function getContentIdx($tbaId)
    {
        $contentIdx = [
            'value' => null,
            'items' => [
                'labels' => collect(),
                'values' => collect(),
            ],
        ];

        $types      = StatisticType::getContentIdx();
        $mapStatIdx = Lang::get('app/tba/statistic-index');

        $tbaStat = TbaStatistic::where('tba_id', $tbaId)
            ->whereIn('type', array_merge([$types['value']], $types['items']))
            ->select(['type', 'idx'])
            ->get()
            ->each(function ($v) use (& $types, & $mapStatIdx, & $contentIdx) {
                if ($v->type === $types['value']) {
                    $contentIdx['value'] = $v->idx;
                } else {
                    $contentIdx['items']['labels']->push($mapStatIdx[$v->type]);
                    $contentIdx['items']['values']->push($v->idx);
                }
            });

        // 未評分時, 給予基本項目顯示
        if (is_null($contentIdx['value'])) {

            $contentIdx['items'] = [
                'labels' => collect(),
                'values' => collect(),
            ];

            foreach ($types['items'] as $v) {
                $contentIdx['items']['labels']->push($mapStatIdx[$v]);
                $contentIdx['items']['values']->push(0);
            }
        }

        return $contentIdx;
    }

    /**
     * 檢查T P 分數 是否有效
     * 47 (T)
     * 48 (P)
     * @param int $tba_id
     * @param array $type
     * @return bool
     */
    public function checkTPNumber(int $tba_id, array $type): bool
    {
        try {
            return $this->model->query()->where('tba_id', $tba_id)->whereIn('type', $type)->where('idx', '!=', 0)->exists();
        } catch (\Exception $exception) {
            \Log::error($exception->getMessage());
            return false;
        }

    }

}

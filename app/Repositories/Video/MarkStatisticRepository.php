<?php

namespace App\Repositories\Video;

use App\Models\VideoMarkStatistic;

class MarkStatisticRepository
{
    private $maxTotalMarkStats = 30; // 最大統計區間數, 必須大於等於1
    
    //
    public function __construct()
    {
        
    }
    
    //
    public function getMaxTotalMarkStats()
    {
        return $this->maxTotalMarkStats;
    }
    
    //
    public function getMarkStatsByMarkType($videoId, $markType)
    {
        return VideoMarkStatistic::where('video_id', $videoId)
                ->where('type', $markType)
                ->orderBy('time', 'asc')
                ->get();
    }
    
    //
    public function createMarkStatsByMarkType($videoId, $markType, & $markStats)
    {
        $this->processMarkStatsAsMarkType($markStats);
        
        VideoMarkStatistic::where('video_id', $videoId)
            ->where('type', $markType)
            ->delete();
        
        foreach ($markStats as $i => $v) {
            $markStats[$i]['video_id'] = $videoId;
            $markStats[$i]['type'    ] = $markType;
        }
        VideoMarkStatistic::insert($markStats);
    }
    
    //
    private function processMarkStatsAsMarkType(& $markStats)
    {
        if (count($markStats) <= $this->maxTotalMarkStats) {
            return;
        }
        
        $markStats = collect($markStats);
        $min  = $markStats->min('time');
        $max  = $markStats->max('time');
        
        if (intval($max - $min) === 0) {
            $markStats = [[
                    'time'  => $min,
                    'count' => $markStats->sum('count'),
            ]];
            return;
        }
        
        $processed = array_fill(0, $this->maxTotalMarkStats, [
                'time'  => 0,
                'count' => 0,
        ]);
        
        $coef = ($this->maxTotalMarkStats - 1) / ($max - $min);
        $markStats->each(function ($v) use (& $processed, $min, $coef) {
            $idx = ($v['time'] - $min) * $coef;
            $processed[$idx]['time' ] = ($processed[$idx]['time'] === 0) ? $v['time'] : intval(($processed[$idx]['time'] + $v['time']) / 2);
            $processed[$idx]['count'] += $v['count'];
        });
        
        $markStats = $processed;
    }
    
}

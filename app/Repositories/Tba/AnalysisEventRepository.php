<?php

namespace App\Repositories\Tba;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\TbaAnalysisEvent;
use App\Models\TbaAnalysisEventMode;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaVideoMap;

class AnalysisEventRepository
{
    //
    public function __construct()
    {

    }

    //
    public function getEvents($tbaId)
    {
        $events = DB::table('tba_analysis_events')
            ->where('tba_id', $tbaId)
            ->join('tba_analysis_event_modes', 'tba_analysis_events.tba_analysis_event_mode_id', '=', 'tba_analysis_event_modes.id')
            ->orderBy('order', 'asc')
            ->orderBy('time_point', 'asc')
            ->select('tba_analysis_events.id', 'time_start', 'time_end', 'time_point', 'event', 'mode', 'type', 'color', 'style', 'order')
            ->get();

        $pattern = collect();
        $events->unique('event')->each(function ($v) use ($pattern) {
            $pattern->push($v->event);
        });

        // labels
        $labels = $this->parseEventsToLabels($pattern);

        // range
        $range = $this->getTbaTimeRange($tbaId);

        // datasets
        $dataset = $this->getDataset($events, $pattern);

        return [
            'labels'   => $labels,
            'range'    => $range,
            'datasets' => [$dataset],
        ];
    }

    public function getTbaTimeRange($tbaId)
    {
        $anals     = TbaAnalysisEvent::where('tba_id', $tbaId)->get();
        $evals     = TbaEvaluateEvent::where('tba_id', $tbaId)->get();
        $video_map = TbaVideoMap::where('tba_id', $tbaId)->get();
        $extremums = collect([$anals->min('time_start'), $anals->min('time_point'), $evals->min('time_point'), $anals->max('time_end'), $anals->max('time_point'), $evals->max('time_point'), $video_map->min('tba_start')]);
        $range     = ['min' => $extremums->min(), 'max' => $video_map->max('tba_end')];

        return $range;
    }

    //
    public function createEvents($tbaId, &$events)
    {
        // 待實作
    }

    //
    public function createEventGroups($tbaId, &$groups)
    {
        $timestamp = date("Y-m-d H:i:s");

        foreach ($groups as $group) {

            $eventMode = TbaAnalysisEventMode::where(['event' => $group['event'], 'mode' => $group['mode']])->first();
            if (is_null($eventMode)) {
                continue;
            }

            $events = [];

            if ($eventMode->type === 0) { // 單點

                foreach ($group['data'] as $data) {

                    array_push($events, [
                        'tba_id'                     => $tbaId,
                        'tba_analysis_event_mode_id' => $eventMode->id,
                        'time_point'                 => $data,
                        'created_at'                 => $timestamp,
                        'updated_at'                 => $timestamp,
                    ]);

                }

            } else { // 區間

                foreach ($group['data'] as $data) {

                    array_push($events, [
                        'tba_id'                     => $tbaId,
                        'tba_analysis_event_mode_id' => $eventMode->id,
                        'time_start'                 => $data[0],
                        'time_end'                   => $data[1],
                        'time_point'                 => $data[0],
                        'created_at'                 => $timestamp,
                        'updated_at'                 => $timestamp,
                    ]);

                }

            }

            DB::table('tba_analysis_events')->insert($events);

        }
    }

    //
    public function deleteEvents($tbaId)
    {
        DB::table('tba_analysis_events')->where('tba_id', $tbaId)->delete();
    }

    //
    private function parseEventsToLabels(&$events)
    {
        $mapEventLabel = Lang::get('app/tba/analysis-event');
        return $events->map(function ($v) use ($mapEventLabel) {
            return $mapEventLabel[$v];
        });
    }

    //
    private function getDataset(&$events, &$pattern)
    {
        $dataset = [
            'ids'     => [],
            'colors'  => [],
            'details' => [],
        ];

        foreach ($pattern as $i => $event) {

            array_push($dataset['ids'], []);
            array_push($dataset['colors'], []);
            array_push($dataset['details'], []);

            $colorShared = TbaAnalysisEventMode::where('event', $event)->count() === 1;

            $tmpEvents = $events->where('event', $event);
            foreach ($tmpEvents as $v) {
                array_push($dataset['ids'][$i], $v->id);
                if ($colorShared) {
                    $dataset['colors'][$i] = $v->color;
                } else {
                    array_push($dataset['colors'][$i], $v->color);
                }
                $detail = ($v->type === 0) ? $v->time_point : [$v->time_start, $v->time_end];
                array_push($dataset['details'][$i], $detail);
            }
        }

        return $dataset;
    }

}

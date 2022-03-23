<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Models\Tba;
use App\Models\TbaEvaluateEvent;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EvaluateEventsSheet implements FromCollection, WithTitle, WithEvents, WithColumnFormatting
{
    protected $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return new Collection($this->createData());
    }

    public function title(): string
    {
        return Lang::get('app/tba/evaluate-event-export')['sheet-name'];
    }

    public function createData()
    {
        $tba_id               = $this->data['contentId'];
        $channel_id           = $this->data['channelId'];
        $group_ids            = $this->data['groupIds'];
        $enable_guest_events  = $this->data['enableGuestEvents'];
        $enable_person_events = $this->data['enablePersonEvents'];
        $tba                  = DB::table('tbas')->where('id', $tba_id)->get()->toArray();
        
        if (!empty($tba)) {
            $evaluator_number = TbaEvaluateEvent::distinct('tba_evaluate_user_id')->where('tba_id', $tba_id)->whereNotNull('tba_evaluate_user_id')->count('tba_evaluate_user_id');
            $query = DB::table('tba_evaluate_events')
                ->join('tba_evaluate_event_modes', 'tba_evaluate_events.tba_evaluate_event_mode_id', 'tba_evaluate_event_modes.id');
                
            if ($enable_guest_events !== true && $enable_person_events === false) {
                $query->where('tba_evaluate_users.identity', '!=', 'G');
            }
            $query->join('tba_evaluate_users', 'tba_evaluate_events.tba_evaluate_user_id', 'tba_evaluate_users.id');
            $query->join('users', 'tba_evaluate_users.user_id', 'users.id');
            $query->select(['tba_evaluate_events.*', 'tba_evaluate_users.identity', 'tba_evaluate_event_modes.mode', 'users.name']);
            $query->where('tba_evaluate_events.tba_id', $tba_id);
            $events = $query->get();
            if ($enable_person_events === true) {
                $query = DB::table('tba_evaluate_events')
                    ->join('tba_evaluate_event_modes', 'tba_evaluate_events.tba_evaluate_event_mode_id', 'tba_evaluate_event_modes.id');
                $query->join('users', 'tba_evaluate_events.user_id', 'users.id');
                $query->select(['tba_evaluate_events.*', 'tba_evaluate_event_modes.mode', 'users.name']);
                $query->where('tba_evaluate_events.user_id', auth()->id());
                $query->where('tba_evaluate_events.tba_id', $tba_id);
                $person_events = $query->get();
                foreach ($person_events as $event) {
                    $events[] = $event;
                }
            }
            $statistics = DB::table('tba_statistics')->where('tba_id', $tba_id)->get()->toArray();
            $idx = [
                'TechDex' => null,
                'PedaDex' => null,
                'CntDex'  => null,
            ];
            foreach ($statistics as $statistic) {
                switch ($statistic->type) {
                    case '47':
                        $idx['TechDex'] = round($statistic->idx);
                        break;
                    case '48':
                        $idx['PedaDex'] = round($statistic->idx);
                        break;
                    case '55':
                        $idx['CntDex'] = round($statistic->idx);
                        break;
                }
            }
            $lang   = Lang::get('app/tba/evaluate-event-export');
            $result = [
                [$lang['title']],
                [$lang['name'] . $tba[0]->name],
                [$lang['date'] . $tba[0]->lecture_date],
                [$lang['teacher'] . $tba[0]->teacher],
                [$lang['tech-dex'] . ($idx['TechDex'] === null || $idx['TechDex'] == 0 ? $lang['none'] : $idx['TechDex'])],
                [$lang['peda-dex'] . ($idx['PedaDex'] === null || $idx['PedaDex'] == 0 ? $lang['none'] : $idx['PedaDex'])],
                [$lang['cnt-dex'] . ($idx['CntDex'] === null || $idx['CntDex'] == 0 ? $lang['none'] : $idx['CntDex'])],
                [$lang['evaluator-number'] . $evaluator_number],
                [$lang['evaluator'], $lang['identity'], $lang['time'], $lang['type'], $lang['text'], $lang['url']],
            ];
            foreach ($events as $event) {
                $time     = $event->time_point;
                $to       = base64_encode("/Player?contentId={$tba_id}&groupIds={$group_ids}&channelId={$channel_id}&start={$time}&memberChannel=0");
                $result[] = [
                    $event->name,
                    Lang::get('app/tba/identity')[isset($event->identity) ? $event->identity : 'U'],
                    sprintf('%02d:%02d', ($time / 60 ), $time % 60),
                    Lang::get('app/tba/evaluate-event-mode')[$event->mode],
                    $event->text,
                    (\url("/auth/login?to=$to"))
                ];
            }
            return $result;
        } else {
            return [];
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $max_row = $event->sheet->getDelegate()->getHighestRow();
                for ($i = 10; $i <= $max_row; $i++) {
                    $value = $event->sheet->getDelegate()->getCell("F{$i}")->getValue();
                    $event->sheet->getDelegate()->getCell('F' . $i)->getHyperlink()->setUrl($value);
                }
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(100);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(50);
                $event->sheet->getDelegate()->getStyle('A1:F' . $max_row)->applyFromArray([
                    'font' => [
                        'size' => 10
                    ],
                ]);
                $event->sheet->getDelegate()->getStyle('A9:F9')->applyFromArray([
                    'fill' => [
                        'fillType'   => 'solid',
                        'startColor' => [
                            'rgb' => 'C9DAF8'
                        ],
                        'endColor'   => [
                            'argb' => 'C9DAF8'
                        ]
                    ]
                ]);
                $event->sheet->getDelegate()->setAutoFilter('A9:F9');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function recursivelyCollect ($item) {
        if (is_array($item)) {
            return recursivelyCollect(collect($item));
        } elseif ($item instanceof Collection) {
            $item->transform(static function ($collection) {
                return recursivelyCollect($collection);
            });
        } elseif (is_object($item)) {
            foreach ($item as $key => &$val) {
                $item->{$key} = recursivelyCollect($val);
            }
        }
        return $item;
    }
}

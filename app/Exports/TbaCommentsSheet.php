<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TbaCommentsSheet implements FromCollection, WithTitle, WithEvents, WithColumnFormatting
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
        $tbaId = $this->data['contentId'];
        $tba = DB::table('tbas')->where('id', $tbaId)->get()->toArray();
        $groupIds = $this->data['groupIds'];
        $channelId = $this->data['channelId'];
        $tbaComments = $this->data['tbaComments'];

        if (!empty($tba)) {
            $result = [];
            $lang = Lang::get('app/tba/evaluate-event-export');

            // General Info
            $result = [
                [$lang['title']],
                [$lang['name'] . $tba[0]->name],
                [$lang['date'] . $tba[0]->lecture_date],
                [$lang['teacher'] . $tba[0]->teacher],
            ];

            // Statistics
            $statistics = DB::table('tba_statistics')->where('tba_id', $tbaId)->get()->toArray();
            $idx = ['TechDex' => null, 'PedaDex' => null, 'CntDex'  => null];
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
            $result[] = [$lang['tech-dex'] . ($idx['TechDex'] === null || $idx['TechDex'] == 0 ? $lang['none'] : $idx['TechDex'])];
            $result[] = [$lang['peda-dex'] . ($idx['PedaDex'] === null || $idx['PedaDex'] == 0 ? $lang['none'] : $idx['PedaDex'])];
            $result[] = [$lang['cnt-dex'] . ($idx['CntDex'] === null || $idx['CntDex'] == 0 ? $lang['none'] : $idx['CntDex'])];

            // Comments
            $oberverCount = sizeof(array_column($tbaComments, null, 'user')); // add user value as a key to remove duplication, then count
            $result[] = [$lang['evaluator-number'] . $oberverCount];
            $result[] = [$lang['evaluator'], $lang['identity'], $lang['time'], $lang['type'], $lang['text'], $lang['url']];
            foreach ($tbaComments as $tbaComment) {
                $name = empty($tbaComment['user']) ? $tbaComment['nick_name'] : $tbaComment['user']['name'];
                $identity = empty($tbaComment['user']) ? 'G' : 'V'; // G: Guest, V: Member
                $time = $tbaComment['time'];
                $to = base64_encode("/Player?contentId={$tbaId}&groupIds={$groupIds}&channelId={$channelId}&start={$time}&memberChannel=0");
                $result[] = [
                    $name,
                    Lang::get('app/tba/identity')[$identity],
                    sprintf('%02d:%02d', ($time / 60), $time % 60),
                    $tbaComment['type'] . ' - ' . $tbaComment['tag']['name'],
                    $tbaComment['text'],
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

    public function recursivelyCollect($item)
    {
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

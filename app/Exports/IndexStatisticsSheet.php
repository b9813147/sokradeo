<?php

namespace App\Exports;

use App\Exports\IndexStatisticsMap;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use App\Models\Tba;
use App\Models\TbaEvaluateEvent;
use App\Models\TbaStatistic;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class IndexStatisticsSheet implements FromCollection, WithTitle, WithEvents, WithColumnFormatting
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
        return Lang::get('app/tba/index-statistics-export')['sheet-name'];
    }

    public function createData()
    {
        $tba_id     = $this->data['contentId'];
        $query      = TbaStatistic::where('tba_id', $tba_id)->get();
        $statistics = $query->toArray();
        if (!empty($statistics)) {
            $statistics_map = new IndexStatisticsMap();
            $feature_list   = $statistics_map->get_feature_list();
            foreach ($statistics as $statistic) {
                $type = $statistic['type'];
                if ($statistics_map->get_feature_name($type) !== false) {
                    $feature_name = $statistics_map->get_feature_name($type);
                    if ($statistics_map->check_if_idx_type($type) === true) {
                        if ($statistics_map->check_if_idx_decimal_type($type) === true) {
                            $feature_list[$feature_name] = $feature_list[$feature_name] + round($statistic['idx'] * 100);
                        } else {
                            $feature_list[$feature_name] = $feature_list[$feature_name] + round($statistic['idx']);
                        }
                    } else {
                        $feature_list[$feature_name] = $feature_list[$feature_name] + round($statistic['freq']);
                    }
                } else {
                    continue;
                }
            }
            $feature_list = $statistics_map->get_sum_result($feature_list);
            $lang = Lang::get('app/tba/index-statistics-export');
            $result = [[$lang['feature'], $lang['freq-or-idx']]];
            foreach ($feature_list as $name => $value) {
                $result[] = [
                    $lang['feature-list'][$name], ($value === null) ? '' : strval($value)
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
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(30);
                foreach (['A2', 'A29', 'A36'] as $col) {
                    $event->sheet->getDelegate()->getStyle($col)->applyFromArray([
                        'font' => [
                            'color'   => [
                                'argb' => '000080'
                            ]
                        ]
                    ]);
                }
                foreach (['A3', 'A9', 'A15', 'A20', 'A24', 'A30', 'A31', 'A32', 'A33', 'A34', 'A35', 'A37', 'A38', 'A39', 'A40', 'A41'] as $col) {
                    $event->sheet->getDelegate()->getStyle($col)->applyFromArray([
                        'font' => [
                            'color'   => [
                                'argb' => '008000'
                            ]
                        ]
                    ]);
                }
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
        ];
    }
}

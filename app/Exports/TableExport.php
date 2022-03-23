<?php

namespace App\Exports;

use App\Models\Grade;
use App\Models\Rating;
use App\Services\Cms\TbaService;
use App\Types\Tba\AnnexType;
use Illuminate\Contracts\Support\Responsable;
use App\Models\GroupSubjectFields;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Excel;


class TableExport implements FromCollection, Responsable
{


    use Exportable;

    private $fileName = 'export.xlsx';

    /**
     * Optional Writer Type
     */
    private $writerType = Excel::XLS;

    /**
     * Optional headers
     */
    private $headers = [
        'Content-Type' => 'text/xlsx',
    ];

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return new Collection($this->createData());

    }

    public function createData()
    {
        $tbaService = app(TbaService::class);
        $result     = [];

        $title   = [];
        $columns = $this->data['columns'];
        foreach ($columns as $column) {
            $column = (object)$column;
            if ($column->key != 'thumbnail') {
                if ($column->title) {
                    $title[] = $column->title;
                }
            }
        }

        $content   = [];
        $user_info = (object)$this->data['user_info'];
        $filter    = collect($user_info->filter);
        $filter    = $filter->filter(function ($q) {
            return $q && $q !== 'none';
        });

        $groupChannels = null;

        if ($filter->isNotEmpty()) {
            $groupChannels = $tbaService->getGroupByFilter($user_info->channelId, $user_info->id, $filter, false);
        } elseif ($filter->isEmpty()) {
            $groupChannels = $tbaService->getGroupChannel($user_info->channelId, $user_info->id, false);
        }

        $groupChannels->each(function ($v, $key) use (&$content) {
            $lessonPlan = $v->tbaAnnexs->filter(function ($tbaAnnex) {
                return $tbaAnnex->type === AnnexType::LessonPlan;
            });
            $material   = $v->tbaAnnexs->filter(function ($tbaAnnex) {
                return $tbaAnnex->type === AnnexType::Material;
            });

            $content[] = [
                'key'          => $key + 1,
                'status'       => $v->groupChannels->first()->pivot->content_public === 1 ? 'V' : '',
                'doubleGreen'  => $v->tbaStatistics->isNotEmpty() ? $v->tbaStatistics->first()->T >= 70 && $v->tbaStatistics->first()->P >= 70 ? 'V' : '' : '',
                'channel_name' => $v->name,
                'lecture_date' => $v->lecture_date,
                'teacher'      => $v->teacher,
                'rating'       => !empty($v->groupChannelContent()->first()->groupRatingFields()->first()->name) ? $v->groupChannelContent()->first()->groupRatingFields()->first()->name : 'x',
                'subject'      => !empty($v->groupChannelContent()->first()->groupSubjectFields()->first()->subject) ? $v->groupChannelContent()->first()->groupSubjectFields()->first()->subject : __('app/subject-field.Other'),
                'grade'        => $v->groupChannelContent()->first()->grades_id,
                'lessonPlan'   => $lessonPlan->isNotEmpty() ? 'V' : '',
                'material'     => $material->isNotEmpty() ? 'V' : '',
                't'            => $v->tbaStatistics->isNotEmpty() ? (string)$v->tbaStatistics->first()->T : (string)0,
                'p'            => $v->tbaStatistics->isNotEmpty() ? (string)$v->tbaStatistics->first()->P : (string)0,
                'c'            => $v->tbaStatistics->isNotEmpty() ? (string)$v->tbaStatistics->first()->C > (string)0 ? $v->tbaStatistics->first()->C : '' : '',
                'event_total'  => $v->tbaEvaluateEvents->isNotEmpty() ? (string)$v->tbaEvaluateEvents->first()->total : (string)0,
            ];
        });

        $result = [
            $title, $content
        ];

        return $result;
    }

}

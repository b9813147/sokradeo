<?php

namespace App\Http\Resources;

use App\Helpers\Custom\GlobalPlatform;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Tba */
class TbaCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id'               => $item->id,
                'name'             => !empty($item->name) ? $item->name : null,
                'teacher'          => !empty($item->teacher) ? $item->teacher : null,
                'habook'           => !empty($item->user->habook) ? $item->user->habook : null,
                'lecture_date'     => !empty($item->lecture_date) ? $item->lecture_date : null,
                'update_time'      => !empty($item->updated_at->toTimeString()) ? $item->updated_at->toTimeString() : null,
                'thum'             => !empty($item->thumbnail) ? $item->thumbnail : null,
                'channelName'      => !empty($item->groupChannels->first()->name) ? $item->groupChannels->first()->name : null,
                'lessonExample'    => !empty($item->tbaAnnexs) ? $item->tbaAnnexs->map(function ($tbaAnnex) {
                    return ['type' => !empty($tbaAnnex->type) ? GlobalPlatform::convertAnnexType($tbaAnnex->type) : null];
                }) : null,
                'description'      => !empty($item->description) ? $item->description : null,
                'observationFocus' => !empty($item->observation_focus) ? $item->observation_focus : null,
                'courseCore'       => !empty($item->course_core) ? $item->course_core : null,
                'studentCount'     => $item->student_count,
                'irsCount'         => $item->irs_count,
                'statistics'       => !empty($item->tbaStatistics) ? $item->tbaStatistics : [],
                'globalNormRefData' => GlobalPlatform::getGlobalNormRefData(),
            ];
        })->toArray();
    }
}

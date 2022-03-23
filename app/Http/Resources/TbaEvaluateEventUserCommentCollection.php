<?php

namespace App\Http\Resources;

use App\Helpers\Custom\GlobalPlatform;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TbaEvaluateEventUserCommentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($v) {
                $userCommentData = [
                    "tba_id" => $v->tba_id,
                    "group_id" => $v->group_id,
                    "channel_id" => $v->group_id ? GlobalPlatform::convertGroupIdToChannelId($v->group_id) : null,
                    "lesson_name" => $v->tba->name,
                    "teacher" => $v->tba->teacher,
                    "habook_id" => $v->tba->habook_id,
                    "time_point" => $v->time_point,
                    "text" => $v->text,
                    "event_mode" => $v->tbaEvaluateEventMode->mode,
                    "event_mode_tag" => __("app/tba/evaluate-event-mode." . $v->tbaEvaluateEventMode->mode),
                    "attachment" => !empty($v->tbaEvaluateEventFiles)
                        ? $v->tbaEvaluateEventFiles->map(function ($tbaEvaluateEventFile) {
                            return GlobalPlatform::getAttachmentData($tbaEvaluateEventFile);
                        })
                        : null,
                    "updated_at" => $v->updated_at
                ];
                return $userCommentData;
            }),
        ];
    }
}

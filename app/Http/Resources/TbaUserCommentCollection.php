<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\TbaComment */
class TbaUserCommentCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($item) {
                $commentData = [
                    'attachment' => $item['attachment'],
                    'type' => $item['type'],
                    'tag' => $item['tag'],
                    'isPositive' => $item['is_positive'],
                    'text' => $item['text'],
                    'time' => $item['time'],
                    'type' => $item['type'],
                    'updatedAt' => $item['updated_at'],
                    'tbaId' => empty($item['tba']) ? '' : $item['tba']['id'],
                    'lessonName' => empty($item['tba']) ? '' : $item['tba']['name'],
                    'teacher' => [
                        'name' => empty($item['tba']) ? '' : $item['tba']['teacher'],
                        'habookId' => empty($item['tba']) ? '' : $item['tba']['habook_id'],
                    ],
                    'groupId' => $item['group_id'],
                    'channelId' => $item['channel_id'],
                    'user' => [
                        'name' => empty($item['user']) ? $item['nick_name'] : $item['user']['name'],
                    ],
                ];
                return $commentData;
            })->sortByDesc('updatedAt')->toArray(),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\TbaComment */
class TbaCommentCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            $name = empty($item['user']) ? $item['nick_name'] : $item['user']['name'];
            $habook = empty($item['user']) ? null : $item['user']['habook'];
            return [
                'attachment' => $item['attachment'],
                'type' => $item['type'],
                'tag' => $item['tag'],
                'isPositive' => $item['is_positive'],
                'text' => $item['text'],
                'time' => $item['time'],
                'type' => $item['type'],
                'name' => $name,
                'habook' => $habook,
            ];
        })->sortBy('time')->toArray();
    }
}

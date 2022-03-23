<?php

namespace App\Http\Resources\Api\V1\Division;

use Illuminate\Http\Resources\Json\JsonResource;

class DivisionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'group_id' => $this->group_id,
            'title'    => $this->title,
        ];
    }
}

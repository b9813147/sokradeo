<?php

namespace App\Http\Resources\Api\V1;

use  Illuminate\Http\Resources\Json\JsonResource;

class GroupChannelContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}

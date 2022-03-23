<?php

namespace App\Http\Resources\Api\V1\Division;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'habook'    => $this->habook,
            'name'      => $this->name,
            'thumbnail' => $this->thumbnail,
        ];
    }
}

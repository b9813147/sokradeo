<?php

namespace App\Http\Resources\Api\V1\Division;

use App\Types\Group\DutyType;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DivisionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            $user = $item->users->filter(function ($user) use ($item) {
                $member_duty = $user->groups->where('id', $item->group_id)->first()->pivot->member_duty ?? null;
                if ($member_duty) {
                    if ($member_duty === DutyType::Admin || $member_duty === DutyType::Expert) {
                        return $user;
                    }
                }
            });

            return [
                'id'       => $item->id,
                'group_id' => $item->group_id,
                'title'    => $item->title,
                'tbas'     => $item->tbas,
                'users'    => UserResource::collection($user)
            ];
        })->toArray();
    }
}

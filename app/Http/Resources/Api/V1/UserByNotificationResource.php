<?php

namespace App\Http\Resources\Api\V1;

use App\Models\GroupChannel;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserByNotificationResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        //Mark read
        $this->markAsRead();

        // Modify notification data content
        $notifications = $this->notifications->flatMap(function ($q) {
            $collection = collect($q->data);
            if ($collection->has('channel_id') && $collection->get('channel_id') !== null) {
                $groupChannelInfo = GroupChannel::query()->find($collection->get('channel_id'));
                if ($groupChannelInfo) {
                    $collection->put('thumbnail', $groupChannelInfo->thumbnail);
                    $collection->put('channel', $groupChannelInfo->name);
                }
            }
            return [
                $collection
            ];
        });

        return $notifications->paginate(20);
    }
}

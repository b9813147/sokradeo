<?php

namespace App\Http\Controllers\Api\V1\Notification;


use App\Http\Controllers\Api\Controller;
use App\Http\Resources\Api\V1\UserByNotificationResource;
use App\Services\App\UserService;


class NotificationController extends Controller
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * NotificationController constructor.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @return string|UserByNotificationResource
     */
    public function show()
    {
        try {
            return new UserByNotificationResource($this->userService->getUser(auth()->id()));
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

    }
}

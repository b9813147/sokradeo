<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Default
 * @method static static General
 * @method static static Event
 * @method static static Review
 * @method static static License
 * @method static static Join
 */
final class NotificationMessageType extends Enum
{
    /**
     *  通知類型
     */
    const Default = 0;
    const Announce = 1;
    const Event = 2;
    const Review = 3;
    const License = 4;
    const Join = 5;
    const Lesson = 6;

    /**
     * @param int $type
     * @return bool
     */
    public static function check(int $type): bool
    {
        switch ($type) {
            case NotificationMessageType::Default:
            case NotificationMessageType::Announce;
            case NotificationMessageType::Event;
            case NotificationMessageType::Review;
            case NotificationMessageType::License;
            case NotificationMessageType::Join;
            case NotificationMessageType::Lesson;
                return true;
            default:
                return false;
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public static function list(): \Illuminate\Support\Collection
    {
        return collect([
            ['type' => 'Default', 'value' => NotificationMessageType::Default, 'text' => __('notification.Default')],
            ['type' => 'Announce', 'value' => NotificationMessageType::Announce, 'text' => __('notification.Announce')],
            ['type' => 'Event', 'value' => NotificationMessageType::Event, 'text' => __('notification.Event')],
            ['type' => 'Review', 'value' => NotificationMessageType::Review, 'text' => __('notification.Review')],
            ['type' => 'License', 'value' => NotificationMessageType::License, 'text' => __('notification.License')],
            ['type' => 'Join', 'value' => NotificationMessageType::Join, 'text' => __('notification.Join')],
            ['type' => 'Lesson', 'value' => NotificationMessageType::Lesson, 'text' => __('notification.Lesson')]
        ]);

    }
}

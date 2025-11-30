<?php

namespace App\Helpers;

use App\Services\NotificationService;

class NotificationHelper
{
    /**
     * Get notifications data for views
     */
    public static function getNotificationsData($userId)
    {
        return [
            'unreadCount' => NotificationService::getUnreadCount($userId),
            'notifications' => NotificationService::getRecentNotifications($userId, 5),
        ];
    }
}

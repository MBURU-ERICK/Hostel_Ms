<?php

namespace App\Providers;

use App\Helpers\NotificationHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Share notifications data with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $notificationData = NotificationHelper::getNotificationsData(Auth::id());
                $view->with($notificationData);
            }
        });
    }
}

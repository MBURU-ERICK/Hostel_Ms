<?php

namespace App\Http\Middleware;

use App\Helpers\NotificationHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ShareNotifications
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $notificationData = NotificationHelper::getNotificationsData(Auth::id());
            View::share($notificationData);
        }

        return $next($request);
    }
}

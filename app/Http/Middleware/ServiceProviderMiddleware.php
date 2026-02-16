<?php
// app/Http/Middleware/ServiceProviderMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ServiceProviderMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

public function handle(Request $request, Closure $next): Response
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    if (!$user->serviceProvider) {
        // Check if the user is trying to access service provider area for the first time
        if ($request->is('service-provider/*')) {
            return redirect()->route('service-provider.create')
                ->with('error', 'Please complete your service provider profile to access this area.');
        }

        // For other cases, redirect back
        return redirect()->back()->with('error', 'Access denied. Service provider access only.');
    }

    if (!$user->serviceProvider->is_verified) {
        return redirect()->route('service-provider.profile')
            ->with('warning', 'Your service provider account is pending verification. Please complete your profile and wait for approval.');
    }

    return $next($request);
}
}

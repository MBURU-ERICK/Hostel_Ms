<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->isLandlord()) {
            if ($user->isStudent()) {
                return redirect()->route('student.dashboard');
            } elseif ($user->isServiceProvider()) {
                return redirect()->route('service-provider.dashboard');
            } elseif ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect('/')->with('error', 'Access denied.');
        }

        // Check if landlord is approved and active
        if (!$user->canAccessSystem()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is pending approval or has been suspended.');
        }

        return $next($request);
    }
}

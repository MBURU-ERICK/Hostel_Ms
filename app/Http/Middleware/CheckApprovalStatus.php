<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckApprovalStatus
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is not approved (landlord or service provider)
            if (!$user->is_approved && in_array($user->user_type, ['landlord', 'service_provider'])) {
                // Allow access to logout and approval pending page
                if (!$request->is('logout') && !$request->is('approval-pending')) {
                    return redirect()->route('approval.pending');
                }
            }
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // ... other methods

    protected function authenticated(Request $request, $user)
    {
        \Log::info('User logged in', [
            'user_id' => $user->id,
            'user_type' => $user->user_type,
            'is_approved' => $user->is_approved,
            'is_active' => $user->is_active
        ]);

        // Check if user is approved and active
        if (!$user->is_approved) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account is pending approval. Please contact administrator.');
        }

        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been suspended. Please contact administrator.');
        }

        // Redirect based on user type
        switch ($user->user_type) {
            case 'landlord':
                return redirect()->route('landlord.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            case 'service_provider':
                return redirect()->route('service-provider.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
            default:
                return redirect('/')->with('error', 'Unknown user type.');
        }
    }
}

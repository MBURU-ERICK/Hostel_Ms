<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        $user = $request->user();

        // Check if user is approved
        if (!$user->is_approved) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account is pending approval. Please contact administrator.');
        }

        // Check if user is active (if the field exists)
        if (property_exists($user, 'is_active') && !$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Your account has been suspended. Please contact administrator.');
        }

        // Safe redirect based on user type
        try {
            switch ($user->user_type) {
                case 'landlord':
                    return redirect()->route('landlord.dashboard');
                case 'student':
                    return redirect()->route('student.dashboard');
                case 'service_provider':
                    return redirect()->route('service-provider.dashboard');
                case 'admin':
                    // Check if admin route exists, otherwise redirect to home
                    if (\Illuminate\Support\Facades\Route::has('admin.dashboard')) {
                        return redirect()->route('admin.dashboard');
                    }
                    return redirect('/admin'); // Fallback
                default:
                    return redirect('/');
            }
        } catch (\Exception $e) {
            // Fallback if any route fails
            \Log::error('Login redirect failed: ' . $e->getMessage());
            return redirect('/');
        }
    }
}

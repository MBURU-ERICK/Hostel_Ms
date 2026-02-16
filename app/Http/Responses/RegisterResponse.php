<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): Response|JsonResponse|RedirectResponse
    {
        $user = $request->user();

        // For API requests
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'redirect_to' => $this->getRedirectUrl($user)
            ], 201);
        }

        // For web requests
        return redirect()->intended($this->getRedirectUrl($user));
    }

    /**
     * Get the redirect URL based on user type.
     */
    protected function getRedirectUrl($user): string
    {
        return match($user->user_type) {
            'admin' => route('admin.dashboard'),
            'landlord' => route('landlord.dashboard'),
            'service_provider' => route('provider.dashboard'),
            default => config('fortify.home', '/dashboard')
        };
    }
}

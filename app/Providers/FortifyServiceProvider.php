<?php

namespace App\Providers;

use App\Actions\Fortify\ResetUserPassword;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Override where to redirect after login
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // Register the password reset action
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Override login response
        $this->app->singleton(\Laravel\Fortify\Contracts\LoginResponse::class, function () {
            return new class implements \Laravel\Fortify\Contracts\LoginResponse {
                public function toResponse($request)
                {
                    $user = Auth::user();

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
                            return redirect()->route('dashboard');
                        default:
                            return redirect('/');
                    }
                }
            };
        });
    }
}

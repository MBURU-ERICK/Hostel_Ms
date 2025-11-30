<?php

namespace App\Providers;

use App\Services\DarajaService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DarajaService::class, function ($app) {
            return new DarajaService();
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [DarajaService::class];
    }
}

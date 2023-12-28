<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EbayAccessTokenService;
use App\Contracts\AccessTokenServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(AccessTokenServiceInterface::class, EbayAccessTokenService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use App\Contracts\Portal\PortalFTPContract;
use App\Services\Portal\PortalDataProcessing;
use Illuminate\Support\ServiceProvider;

class PortalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(PortalFTPContract::class, PortalDataProcessing::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

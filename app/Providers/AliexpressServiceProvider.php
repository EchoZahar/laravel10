<?php

namespace App\Providers;

use App\Contracts\Aliexpress\SyncAliexpressCategories;
use App\Services\Aliexpress\AliexpressSyncCategoriesService;
use Illuminate\Support\ServiceProvider;

class AliexpressServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SyncAliexpressCategories::class, AliexpressSyncCategoriesService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

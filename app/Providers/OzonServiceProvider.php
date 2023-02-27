<?php

namespace App\Providers;

use App\Contracts\Ozon\SyncOzonCategories;
use App\Services\Ozon\GetOzonCategoriesProcessingAndRewrite;
use Illuminate\Support\ServiceProvider;

class OzonServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(SyncOzonCategories::class, GetOzonCategoriesProcessingAndRewrite::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

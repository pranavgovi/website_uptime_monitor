<?php

namespace App\Providers;

use App\Models\Website;
use App\Observers\WebsiteObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Website::observe(WebsiteObserver::class);
    }
}

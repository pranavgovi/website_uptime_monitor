<?php

namespace App\Observers;

use App\Models\Website;
use App\Services\WebsiteMonitorService;

class WebsiteObserver
{
    public function created(Website $website): void
    {
        app(WebsiteMonitorService::class)->monitorWebsite($website);
    }
}

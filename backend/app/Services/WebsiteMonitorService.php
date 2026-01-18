<?php

namespace App\Services;

use App\Models\Website;
use App\Notifications\WebsiteDownNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebsiteMonitorService
{
    public function monitorWebsite(Website $website): void
    {
        $previousStatus = $website->is_up;

        try {
            $response = Http::timeout(10)
                ->retry(0)
                ->get($website->url);

            $isUp = $response->successful();
            
            $website->update([
                'is_up' => $isUp,
                'last_checked_at' => now(),
            ]);

            if (!$isUp && $previousStatus === true) {
                $website->client->notify(new WebsiteDownNotification($website));
                
                Log::info("Website down alert sent", [
                    'website_id' => $website->id,
                    'url' => $website->url,
                    'client_email' => $website->client->email,
                ]);
            }

        } catch (\Exception $e) {
            $website->update([
                'is_up' => false,
                'last_checked_at' => now(),
            ]);

            if ($previousStatus === true) {
                $website->client->notify(new WebsiteDownNotification($website));
                
                Log::warning("Website monitoring error", [
                    'website_id' => $website->id,
                    'url' => $website->url,
                    'error' => $e->getMessage(),
                    'client_email' => $website->client->email,
                ]);
            }
        }
    }

    public function monitorAllWebsites(): void
    {
        $websites = Website::with('client')->get();

        foreach ($websites as $website) {
            $this->monitorWebsite($website);
        }
    }
}

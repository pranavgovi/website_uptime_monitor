<?php

namespace App\Console\Commands;

use App\Services\WebsiteMonitorService;
use Illuminate\Console\Command;

class MonitorWebsites extends Command
{
    protected $signature = 'websites:monitor';
    protected $description = 'Monitor all websites and check their status';

    public function handle(WebsiteMonitorService $monitorService)
    {
        $this->info('Starting website monitoring...');
        
        $monitorService->monitorAllWebsites();
        
        $this->info('Monitoring complete!');
        $this->newLine();
        
        $websites = \App\Models\Website::with('client')->get();
        
        $this->table(
            ['Client Email', 'URL', 'Status', 'Last Checked'],
            $websites->map(function ($website) {
                return [
                    $website->client->email,
                    $website->url,
                    $website->is_up ? '<fg=green>UP</>' : '<fg=red>DOWN</>',
                    $website->last_checked_at ? $website->last_checked_at->format('Y-m-d H:i:s') : 'Never',
                ];
            })->toArray()
        );
        
        return 0;
    }
}

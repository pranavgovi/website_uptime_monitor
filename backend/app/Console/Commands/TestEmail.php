<?php

namespace App\Console\Commands;

use App\Models\Website;
use App\Notifications\WebsiteDownNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email {email} {url}';
    protected $description = 'Test sending email notification for a down website';

    public function handle()
    {
        $email = $this->argument('email');
        $url = $this->argument('url');
        
        $this->info("Testing email notification...");
        $this->info("Email: {$email}");
        $this->info("Website URL: {$url}");
        $this->newLine();
        
        $client = \App\Models\Client::where('email', $email)->first();
        
        if (!$client) {
            $this->error("Client with email '{$email}' not found!");
            return 1;
        }
        
        $website = $client->websites()->where('url', $url)->first();
        
        if (!$website) {
            $this->error("Website '{$url}' not found for client '{$email}'!");
            return 1;
        }
        
        $this->info("Current mail driver: " . config('mail.default'));
        $this->info("Mail from: " . config('mail.from.address'));
        $this->newLine();
        
        try {
            $originalStatus = $website->is_up;
            
            $this->info("Setting website to UP first, then triggering DOWN notification...");
            $website->update(['is_up' => true]);
            $website->refresh();
            
            $this->info("Sending notification (website will be marked as DOWN)...");
            $client->notify(new WebsiteDownNotification($website));
            
            $this->newLine();
            $this->info("✓ Email notification queued/sent!");
            $this->newLine();
            
            $mailDriver = config('mail.default');
            $queueDriver = config('queue.default');
            
            $this->info("Mail Driver: {$mailDriver}");
            $this->info("Queue Driver: {$queueDriver}");
            $this->newLine();
            
            if ($mailDriver === 'log') {
                $this->info("✓ Mail driver is 'log' - check storage/logs/laravel.log for email content");
            } elseif ($queueDriver === 'sync') {
                $this->info("✓ Queue is 'sync' - email was sent immediately (if mail is configured)");
            } else {
                $this->warn("⚠ Queue is '{$queueDriver}' - emails are queued!");
                $this->warn("⚠ You need to run: php artisan queue:work");
                $this->warn("⚠ Or set QUEUE_CONNECTION=sync in .env for testing");
            }
            
            if ($mailDriver !== 'log' && $queueDriver !== 'sync') {
                $this->newLine();
                $this->comment("To check if email is queued, run:");
                $this->comment("  SELECT COUNT(*) FROM jobs;");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error sending email: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

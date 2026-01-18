<?php

namespace App\Notifications;

use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WebsiteDownNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Website $website
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = "{$this->website->url} is down!";
        
        return (new MailMessage)
            ->subject($subject)
            ->line($subject);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'website_id' => $this->website->id,
            'url' => $this->website->url,
        ];
    }
}

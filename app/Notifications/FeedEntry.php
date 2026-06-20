<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * A reusable, database-only feed entry used to mirror existing Mailable emails
 * into the in-app notification feed (N5 backfill). The email still goes out via
 * its Mailable at the call site; this only writes the feed row.
 */
class FeedEntry extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $type,
        public string $title,
        public string $body,
        public string $icon = 'bell',
        public ?string $url = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'  => $this->type,
            'title' => $this->title,
            'body'  => $this->body,
            'icon'  => $this->icon,
            'url'   => $this->url,
        ];
    }
}

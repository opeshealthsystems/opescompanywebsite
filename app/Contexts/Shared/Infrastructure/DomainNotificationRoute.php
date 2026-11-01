<?php

namespace App\Contexts\Shared\Infrastructure;

final class DomainNotificationRoute
{
    private function __construct(private string $channel, private mixed $recipient) {}
    public static function route(string $channel, mixed $recipient): self { return new self($channel, $recipient); }
    public function notify(\Illuminate\Notifications\Notification $message): void
    {
        if ($this->channel !== 'mail') throw new \LogicException('Unsupported external notification channel');
        DomainOutbox::append('notification-route', get_class($message), ['recipient' => $this->recipient, 'message' => MessageValueCodec::message($message)]);
    }
}

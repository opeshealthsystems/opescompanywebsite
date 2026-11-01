<?php

namespace App\Contexts\Shared\Infrastructure;

final class DomainMail
{
    private function __construct(private mixed $recipients) {}
    public static function to(mixed $recipients): self { return new self($recipients); }
    public function queue(\Illuminate\Mail\Mailable $message): void
    {
        $recipients = collect(is_iterable($this->recipients) ? $this->recipients : [$this->recipients])
            ->map(fn ($value) => $value instanceof \App\Models\User ? $value->email : $value)->all();
        DomainOutbox::append('mail', get_class($message), ['recipients' => $recipients, 'message' => MessageValueCodec::message($message)]);
    }
    public function send(\Illuminate\Mail\Mailable $message): void { $this->queue($message); }
}

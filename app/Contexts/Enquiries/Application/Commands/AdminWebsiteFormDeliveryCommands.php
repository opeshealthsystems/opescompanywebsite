<?php

namespace App\Contexts\Enquiries\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\WebsiteFormDelivery;
use Illuminate\Validation\ValidationException;

final class AdminWebsiteFormDeliveryCommands extends WorkflowHandler
{
    public function confirmReceived(WorkflowInput $input, string $eventId): WorkflowResult
    {
        $input->validate(['confirmed_received' => 'required|accepted']);
        $delivery = WebsiteFormDelivery::query()->where('event_id', $eventId)->lockForUpdate()->firstOrFail();
        if ($delivery->status !== 'uncertain') {
            throw ValidationException::withMessages([
                'event_id' => ['Only an ambiguous delivery can be reconciled as received.'],
            ]);
        }

        $delivery->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_type' => null,
            'next_attempt_at' => null,
            'claim_token' => null,
            'locked_at' => null,
        ]);

        DomainOutbox::append('event', 'enquiries.website_form_delivery_confirmed_received.v1', [
            'references' => [['resource' => 'WebsiteFormDelivery', 'id' => $delivery->event_id]],
            'event_id' => $delivery->event_id,
        ], $input->user()?->id);

        return WorkflowResult::payload([
            'event_id' => $delivery->event_id,
            'status' => $delivery->status,
            'sent_at' => $delivery->sent_at,
        ]);
    }
}

<?php

namespace App\Console\Commands;

use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Infrastructure\WorkflowDispatcher;
use App\Models\User;
use Illuminate\Console\Command;

final class ResolveWebsiteFormDelivery extends Command
{
    protected $signature = 'website-forms:resolve {event : Delivery event UUID} {--owner= : Active admin email acting on this operation} {--sent : Confirm this event was found in the inbox; mark sent without sending again}';
    protected $description = 'Inspect an ambiguous delivery or record operator-confirmed inbox receipt';

    public function handle(WorkflowDispatcher $workflows): int
    {
        $actor = User::query()->where('email', $this->option('owner'))->first();
        if (!$actor?->is_active || !$actor->hasAnyRole(['admin', 'super_admin'])) {
            $this->error('An active administrator owner is required.');
            return self::FAILURE;
        }

        $eventId = (string) $this->argument('event');
        $input = new WorkflowInput(
            values: $this->option('sent') ? ['confirmed_received' => true] : [],
            parameters: ['event' => $eventId],
            actor: $actor,
        );

        try {
            $result = $workflows->execute(
                $this->option('sent') ? 'admin.enquiries.deliveries.confirmReceived' : 'admin.enquiries.deliveries.show',
                $input,
            );
        } catch (\Throwable $error) {
            $this->error($error->getMessage());
            return self::FAILURE;
        }

        $this->line(json_encode(
            $result->data['delivery'] ?? $result->data,
            JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        ));
        return self::SUCCESS;
    }
}

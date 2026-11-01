<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PractitionerApplication;
use App\Models\User;
use App\Services\Payouts\PayoutGatewayManager;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

/** Admin command that accepts and durably dispatches an idempotent payout request. */
final class AdminPractitionerPayoutCommands extends WorkflowHandler
{
    public function request(WorkflowInput $input, PractitionerApplication $application): WorkflowResult
    {
        $actor = $input->user();
        abort_unless($actor?->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);

        $data = $input->validate([
            'payout_amount' => 'required|numeric|min:0.01|max:99999999.99',
            'payout_currency' => ['required', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
            'network_override' => 'nullable|in:mtn,manual',
        ]);

        $application->loadMissing(['program', 'practitioner.practitionerProfile']);
        if (! $application->isPayable()) {
            throw ValidationException::withMessages(['application' => ['Only approved, unpaid applications in paid programmes can be paid.']]);
        }

        if ($application->payout_status === 'pending' && $application->payout_reference) {
            throw ValidationException::withMessages(['payout_status' => ['A payout is already pending or requires reconciliation.']]);
        }

        $network = app(PayoutGatewayManager::class)->resolveNetwork($application, $data['network_override'] ?? null);
        if (! in_array($network, ['mtn', 'manual'], true)) {
            throw ValidationException::withMessages(['network_override' => ['This payout network is not available. Choose MTN MoMo or manual settlement.']]);
        }

        $reference = (string) Str::uuid();
        $application->update([
            'payout_status' => 'pending',
            'payout_amount' => (float) $data['payout_amount'],
            'payout_currency' => $data['payout_currency'],
            'payout_reference' => $reference,
            'payout_provider' => $network,
            'payout_initiated_at' => now(),
            'payout_failure_reason' => null,
        ]);

        DomainOutbox::append('event', 'practitioners.application_payout_requested.v1', [
            'references' => [['resource' => 'PractitionerApplication', 'id' => $application->id]],
        ], $actor->id);

        DomainOutbox::append('payout', 'practitioners.application_payout_requested.v1', [
            'application_id' => $application->id,
            'amount' => (float) $data['payout_amount'],
            'currency' => $data['payout_currency'],
            'network' => $network,
            'force_manual' => ($data['network_override'] ?? null) === 'manual',
        ], $actor->id, $reference);

        return WorkflowResult::payload([
            'id' => $application->id,
            'request_status' => 'accepted',
            'payout_reference' => $reference,
            'payout_provider' => $network,
        ], 202)->with('success', 'Payout request accepted. Check the application record for its current settlement status.');
    }
}

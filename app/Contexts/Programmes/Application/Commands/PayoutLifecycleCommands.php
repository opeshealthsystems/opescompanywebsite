<?php

namespace App\Contexts\Programmes\Application\Commands;

use App\Contexts\Shared\Infrastructure\DomainOutbox;
use App\Models\PractitionerApplication;
use Illuminate\Support\Facades\DB;

/** Shared state transitions for API, outbox and provider-poll payout adapters. */
final class PayoutLifecycleCommands
{
    public function markPaid(int $applicationId): bool
    {
        return DB::transaction(function () use ($applicationId): bool {
            $application = PractitionerApplication::query()->lockForUpdate()->findOrFail($applicationId);
            if ($application->payout_status !== 'pending') return false;

            $application->update(['payout_status' => 'paid', 'paid_at' => $application->paid_at ?? now()]);
            DomainOutbox::append('event', 'practitioners.application_payout_settled.v1', [
                'references' => [['resource' => 'PractitionerApplication', 'id' => (string) $application->id]],
                'status' => 'paid',
            ]);

            return true;
        });
    }

    public function markFailed(int $applicationId, string $reason): bool
    {
        return DB::transaction(function () use ($applicationId, $reason): bool {
            $application = PractitionerApplication::query()->lockForUpdate()->findOrFail($applicationId);
            if ($application->payout_status !== 'pending') return false;

            $application->update([
                'payout_status' => 'failed',
                'payout_failure_reason' => mb_substr(trim($reason) ?: 'Provider reported FAILED', 0, 255),
            ]);
            DomainOutbox::append('event', 'practitioners.application_payout_failed.v1', [
                'references' => [['resource' => 'PractitionerApplication', 'id' => (string) $application->id]],
                'status' => 'failed',
            ]);

            return true;
        });
    }

    public function markUncertain(int $applicationId, string $reference): bool
    {
        return DB::transaction(function () use ($applicationId, $reference): bool {
            $application = PractitionerApplication::query()->lockForUpdate()->find($applicationId);
            if (! $application || $application->payout_status !== 'pending' || $application->payout_reference !== $reference) return false;

            $application->update([
                'payout_failure_reason' => 'The provider outcome is uncertain. Reconcile this payout before retrying.',
            ]);
            DomainOutbox::append('event', 'practitioners.application_payout_reconciliation_required.v1', [
                'references' => [['resource' => 'PractitionerApplication', 'id' => (string) $application->id]],
                'status' => 'pending',
            ]);

            return true;
        });
    }
}

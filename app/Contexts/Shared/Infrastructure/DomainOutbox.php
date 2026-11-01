<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\CurrentWorkflow;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class DomainOutbox
{
    public static bool $delivering = false;

    public static function append(string $kind, string $type, array $payload, ?int $actorId = null): string
    {
        $id = (string) Str::uuid();
        DB::table('business_outbox')->insert(['id' => $id, 'kind' => $kind, 'type' => $type,
            'actor_id' => $actorId ?? CurrentWorkflow::id(), 'payload' => Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR)),
            'status' => 'pending', 'attempts' => 0, 'created_at' => now(), 'updated_at' => now()]);
        DB::afterCommit(fn () => self::deliver($id));
        app(CurrentWorkflow::class)->outboxIds[] = $id;
        return $id;
    }

    public static function deliver(string $id): string
    {
        $row = DB::table('business_outbox')->where('id', $id)->first();
        if (!$row || $row->status === 'completed') return 'already-completed';
        if ($row->status === 'processing' && $row->locked_at && \Carbon\Carbon::parse($row->locked_at)->lt(now()->subMinutes(2))) {
            DB::table('business_outbox')->where('id', $id)->where('status', 'processing')->where('claim_token', $row->claim_token)
                ->where('locked_at', '<', now()->subMinutes(2))->update(['status' => $row->kind === 'event' ? 'pending' : 'uncertain']);
            $row = DB::table('business_outbox')->where('id', $id)->first();
        }
        if ($row->status === 'uncertain') return 'uncertain';
        $claim = (string) Str::uuid();
        if (!DB::table('business_outbox')->where('id', $id)->whereIn('status', ['pending', 'failed'])
            ->where(fn ($q) => $q->whereNull('available_at')->orWhere('available_at', '<=', now()))
            ->update(['status' => 'processing', 'claim_token' => $claim, 'locked_at' => now(), 'attempts' => DB::raw('attempts + 1'), 'updated_at' => now()])) return 'deferred';
        try {
            $payload = json_decode(Crypt::decryptString($row->payload), true, 512, JSON_THROW_ON_ERROR);
            if ($row->kind === 'event') {
                DB::transaction(function () use ($id, $row, $payload) {
                    if (DB::table('business_event_inbox')->where('event_id', $id)->where('consumer', 'activity.v1')->exists()) return;
                    DB::table('business_activity')->insertOrIgnore(['id' => $id, 'actor_id' => $row->actor_id, 'operation' => $row->type,
                        'references' => json_encode($payload['references'] ?? []), 'occurred_at' => $row->created_at]);
                    DB::table('business_event_inbox')->insert(['event_id' => $id, 'consumer' => 'activity.v1', 'processed_at' => now()]);
                    event(new \App\Contexts\Shared\Domain\BusinessEvent($id, $row->type, $row->actor_id, $payload));
                });
            } else {
                self::$delivering = true;
                try {
                    $message = MessageValueCodec::restore($payload['message']);
                    if ($row->kind === 'mail') \Illuminate\Support\Facades\Mail::to($payload['recipients'])->sendNow($message);
                    elseif ($row->kind === 'notification') {
                        $message->id = $id;
                        \Illuminate\Support\Facades\Notification::sendNow(MessageValueCodec::decode($payload['recipient']), $message);
                    } elseif ($row->kind === 'notification-route') {
                        $message->id = $id;
                        \Illuminate\Support\Facades\Notification::route('mail', $payload['recipient'])->notifyNow($message);
                    } else throw new \LogicException('Unknown outbox kind');
                } finally { self::$delivering = false; }
            }
            DB::table('business_outbox')->where('id', $id)->where('claim_token', $claim)->update(['status' => 'completed', 'completed_at' => now(), 'updated_at' => now()]);
            return 'completed';
        } catch (\Throwable $error) {
            $status = $row->kind === 'event' ? 'failed' : 'uncertain';
            DB::table('business_outbox')->where('id', $id)->where('claim_token', $claim)->update(['status' => $status,
                'error_type' => get_class($error), 'available_at' => now()->addMinute(), 'updated_at' => now()]);
            \Illuminate\Support\Facades\Log::warning('Business outbox requires recovery', ['id' => $id, 'kind' => $row->kind, 'error_type' => get_class($error)]);
            return $status;
        }
    }
}

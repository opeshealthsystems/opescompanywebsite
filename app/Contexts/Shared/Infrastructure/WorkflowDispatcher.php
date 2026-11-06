<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\CurrentWorkflow;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

final class WorkflowDispatcher
{
    public function execute(string $operation, WorkflowInput $input, ?string $key = null): WorkflowResult
    {
        $definition = config('business_workflows.definitions', [])[$operation] ?? throw new \LogicException('Unknown business operation');
        $user = $input->user();
        if ($definition['authenticated']) {
            abort_unless($user && $user->is_active, 403);
            if ($definition['roles']) abort_unless($user->hasAnyRole($definition['roles']), 403);
            if ($input->api) abort_unless(in_array($definition['scope'], $input->scopes, true), 403);
        }
        $context = app(CurrentWorkflow::class);
        $previous = [$context->input, $context->operation, $context->command, $context->changes, $context->outboxIds];
        $context->input = $input; $context->operation = $operation; $context->command = $definition['command']; $context->changes = []; $context->outboxIds = [];
        try {
            if (!$definition['command']) return $this->invoke($definition, $input);
            if ($input->api && ($key === null || !preg_match('/\A[A-Za-z0-9._:-]{16,128}\z/', $key))) {
                throw \Illuminate\Validation\ValidationException::withMessages(['idempotency_key' => ['A valid Idempotency-Key is required for this operation.']]);
            }
            try {
            $result = DB::transaction(function () use ($operation, $definition, $input, $key, $context) {
                $context->changes = []; $context->outboxIds = [];
                if ($input->user()) {
                    $lockedActor = \App\Models\User::query()->lockForUpdate()->findOrFail($input->user()->id);
                    abort_unless($lockedActor->is_active, 403);
                }
                $hash = $key === null ? null : hash('sha256', implode('|', [$operation, $input->user()?->id ?? 'anonymous', $input->consumerId ?? 'web', $key]));
                $fingerprint = hash_hmac('sha256', json_encode($this->canonical([$input->all(), $input->route()]), JSON_THROW_ON_ERROR), (string) config('app.key'));
                if ($hash !== null) {
                    DB::table('business_command_receipts')->insertOrIgnore(['key_hash' => $hash, 'fingerprint' => $fingerprint,
                        'actor_id' => $input->user()?->id, 'operation' => $operation, 'outcome' => '', 'expires_at' => now()->addDay(), 'created_at' => now(), 'updated_at' => now()]);
                    $receipt = DB::table('business_command_receipts')->where('key_hash', $hash)->lockForUpdate()->first();
                    if (\Carbon\Carbon::parse($receipt->expires_at)->isPast()) {
                        DB::table('business_command_receipts')->where('id', $receipt->id)->update(['outcome' => '', 'fingerprint' => $fingerprint, 'expires_at' => now()->addDay(), 'updated_at' => now()]);
                        $receipt->outcome = ''; $receipt->fingerprint = $fingerprint;
                    }
                    abort_unless(hash_equals($receipt->fingerprint, $fingerprint), 409);
                    if ($receipt->outcome !== '') {
                        $saved = json_decode(Crypt::decryptString($receipt->outcome), true, 512, JSON_THROW_ON_ERROR);
                        $result = match ($saved['kind']) {
                            'next' => WorkflowResult::next($saved['variant'], $saved['data']),
                            'empty' => WorkflowResult::empty($saved['status']),
                            default => WorkflowResult::payload($saved['data'], $saved['status']),
                        };
                        $result->status = $saved['status'];
                        $result->messages = $saved['messages']; $result->errors = $saved['errors'];
                        $result->replayed = true;
                        return $result;
                    }
                }
                $result = $this->invoke($definition, $input);
                if ($result->errors || isset($result->messages['error'])) throw new \App\Contexts\Shared\Application\RejectedWorkflow($result);
                DomainOutbox::append('event', $operation.'.completed.v1', ['references' => $context->changes]);
                if ($hash !== null) {
                    $stored = ['kind' => $result->kind, 'variant' => $result->variant, 'data' => $this->canonical($result->data),
                        'messages' => $result->messages, 'errors' => $result->errors, 'status' => $result->status];
                    DB::table('business_command_receipts')->where('key_hash', $hash)->update(['outcome' => Crypt::encryptString(json_encode($stored, JSON_THROW_ON_ERROR)), 'updated_at' => now()]);
                }
                return $result;
            }, 3);
            } catch (\App\Contexts\Shared\Application\RejectedWorkflow $rejection) { return $rejection->result; }
            // RefreshDatabase uses an enclosing rollback transaction. Exercise post-commit consumers against fakes inside that test transaction.
            if (app()->runningUnitTests()) foreach ($context->outboxIds as $id) DomainOutbox::deliver($id);
            return $result;
        } finally {
            [$context->input, $context->operation, $context->command, $context->changes, $context->outboxIds] = $previous;
        }
    }

    private function invoke(array $definition, WorkflowInput $input): WorkflowResult
    {
        $arguments = [];
        foreach ($definition['parameters'] as $parameter) {
            if ($parameter['kind'] === 'input') { $arguments[] = $input; continue; }
            $value = $parameter['name'] === 'locale' ? $input->locale() : $input->route($parameter['route_key'] ?? $parameter['name']);
            if ($parameter['kind'] === 'model') {
                $class = $parameter['class'];
                $query = $class::where($parameter['key'], $value instanceof $class ? $value->getAttribute($parameter['key']) : $value);
                if ($definition['command']) $query->lockForUpdate();
                $value = $query->firstOrFail();
            }
            $arguments[] = $value;
        }
        $result = app($definition['handler'])->{$definition['method']}(...$arguments);
        if (!$result instanceof WorkflowResult) throw new \LogicException('Business operation did not return an application outcome');
        return $result;
    }

    private function canonical(mixed $value): mixed
    {
        if ($value instanceof \Illuminate\Database\Eloquent\Model) return ['type' => class_basename($value), 'id' => $value->getKey()];
        if ($value instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) return ['file_hash' => hash_file('sha256', $value->getRealPath()), 'size' => $value->getSize()];
        if (is_array($value)) {
            if (!array_is_list($value)) ksort($value);
            return array_map(fn ($item) => $this->canonical($item), $value);
        }
        if ($value instanceof \DateTimeInterface) return $value->format(DATE_ATOM);
        return $value;
    }
}

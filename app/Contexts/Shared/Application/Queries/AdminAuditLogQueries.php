<?php

namespace App\Contexts\Shared\Application\Queries;

use App\Contexts\Shared\Application\WorkflowHandler;
use App\Contexts\Shared\Application\WorkflowInput;
use App\Contexts\Shared\Application\WorkflowResult;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/** Protected audit-log reads shared by Filament and delegated API clients. */
final class AdminAuditLogQueries extends WorkflowHandler
{
    private function admin(WorkflowInput $input): void
    {
        $actor = $input->user();
        abort_unless($actor instanceof User && $actor->is_active && $actor->hasAnyRole(['super_admin', 'admin']), 403);
    }

    public function query(WorkflowInput $input): Builder
    {
        $this->admin($input);

        return AuditLog::query()->with('user')->orderByDesc('created_at');
    }

    private function filters(WorkflowInput $input): array
    {
        return $input->validate([
            'action' => 'sometimes|string|max:80',
            'model_type' => 'sometimes|string|max:100',
            'user_id' => 'sometimes|integer|exists:users,id',
            'from' => 'sometimes|date',
            'until' => 'sometimes|date',
            'search' => 'sometimes|nullable|string|max:150',
        ]);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        foreach (['action', 'model_type', 'user_id'] as $field) {
            if (isset($filters[$field])) $query->where($field, $filters[$field]);
        }
        if (isset($filters['from'])) $query->whereDate('created_at', '>=', $filters['from']);
        if (isset($filters['until'])) $query->whereDate('created_at', '<=', $filters['until']);
        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(fn (Builder $logs) => $logs
                ->where('action', 'like', '%'.$search.'%')
                ->orWhere('model_type', 'like', '%'.$search.'%')
                ->orWhere('ip_address', 'like', '%'.$search.'%')
                ->orWhereHas('user', fn (Builder $users) => $users->where('name', 'like', '%'.$search.'%')));
        }

        return $query;
    }

    private function item(AuditLog $log, bool $includeValues = false): array
    {
        $item = [
            'id' => $log->id,
            'user_id' => $log->user_id,
            'user_name' => $log->user?->name,
            'action' => $log->action,
            'model_type' => $log->model_type,
            'model_id' => $log->model_id,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'created_at' => $log->created_at?->toISOString(),
        ];

        if ($includeValues) {
            $item['old_values'] = $this->redact($log->old_values ?? []);
            $item['new_values'] = $this->redact($log->new_values ?? []);
        }

        return $item;
    }

    private function redact(mixed $value): mixed
    {
        if (!is_array($value)) return $value;

        $sensitive = ['password', 'remember_token', 'token', 'access_token', 'refresh_token', 'secret', 'client_secret', 'api_key', 'authorization'];
        foreach ($value as $key => $item) {
            if (is_string($key) && in_array(strtolower($key), $sensitive, true)) $value[$key] = '[redacted]';
            else $value[$key] = $this->redact($item);
        }

        return $value;
    }

    public function index(WorkflowInput $input): WorkflowResult
    {
        $this->admin($input);
        $query = $this->applyFilters($this->query($input), $this->filters($input));
        $page = $input->paginate($query);

        return WorkflowResult::payload([
            'items' => $page->getCollection()->map(fn (AuditLog $log) => $this->item($log))->all(),
            'pagination' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }

    public function show(WorkflowInput $input, AuditLog $auditLog): WorkflowResult
    {
        $this->admin($input);

        return WorkflowResult::payload(['audit_log' => $this->item($auditLog->loadMissing('user'), includeValues: true)]);
    }
}

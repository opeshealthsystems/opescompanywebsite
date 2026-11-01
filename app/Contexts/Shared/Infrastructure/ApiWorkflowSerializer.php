<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/** Explicit field contracts; never return a model's toArray() or hidden-sensitive attributes. */
final class ApiWorkflowSerializer
{
    public function references(mixed $value): mixed
    {
        if ($value instanceof Model) return ['type' => class_basename($value), 'id' => $value->getKey()];
        if ($value instanceof \DateTimeInterface) return $value->format(DATE_ATOM);
        if (is_array($value)) return array_map($this->references(...), $value);
        return $value;
    }

    public function serialize(mixed $value, WorkflowInput $input, int $depth = 0): mixed
    {
        if ($depth > 5) return null;
        if ($value instanceof \DateTimeInterface) return $value->format(DATE_ATOM);
        if ($value instanceof \BackedEnum) return $value->value;
        if ($value instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator) return [
            'items' => $this->serialize($value->items(), $input, $depth + 1),
            'meta' => ['page' => $value->currentPage(), 'per_page' => $value->perPage(), 'total' => $value->total(), 'last_page' => $value->lastPage()],
        ];
        if ($value instanceof \Illuminate\Support\Collection) return $this->serialize($value->take(100)->all(), $input, $depth + 1);
        if ($value instanceof Model) {
            if ($value instanceof \App\Models\TicketReply && $value->is_internal && !$input->user()?->hasAnyRole(['support', 'admin', 'super_admin'])) return null;
            $contract = config('api_resources', [])[get_class($value)] ?? null;
            if ($contract === null) throw new \LogicException('Missing API resource field contract: '.get_class($value));
            $fields = $input->user() ? $contract['private'] : $contract['public'];
            if (!array_key_exists('id', $value->getAttributes())) $fields = array_merge($fields, $contract['aggregates'] ?? []);
            if ($value instanceof \App\Models\License && $input->user() && ((string) $value->user_id === (string) $input->user()->id || $input->user()->hasAnyRole(['admin', 'super_admin', 'support']))) $fields[] = 'license_key';
            if ($value instanceof \App\Models\User) {
                $fields = ['id', 'name'];
                if ($input->user() && (string) $input->user()->id === (string) $value->getKey()) $fields = array_merge($fields, ['email', 'phone', 'is_active', 'position', 'avatar', 'employee_id', 'department_id']);
                if ($input->user()?->hasAnyRole(['hr', 'manager', 'accountant', 'admin', 'super_admin']) && $value->employee_id !== null) {
                    $fields = array_merge($fields, ['email', 'phone', 'employee_id', 'position', 'hire_date', 'is_active', 'department_id']);
                }
            }
            if ($value instanceof \App\Models\EmployeeProfile && !$input->user()?->hasAnyRole(['hr', 'accountant', 'admin', 'super_admin']) && (string) $input->user()?->id !== (string) $value->user_id) {
                $fields = array_values(array_diff($fields, ['salary', 'currency', 'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation', 'notes']));
            }
            $data = [];
            foreach ($fields as $field) {
                if (array_key_exists($field, $value->getAttributes()) || $value->hasGetMutator($field)) $data[$field] = $this->serialize($value->getAttribute($field), $input, $depth + 1);
            }
            foreach ($contract['relations'] as $relation) {
                if ($relation === 'replies' && !$input->user()?->hasAnyRole(['support', 'admin', 'super_admin'])) continue;
                if ($value->relationLoaded($relation)) $data[Str::snake($relation)] = $this->serialize($value->getRelation($relation), $input, $depth + 1);
            }
            return $data;
        }
        if (is_array($value)) {
            $data = [];
            foreach ($value as $key => $item) $data[is_string($key) ? Str::snake($key) : $key] = $this->serialize($item, $input, $depth + 1);
            return $data;
        }
        if (is_scalar($value) || $value === null) return $value;
        throw new \LogicException('API output requires an explicit value mapper: '.get_debug_type($value));
    }
}

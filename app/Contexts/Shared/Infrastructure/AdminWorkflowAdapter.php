<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/** Filament transport adapter for the same application commands used by APIs. */
final class AdminWorkflowAdapter
{
    /**
     * Run the same role-checked application query used by API operations from a
     * Filament read adapter. Relation managers must not invent parallel reads.
     *
     * @param array<int, mixed> $arguments Arguments after WorkflowInput, usually owner records.
     */
    public static function query(string $handler, string $method, array $arguments = []): Builder
    {
        $query = app($handler)->{$method}(
            new WorkflowInput(values: request()->query(), actor: Auth::user(), language: app()->getLocale()),
            ...$arguments,
        );

        if (! $query instanceof Builder) {
            throw new \LogicException('Application query ['.$handler.'::'.$method.'] did not return an Eloquent builder.');
        }

        return $query;
    }

    /** Execute an application read operation from a server-rendered UI adapter. */
    public static function read(string $operation, array $values = [], array $parameters = []): array
    {
        $result = app(WorkflowDispatcher::class)->execute(
            $operation,
            new WorkflowInput(values: $values, parameters: $parameters, actor: Auth::user(), language: app()->getLocale()),
        );

        return $result->data;
    }

    public static function record(string $operation, array $values = [], array $parameters = []): Model
    {
        $result = app(WorkflowDispatcher::class)->execute($operation, new WorkflowInput(values: $values, parameters: $parameters, actor: Auth::user()));
        foreach (['record', 'employee', 'assessment', 'user'] as $recordKey) {
            if (($record = $result->data[$recordKey] ?? null) instanceof Model) {
                return $record;
            }
        }
        // Some commands return a named read model (for example `employee`) instead of
        // exposing their Eloquent model in the API response. For Filament's record
        // actions, resolve the route model from the operation contract after dispatch.
        $definitions = config('business_workflows.definitions', []);
        $definition = $definitions[$operation] ?? [];
        $recordModel = null;
        $recordClass = null;
        $recordId = null;
        foreach (array_reverse($definition['parameters'] ?? []) as $parameter) {
            if (($parameter['kind'] ?? null) !== 'model' || ! isset($parameters[$parameter['name']])) {
                continue;
            }

            $value = $parameters[$parameter['name']];
            if ($value instanceof Model) {
                $recordModel = $value;
                $recordClass = get_class($value);
                $recordId = $value->getKey();
                break;
            }

            $class = $parameter['class'] ?? null;
            if (is_string($class) && is_subclass_of($class, Model::class)) {
                $recordClass = $class;
                $recordId = $value;
                break;
            }
        }

        $recordData = $result->data['employee'] ?? $result->data['assessment'] ?? $result->data['record'] ?? $result->data;
        if ($recordModel instanceof Model) {
            $attributes = is_array($recordData) ? array_intersect_key($recordData, array_flip($recordModel->getFillable())) : [];
            if ($attributes) $recordModel->fill($attributes);
            return $recordModel;
        }

        $resourceName = $definition['source_controller'] ?? null;
        if ($recordClass === null && is_string($resourceName) && preg_match('/\A[A-Za-z][A-Za-z0-9]*Resource\z/', $resourceName)) {
            $resourceClass = 'App\\Filament\\Resources\\'.$resourceName;
            if (class_exists($resourceClass) && is_subclass_of($resourceClass, \Filament\Resources\Resource::class)) {
                $modelClass = $resourceClass::getModel();
                if (is_string($modelClass) && is_subclass_of($modelClass, Model::class)) $recordClass = $modelClass;
            }
        }

        $recordId ??= is_array($recordData) ? ($recordData['id'] ?? null) : null;
        if (is_string($recordClass) && is_subclass_of($recordClass, Model::class) && $recordId !== null) {
            $model = new $recordClass();
            $attributes = is_array($recordData) ? array_intersect_key($recordData, array_flip($model->getFillable())) : [];
            if ($attributes) $model->fill($attributes);
            $model->setAttribute($model->getKeyName(), $recordId);
            $model->exists = true;
            return $model;
        }

        throw new \LogicException("Workflow [$operation] did not return a record for this Filament action.");
    }

    public static function run(string $operation, array $values = [], array $parameters = []): void
    {
        app(WorkflowDispatcher::class)->execute($operation, new WorkflowInput(values: $values, parameters: $parameters, actor: Auth::user()));
    }
}

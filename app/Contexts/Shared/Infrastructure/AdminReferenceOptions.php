<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Support\Collection;

/** Filament adapter for the same protected reference-data queries exposed by the API. */
final class AdminReferenceOptions
{
    public static function items(string $operation, array $values = []): array
    {
        return self::data($operation, $values)['items'] ?? [];
    }

    public static function data(string $operation, array $values = []): array
    {
        $result = app(WorkflowDispatcher::class)->execute($operation,
            new WorkflowInput(values: $values, actor: auth()->user(), language: app()->getLocale()));
        return $result->data;
    }

    public static function routeData(string $operation, array $parameters = [], array $values = []): array
    {
        $result = app(WorkflowDispatcher::class)->execute($operation,
            new WorkflowInput(values: $values, parameters: $parameters, actor: auth()->user(), language: app()->getLocale()));
        return $result->data;
    }

    public static function options(string $operation, array $values = [], string $labelKey = 'name'): array
    {
        return Collection::make(self::items($operation, $values))
            ->mapWithKeys(fn (array $item) => [$item['id'] => $item[$labelKey] ?? ''])
            ->all();
    }
}

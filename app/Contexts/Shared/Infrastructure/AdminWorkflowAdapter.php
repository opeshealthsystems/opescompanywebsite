<?php

namespace App\Contexts\Shared\Infrastructure;

use App\Contexts\Shared\Application\WorkflowInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/** Filament transport adapter for the same application commands used by APIs. */
final class AdminWorkflowAdapter
{
    public static function record(string $operation, array $values = [], array $parameters = []): Model
    {
        $result = app(WorkflowDispatcher::class)->execute($operation, new WorkflowInput(values: $values, parameters: $parameters, actor: Auth::user()));
        return $result->data['record'];
    }

    public static function run(string $operation, array $values = [], array $parameters = []): void
    {
        app(WorkflowDispatcher::class)->execute($operation, new WorkflowInput(values: $values, parameters: $parameters, actor: Auth::user()));
    }
}

<?php

namespace App\Contexts\Shared\Application;

abstract class WorkflowHandler
{
    protected function actor(): ?\App\Models\User { return CurrentWorkflow::actor(); }
    protected function authorize(string $ability, mixed $arguments): mixed
    {
        return \Illuminate\Support\Facades\Gate::forUser($this->actor())->authorize($ability, $arguments);
    }
}

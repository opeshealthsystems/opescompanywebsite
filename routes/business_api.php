<?php

use App\Http\Controllers\Api\V1\BusinessWorkflowController;
use App\Http\Middleware\AuthenticateBusinessUser;
use Illuminate\Support\Facades\Route;

// Static paths precede variable record paths; caller input never chooses a handler.
$operations = config('business_workflows.definitions', []);
uasort($operations, fn ($a, $b) => substr_count($a['api_path'], '{') <=> substr_count($b['api_path'], '{'));
foreach ($operations as $operation => $definition) {
    Route::match($definition['http_methods'], $definition['api_path'], BusinessWorkflowController::class)
        ->defaults('business_operation', $operation)
        ->middleware($definition['authenticated'] ? [AuthenticateBusinessUser::class, 'throttle:api-consumer'] : ['throttle:public-enquiries'])
        ->name('api.v1.business.'.$operation);
}

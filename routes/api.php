<?php
use App\Http\Controllers\Api\V1\EnquiryController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    require __DIR__.'/business_api.php';
    Route::get('me/activity', \App\Http\Controllers\Api\V1\BusinessActivityController::class)->middleware([\App\Http\Middleware\AuthenticateBusinessUser::class, 'throttle:api-consumer']);
    Route::post('oauth/user-token', \App\Http\Controllers\Api\V1\UserTokenController::class)->middleware('throttle:oauth-token')->name('api.v1.oauth.user-token');
    Route::post('oauth/token', \App\Http\Controllers\Api\V1\MachineTokenController::class)->middleware('throttle:oauth-token')->name('api.v1.oauth.token');
    Route::get('integration', \App\Http\Controllers\Api\V1\IntegrationController::class)->middleware([\App\Http\Middleware\AuthenticateApiClient::class.':integration:read', 'throttle:api-consumer']);
    Route::get('integration/usage', \App\Http\Controllers\Api\V1\IntegrationUsageController::class)->middleware([\App\Http\Middleware\AuthenticateApiClient::class.':integration:read', 'throttle:api-consumer'])->name('api.v1.integration.usage');
    Route::get('catalogue/products', \App\Http\Controllers\Api\V1\CatalogueController::class)->middleware([\App\Http\Middleware\AuthenticateApiClient::class.':catalogue:read', 'throttle:api-consumer']);
    Route::get('openapi.json', fn () => response()->file(base_path('docs/api/openapi.json'), ['Content-Type' => 'application/json']))->name('api.v1.contract');
    Route::post('enquiries', EnquiryController::class)->middleware('throttle:public-enquiries')->name('api.v1.enquiries.store');
});

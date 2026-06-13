<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Send bare root to the default locale.
Route::get('/', fn () => redirect('/'.config('locale.default')));

Route::prefix('{locale}')
    ->where(['locale' => implode('|', config('locale.supported'))])
    ->middleware('setlocale')
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
    });

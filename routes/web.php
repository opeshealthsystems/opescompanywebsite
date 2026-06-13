<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Customer\DashboardController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Bare root → default locale
Route::get('/', fn () => redirect('/'.config('locale.default')));
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ── Auth (non-locale-prefixed so Laravel's Authenticate middleware can redirect to route('login')) ──
Route::middleware('guest')->group(function () {
    Route::get('/login',     [LoginController::class,    'show'])->name('login');
    Route::post('/login',    [LoginController::class,    'authenticate'])->name('login.post');
    Route::get('/register',  [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Document routes (auth required — admin and customer PDF download)
Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/pdf',  [\App\Http\Controllers\DocumentController::class, 'pdf'])->name('documents.pdf');
    Route::get('/documents/{document}/view', [\App\Http\Controllers\DocumentController::class, 'preview'])->name('documents.preview');
});

// Public document signing (no auth — token-based)
Route::get('/documents/signed/{reference}', function ($reference) {
    return view('documents.sign-success', ['reference' => $reference]);
})->name('documents.sign.success');
Route::get('/documents/{token}/sign',  [\App\Http\Controllers\DocumentSigningController::class, 'show'])->name('documents.sign');
Route::post('/documents/{token}/sign', [\App\Http\Controllers\DocumentSigningController::class, 'sign'])->name('documents.sign.submit');

// ── Locale-prefixed routes ──────────────────────────────────────────────────
Route::prefix('{locale}')
    ->where(['locale' => implode('|', config('locale.supported'))])
    ->middleware('setlocale')
    ->group(function () {

        // Public marketing pages
        Route::get('/',               [HomeController::class,    'index'])->name('home');
        Route::get('/products',       [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{slug}',[ProductController::class, 'show'])->name('product.show');
        Route::get('/contact',        [ContactController::class, 'show'])->name('contact');
        Route::post('/contact',       [ContactController::class, 'submit'])->name('contact.submit');
        Route::get('/solutions',      fn () => view('pages.solutions'))->name('solutions');
        Route::get('/about',          fn () => view('pages.about'))->name('about');
        Route::get('/blog',           [BlogController::class,    'index'])->name('blog');
        Route::get('/blog/{slug}',    [BlogController::class,    'show'])->name('blog.show');
        Route::get('/partnerships',   fn () => view('pages.partnerships'))->name('partnerships');

        // Customer portal (auth + customer role required)
        Route::middleware(['auth', 'role:customer'])
            ->prefix('customer')
            ->name('customer.')
            ->group(function () {
                Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
                Route::get('/profile',   [ProfileController::class,   'show'])->name('profile');
                Route::put('/profile',   [ProfileController::class,   'update'])->name('profile.update');
                Route::get('/documents',      [\App\Http\Controllers\Customer\DocumentController::class, 'index'])->name('documents');
                Route::get('/documents/{id}', [\App\Http\Controllers\Customer\DocumentController::class, 'show'])->name('documents.show');
                Route::get('/licenses',      [\App\Http\Controllers\Customer\LicenseController::class, 'index'])->name('licenses');
                Route::get('/licenses/{id}', [\App\Http\Controllers\Customer\LicenseController::class, 'show'])->name('licenses.show');
            });
    });

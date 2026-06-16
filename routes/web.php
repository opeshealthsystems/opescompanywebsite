<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
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

    Route::get('/forgot-password',   [ForgotPasswordController::class,  'show'])->name('password.request');
    Route::post('/forgot-password',  [ForgotPasswordController::class,  'submit'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset.form');
    Route::post('/reset-password',   [ResetPasswordController::class,   'reset'])->name('password.reset');

    Route::get('/practitioners/register',  [\App\Http\Controllers\Auth\PractitionerRegisterController::class, 'show'])->name('practitioner.register');
    Route::post('/practitioners/register', [\App\Http\Controllers\Auth\PractitionerRegisterController::class, 'register'])->name('practitioner.register.post');
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Document routes (auth required — admin and customer PDF download)
Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/pdf',  [\App\Http\Controllers\DocumentController::class, 'pdf'])->name('documents.pdf');
    Route::get('/documents/{document}/view', [\App\Http\Controllers\DocumentController::class, 'preview'])->name('documents.preview');
    Route::get('/invoices/{invoice}/pdf', [\App\Http\Controllers\InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::get('/payroll/entries/{entry}/payslip', [PayrollController::class, 'payslip'])->name('payroll.payslip');
    Route::get('/contracts/{contract}/pdf', [\App\Http\Controllers\ContractController::class, 'pdf'])->name('contracts.pdf');
    Route::get('/quotes/{quote}/pdf', [\App\Http\Controllers\QuoteController::class, 'pdf'])->name('quotes.pdf');
    Route::get('/purchase-orders/{purchaseOrder}/pdf', [\App\Http\Controllers\PurchaseOrderController::class, 'pdf'])->name('purchase-orders.pdf');
    Route::get('/supplier-bills/{supplierBill}/pdf', [\App\Http\Controllers\SupplierBillController::class, 'pdf'])->name('supplier-bills.pdf');
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
        Route::get('/pricing',        fn () => view('pages.pricing'))->name('pricing');
        Route::get('/privacy',        fn () => view('pages.privacy'))->name('privacy');
        Route::get('/terms',          fn () => view('pages.terms'))->name('terms');
        Route::get('/compliance',     fn () => view('pages.compliance'))->name('compliance');

        // Customer portal (auth + customer role required)
        Route::middleware(['auth', 'role:customer'])
            ->prefix('customer')
            ->name('customer.')
            ->group(function () {
                Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
                Route::get('/profile',        [ProfileController::class, 'show'])->name('profile');
                Route::put('/profile',        [ProfileController::class, 'update'])->name('profile.update');
                Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
                Route::get('/documents',      [\App\Http\Controllers\Customer\DocumentController::class, 'index'])->name('documents');
                Route::get('/documents/{id}', [\App\Http\Controllers\Customer\DocumentController::class, 'show'])->name('documents.show');
                Route::get('/licenses',      [\App\Http\Controllers\Customer\LicenseController::class, 'index'])->name('licenses');
                Route::get('/licenses/{id}', [\App\Http\Controllers\Customer\LicenseController::class, 'show'])->name('licenses.show');
                Route::get('/invoices',      [\App\Http\Controllers\Customer\InvoiceController::class, 'index'])->name('invoices');
                Route::get('/invoices/{id}', [\App\Http\Controllers\Customer\InvoiceController::class, 'show'])->name('invoices.show');
                Route::get('/tickets',             [\App\Http\Controllers\Customer\TicketController::class, 'index'])->name('tickets');
                Route::get('/tickets/create',      [\App\Http\Controllers\Customer\TicketController::class, 'create'])->name('tickets.create');
                Route::post('/tickets',            [\App\Http\Controllers\Customer\TicketController::class, 'store'])->name('tickets.store');
                Route::get('/tickets/{id}',        [\App\Http\Controllers\Customer\TicketController::class, 'show'])->name('tickets.show');
                Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Customer\TicketController::class, 'reply'])->name('tickets.reply');
                Route::get('/knowledge-base', [\App\Http\Controllers\Customer\KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
                Route::get('/knowledge-base/category/{slug}', [\App\Http\Controllers\Customer\KnowledgeBaseController::class, 'category'])->name('knowledge-base.category');
                Route::get('/knowledge-base/{slug}', [\App\Http\Controllers\Customer\KnowledgeBaseController::class, 'show'])->name('knowledge-base.show');
            });

        // Tester portal (auth + tester role required)
        Route::middleware(['auth', 'role:tester'])
            ->prefix('tester')
            ->name('tester.')
            ->group(function () {
                Route::get('/dashboard',                             [\App\Http\Controllers\Tester\DashboardController::class,  'index'])->name('dashboard');
                Route::get('/assignments',                           [\App\Http\Controllers\Tester\AssignmentController::class, 'index'])->name('assignments');
                Route::get('/assignments/{id}',                      [\App\Http\Controllers\Tester\AssignmentController::class, 'show'])->name('assignments.show');
                Route::patch('/assignments/{id}/status',             [\App\Http\Controllers\Tester\AssignmentController::class, 'updateStatus'])->name('assignments.status');
                Route::post('/assignments/{id}/bug-reports',         [\App\Http\Controllers\Tester\AssignmentController::class, 'storeBugReport'])->name('assignments.bug-reports');
            });

        // Practitioner portal
        Route::middleware(['auth', 'role:practitioner'])
            ->prefix('practitioner')
            ->name('practitioner.')
            ->group(function () {
                Route::get('/dashboard', [\App\Http\Controllers\Practitioner\DashboardController::class, 'index'])->name('dashboard');
                Route::get('/profile',   [\App\Http\Controllers\Practitioner\ProfileController::class,   'show'])->name('profile');
                Route::put('/profile',   [\App\Http\Controllers\Practitioner\ProfileController::class,   'update'])->name('profile.update');
                Route::put('/profile/password', [\App\Http\Controllers\Practitioner\ProfileController::class, 'changePassword'])->name('profile.password');
            });
    });

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
use App\Http\Controllers\PractitionerLandingController;
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
    Route::get('/certificates/{certificate}/download', [\App\Http\Controllers\CertificatePdfController::class, 'download'])->name('certificates.pdf');
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
        Route::get('/products/{slug}/brochure', [\App\Http\Controllers\BrochureController::class, 'download'])->name('product.brochure');
        Route::get('/contact',        [ContactController::class, 'show'])->name('contact');
        Route::post('/contact',       [ContactController::class, 'submit'])->name('contact.submit');
        Route::get('/solutions',      fn () => view('pages.solutions'))->name('solutions');
        Route::get('/about',          fn () => view('pages.about'))->name('about');
        Route::get('/blog',              [BlogController::class, 'index'])->name('blog');
        Route::get('/blog/{slug}',       [BlogController::class, 'show'])->name('blog.show');
        Route::post('/blog/{slug}/like',    [BlogController::class, 'like'])->name('blog.like');
        Route::post('/blog/{slug}/share',   [BlogController::class, 'share'])->name('blog.share');
        Route::post('/blog/{slug}/comment', [BlogController::class, 'comment'])->name('blog.comment');
        Route::get('/courses',           [\App\Http\Controllers\PublicCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course:slug}', [\App\Http\Controllers\PublicCourseController::class, 'show'])->name('courses.show');
        Route::get('/partnerships',   [\App\Http\Controllers\PartnershipsController::class, 'index'])->name('partnerships');
        Route::get('/pricing',        fn () => view('pages.pricing'))->name('pricing');
        Route::get('/privacy',        fn () => view('pages.privacy'))->name('privacy');
        Route::get('/terms',          fn () => view('pages.terms'))->name('terms');
        Route::get('/compliance',       fn () => view('pages.compliance'))->name('compliance');
        Route::get('/architecture',     fn () => view('pages.architecture'))->name('architecture');
        Route::get('/implementation',   fn () => view('pages.implementation'))->name('implementation');
        Route::get('/support',          fn () => view('pages.support'))->name('support');
        Route::get('/academy',              fn () => view('pages.academy'))->name('academy');
        Route::get('/clinical-governance',  fn () => view('pages.clinical-governance'))->name('clinical-governance');
        Route::get('/interoperability',     fn () => view('pages.interoperability'))->name('interoperability');
        Route::get('/quality',              fn () => view('pages.quality'))->name('quality');
        Route::get('/national-platform',    fn () => view('pages.national-platform'))->name('national-platform');
        Route::get('/partner-program',      fn () => view('pages.partner-program'))->name('partner-program');
        Route::get('/health-os',            fn () => view('pages.health-os'))->name('health-os');
        Route::get('/architecture-diagrams', fn () => view('pages.architecture-diagrams'))->name('architecture-diagrams');
        Route::get('/faq',                  fn () => view('pages.faq'))->name('faq');
        Route::get('/book-demo',            [\App\Http\Controllers\DemoRequestController::class, 'show'])->name('book-demo');
        Route::post('/book-demo',           [\App\Http\Controllers\DemoRequestController::class, 'submit'])->name('book-demo.submit');
        Route::get('/become-a-partner',     [\App\Http\Controllers\PartnerApplicationController::class, 'show'])->name('become-a-partner');
        Route::post('/become-a-partner',    [\App\Http\Controllers\PartnerApplicationController::class, 'submit'])->name('become-a-partner.submit');
        Route::get('/join-testers',         [\App\Http\Controllers\TesterApplicationController::class, 'show'])->name('join-testers');
        Route::post('/join-testers',        [\App\Http\Controllers\TesterApplicationController::class, 'submit'])->name('join-testers.submit');
        Route::get('/mobile-clinic',        fn () => view('pages.mobile-clinic'))->name('mobile-clinic');

        // Confidential — admin / super_admin only (not customer or support)
        Route::middleware(['auth', 'role:admin|super_admin'])
            ->group(function () {
                Route::get('/strategy',        fn () => view('pages.strategy'))->name('strategy');
                Route::get('/risk',            fn () => view('pages.risk'))->name('risk');
                Route::get('/financial-model', fn () => view('pages.financial-model'))->name('financial-model');
                Route::get('/sales-playbook',       fn () => view('pages.sales-playbook'))->name('sales-playbook');
                Route::get('/government-proposal', fn () => view('pages.government-proposal'))->name('government-proposal');
                Route::get('/investor-pitch',      fn () => view('pages.investor-pitch'))->name('investor-pitch');
            });
        // Public practitioner directory
        Route::get('/practitioners',      [\App\Http\Controllers\Public\PractitionerDirectoryController::class, 'index'])->name('practitioners.index');
        Route::get('/practitioners/{id}', [\App\Http\Controllers\Public\PractitionerDirectoryController::class, 'show'])->name('practitioners.show')->where('id', '[0-9]+');

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
                // Surveys
                Route::get('/surveys', [\App\Http\Controllers\Customer\SurveyController::class, 'index'])->name('surveys');
                Route::get('/surveys/{survey}', [\App\Http\Controllers\Customer\SurveyController::class, 'show'])->name('surveys.show');
                Route::post('/surveys/{survey}', [\App\Http\Controllers\Customer\SurveyController::class, 'submit'])->name('surveys.submit');
                // Service Requests
                Route::get('/service-requests', [\App\Http\Controllers\Customer\ServiceRequestController::class, 'index'])->name('service-requests');
                Route::get('/service-requests/create', [\App\Http\Controllers\Customer\ServiceRequestController::class, 'create'])->name('service-requests.create');
                Route::post('/service-requests', [\App\Http\Controllers\Customer\ServiceRequestController::class, 'store'])->name('service-requests.store');
                Route::get('/service-requests/{serviceRequest}', [\App\Http\Controllers\Customer\ServiceRequestController::class, 'show'])->name('service-requests.show');
                // Courses
                Route::get('/courses', [\App\Http\Controllers\Customer\CourseController::class, 'index'])->name('courses');
                Route::get('/courses/{course:slug}', [\App\Http\Controllers\Customer\CourseController::class, 'show'])->name('courses.show');
                Route::post('/courses/{course:slug}/enroll', [\App\Http\Controllers\Customer\CourseController::class, 'enroll'])->name('courses.enroll');
                Route::get('/courses/{course:slug}/lessons/{lesson}', [\App\Http\Controllers\Customer\LessonController::class, 'show'])->name('lessons.show');
                Route::post('/courses/{course:slug}/lessons/{lesson}/done', [\App\Http\Controllers\Customer\LessonController::class, 'markDone'])->name('lessons.done');
                Route::get('/certificates', [\App\Http\Controllers\Customer\CertificateController::class, 'index'])->name('certificates');
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
                Route::get('/profile',  [\App\Http\Controllers\Tester\ProfileController::class, 'show'])->name('profile');
                Route::patch('/profile', [\App\Http\Controllers\Tester\ProfileController::class, 'update'])->name('profile.update');
                Route::get('/bug-reports', [\App\Http\Controllers\Tester\BugReportController::class, 'index'])->name('bug-reports');
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
                // Programs
                Route::get('/programs', [\App\Http\Controllers\Practitioner\ProgramController::class, 'index'])->name('programs');
                Route::get('/programs/{program}', [\App\Http\Controllers\Practitioner\ProgramController::class, 'show'])->name('programs.show');
                Route::post('/programs/{program}/apply', [\App\Http\Controllers\Practitioner\ProgramController::class, 'apply'])->name('programs.apply');
                // Applications
                Route::get('/applications', [\App\Http\Controllers\Practitioner\ApplicationController::class, 'index'])->name('applications');
                Route::get('/applications/{application}', [\App\Http\Controllers\Practitioner\ApplicationController::class, 'show'])->name('applications.show');
                // Findings
                Route::get('/applications/{application}/findings/create', [\App\Http\Controllers\Practitioner\FindingController::class, 'create'])->name('findings.create');
                Route::post('/applications/{application}/findings', [\App\Http\Controllers\Practitioner\FindingController::class, 'store'])->name('findings.store');
                // Surveys
                Route::get('/surveys', [\App\Http\Controllers\Practitioner\SurveyController::class, 'index'])->name('surveys');
                Route::get('/surveys/{survey}', [\App\Http\Controllers\Practitioner\SurveyController::class, 'show'])->name('surveys.show');
                Route::post('/surveys/{survey}', [\App\Http\Controllers\Practitioner\SurveyController::class, 'submit'])->name('surveys.submit');
                // Suggestions
                Route::get('/suggestions', [\App\Http\Controllers\Practitioner\SuggestionController::class, 'index'])->name('suggestions');
                Route::get('/suggestions/create', [\App\Http\Controllers\Practitioner\SuggestionController::class, 'create'])->name('suggestions.create');
                Route::post('/suggestions', [\App\Http\Controllers\Practitioner\SuggestionController::class, 'store'])->name('suggestions.store');
                // Bug Reports
                Route::get('/bug-reports', [\App\Http\Controllers\Practitioner\BugReportController::class, 'index'])->name('bug-reports');
                Route::get('/bug-reports/create', [\App\Http\Controllers\Practitioner\BugReportController::class, 'create'])->name('bug-reports.create');
                Route::post('/bug-reports', [\App\Http\Controllers\Practitioner\BugReportController::class, 'store'])->name('bug-reports.store');
                Route::get('/bug-reports/{bugReport}', [\App\Http\Controllers\Practitioner\BugReportController::class, 'show'])->name('bug-reports.show');
                // Courses
                Route::get('/courses', [\App\Http\Controllers\Practitioner\CourseController::class, 'index'])->name('courses');
                Route::get('/courses/{course:slug}', [\App\Http\Controllers\Practitioner\CourseController::class, 'show'])->name('courses.show');
                Route::post('/courses/{course:slug}/enroll', [\App\Http\Controllers\Practitioner\CourseController::class, 'enroll'])->name('courses.enroll');
                Route::get('/courses/{course:slug}/lessons/{lesson}', [\App\Http\Controllers\Practitioner\LessonController::class, 'show'])->name('lessons.show');
                Route::post('/courses/{course:slug}/lessons/{lesson}/done', [\App\Http\Controllers\Practitioner\LessonController::class, 'markDone'])->name('lessons.done');
                Route::get('/certificates', [\App\Http\Controllers\Practitioner\CertificateController::class, 'index'])->name('certificates');
            });

        // Manager portal
        Route::middleware(['auth', 'role:manager'])
            ->prefix('manager')
            ->name('manager.')
            ->group(function () {
                Route::get('/dashboard', [\App\Http\Controllers\Manager\DashboardController::class, 'index'])->name('dashboard');
                Route::get('/team',      [\App\Http\Controllers\Manager\TeamController::class,      'index'])->name('team');
                Route::get('/leave',     [\App\Http\Controllers\Manager\LeaveController::class,     'index'])->name('leave.index');
                Route::post('/leave/{id}/approve', [\App\Http\Controllers\Manager\LeaveController::class, 'approve'])->name('leave.approve');
                Route::post('/leave/{id}/reject',  [\App\Http\Controllers\Manager\LeaveController::class, 'reject'])->name('leave.reject');
                Route::get('/performance',  [\App\Http\Controllers\Manager\PerformanceController::class, 'index'])->name('performance.index');
                Route::post('/performance', [\App\Http\Controllers\Manager\PerformanceController::class, 'store'])->name('performance.store');
                Route::get('/reports',   [\App\Http\Controllers\Manager\ReportController::class,   'index'])->name('reports');
            });

        // HR portal
        Route::middleware(['auth', 'role:hr'])
            ->prefix('hr')
            ->name('hr.')
            ->group(function () {
                Route::get('/dashboard',                 [\App\Http\Controllers\HR\DashboardController::class,   'index'])->name('dashboard');
                Route::get('/employees',                 [\App\Http\Controllers\HR\EmployeeController::class,    'index'])->name('employees.index');
                Route::get('/employees/{user}',          [\App\Http\Controllers\HR\EmployeeController::class,    'show'])->name('employees.show');
                Route::get('/leave',                     [\App\Http\Controllers\HR\LeaveController::class,       'index'])->name('leave.index');
                Route::post('/leave/{id}/approve',       [\App\Http\Controllers\HR\LeaveController::class,       'approve'])->name('leave.approve');
                Route::post('/leave/{id}/reject',        [\App\Http\Controllers\HR\LeaveController::class,       'reject'])->name('leave.reject');
                Route::get('/payroll',                   [\App\Http\Controllers\HR\PayrollController::class,     'index'])->name('payroll.index');
                Route::get('/payroll/{run}',             [\App\Http\Controllers\HR\PayrollController::class,     'show'])->name('payroll.show');
                Route::get('/performance',               [\App\Http\Controllers\HR\PerformanceController::class, 'index'])->name('performance.index');
                Route::post('/performance',              [\App\Http\Controllers\HR\PerformanceController::class, 'store'])->name('performance.store');
                Route::get('/departments',               [\App\Http\Controllers\HR\DepartmentController::class,  'index'])->name('departments.index');
                Route::post('/departments/{dept}/head',  [\App\Http\Controllers\HR\DepartmentController::class,  'updateHead'])->name('departments.head');
            });

        // Accountant portal
        Route::middleware(['auth', 'role:accountant'])
            ->prefix('accountant')
            ->name('accountant.')
            ->group(function () {
                Route::get('/dashboard',                        [\App\Http\Controllers\Accountant\DashboardController::class, 'index'])->name('dashboard');
                Route::get('/invoices',                         [\App\Http\Controllers\Accountant\InvoiceController::class,   'index'])->name('invoices.index');
                Route::get('/invoices/{invoice}',               [\App\Http\Controllers\Accountant\InvoiceController::class,   'show'])->name('invoices.show');
                Route::post('/invoices/{invoice}/mark-paid',    [\App\Http\Controllers\Accountant\InvoiceController::class,   'markPaid'])->name('invoices.mark-paid');
                Route::get('/payroll',                          [\App\Http\Controllers\Accountant\PayrollController::class,   'index'])->name('payroll.index');
                Route::get('/payroll/{run}',                    [\App\Http\Controllers\Accountant\PayrollController::class,   'show'])->name('payroll.show');
                Route::get('/expenses',                         [\App\Http\Controllers\Accountant\ExpenseController::class,   'index'])->name('expenses.index');
                Route::post('/expenses/{id}/approve',           [\App\Http\Controllers\Accountant\ExpenseController::class,   'approve'])->name('expenses.approve');
                Route::post('/expenses/{id}/reject',            [\App\Http\Controllers\Accountant\ExpenseController::class,   'reject'])->name('expenses.reject');
                Route::get('/reports',                          [\App\Http\Controllers\Accountant\ReportController::class,    'index'])->name('reports');
            });

        // Support portal
        Route::middleware(['auth', 'role:support'])->prefix('support')->name('support.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Support\DashboardController::class, 'index'])->name('dashboard');
            Route::get('/tickets',   [\App\Http\Controllers\Support\TicketController::class,    'index'])->name('tickets');
            Route::get('/tickets/{ticket}', [\App\Http\Controllers\Support\TicketController::class, 'show'])->name('tickets.show');
            Route::post('/tickets/{ticket}/reply',   [\App\Http\Controllers\Support\TicketController::class, 'reply'])->name('tickets.reply');
            Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\Support\TicketController::class, 'updateStatus'])->name('tickets.status');
            Route::patch('/tickets/{ticket}/assign', [\App\Http\Controllers\Support\TicketController::class, 'assignToMe'])->name('tickets.assign');
        });
    });

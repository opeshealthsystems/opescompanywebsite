<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Send bare root to the default locale.
Route::get('/', fn () => redirect('/'.config('locale.default')));
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::prefix('{locale}')
    ->where(['locale' => implode('|', config('locale.supported'))])
    ->middleware('setlocale')
    ->group(function () {
        Route::get('/',                    [HomeController::class,   'index'])->name('home');
        Route::get('/products',            [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{slug}',     [ProductController::class, 'show'])->name('product.show');
        Route::get('/contact',             [ContactController::class, 'show'])->name('contact');
        Route::post('/contact',            [ContactController::class, 'submit'])->name('contact.submit');
        Route::get('/solutions',           fn () => view('pages.solutions'))->name('solutions');
        Route::get('/about',               fn () => view('pages.about'))->name('about');
        Route::get('/blog',                [BlogController::class, 'index'])->name('blog');
        Route::get('/blog/{slug}',         [BlogController::class, 'show'])->name('blog.show');
        Route::get('/partnerships',        fn () => view('pages.partnerships'))->name('partnerships');
    });


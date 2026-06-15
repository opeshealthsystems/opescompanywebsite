<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $products     = Product::where('is_active', true)->orderBy('sort_order')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.home', compact('products', 'testimonials'));
    }
}

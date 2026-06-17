<?php

namespace App\Http\Controllers;

use App\Models\PractitionerFinding;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->orderBy('sort_order')->get();

        $testimonials = PractitionerFinding::where('is_published', true)
            ->whereNotNull('findings_text')
            ->with(['practitioner.practitionerProfile', 'application.program'])
            ->latest()
            ->get();

        return view('pages.home', compact('products', 'testimonials'));
    }
}

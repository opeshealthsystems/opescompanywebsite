<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'email'         => 'required|email|max:150',
            'phone'         => 'nullable|string|max:30',
            'facility_type' => 'nullable|string|max:60',
            'products'      => 'nullable|string|max:255',
            'message'       => 'nullable|string|max:2000',
            'product_slug'  => 'nullable|string|max:60',
        ]);

        Lead::create(array_merge($validated, [
            'source'     => $request->filled('product_slug') ? 'product-page' : 'contact',
            'locale'     => app()->getLocale(),
            'ip_address' => $request->ip(),
        ]));

        return back()->with('success', 'Thank you! Our team will contact you within one business day.');
    }
}

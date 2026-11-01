<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show()
    {
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate(\App\Contexts\Enquiries\Application\SubmitEnquiry::RULES);
        app(\App\Contexts\Enquiries\Application\SubmitEnquiry::class)->handle(
            $validated, app()->getLocale(), $request->ip()
        );

        return back()->with('success', app()->getLocale() === 'fr'
            ? 'Merci ! Votre demande a été enregistrée. Notre équipe vous recontactera.'
            : 'Thank you! Your request has been recorded. Our team will contact you.');
    }
}

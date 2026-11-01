<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show(Request $request)
    {
        $quoteOption = $request->query('quote_option');
        $quoteSelection = \App\Contexts\Enquiries\Application\PricingQuoteSelection::find(
            is_string($quoteOption) ? $quoteOption : null,
            app()->getLocale(),
        );

        return view('pages.contact', compact('quoteSelection'));
    }

    public function submit(Request $request)
    {
        $quoteOptions = \App\Contexts\Enquiries\Application\PricingQuoteSelection::keys();
        $validated = $request->validate(\App\Contexts\Enquiries\Application\SubmitEnquiry::RULES + [
            'quote_option' => ['nullable', 'string', 'max:60', 'in:'.implode(',', $quoteOptions)],
        ]);
        $quoteOption = $validated['quote_option'] ?? null;
        unset($validated['quote_option']);

        $result = app(\App\Contexts\Enquiries\Application\SubmitEnquiry::class)->handle(
            $validated, app()->getLocale(), $request->ip(), null, $quoteOption
        );

        $success = app()->getLocale() === 'fr'
            ? 'Merci ! Votre demande a été enregistrée. Notre équipe vous recontactera.'
            : 'Thank you! Your request has been recorded. Our team will contact you.';

        if ($quoteOption !== null && in_array($result['receipt_status'] ?? null, ['sent', 'already-sent'], true)) {
            $success = app()->getLocale() === 'fr'
                ? 'Votre demande de devis a été enregistrée et une copie avec le prix indicatif sélectionné a été envoyée à votre adresse e-mail.'
                : 'Your quote request has been recorded, and a copy showing the selected indicative price has been sent to your email.';
        } elseif ($quoteOption !== null) {
            $success = app()->getLocale() === 'fr'
                ? 'Votre demande de devis a été enregistrée, mais l’envoi de la confirmation par e-mail n’a pas encore été confirmé. Notre équipe vous recontactera.'
                : 'Your quote request has been recorded, but email delivery has not yet been confirmed. Our team will follow up with you.';
        }

        return back()->with('success', $success);
    }
}

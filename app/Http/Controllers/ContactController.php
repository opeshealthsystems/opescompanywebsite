<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function show(Request $request)
    {
        $quoteOption = $request->query('quote_option');
        $productSlug = $request->query('product_slug');
        $products = array_merge(config('products', []), config('products_specialist', []));
        $product = is_string($productSlug) ? ($products[$productSlug] ?? null) : null;
        $quoteSelection = \App\Contexts\Enquiries\Application\PricingQuoteSelection::find(
            is_string($quoteOption) ? $quoteOption : null,
            app()->getLocale(),
        );
        $productQuoteOptions = $product
            ? \App\Contexts\Enquiries\Application\PricingQuoteSelection::forProduct($product['slug'], app()->getLocale())
            : [];
        if ($product && $quoteSelection && !in_array($quoteSelection['key'], array_column($productQuoteOptions, 'key'), true)) {
            $quoteSelection = null;
        }

        return view('pages.contact', compact('quoteSelection', 'product', 'productQuoteOptions'));
    }

    public function submit(Request $request)
    {
        $quoteOptions = \App\Contexts\Enquiries\Application\PricingQuoteSelection::keys();
        $validated = $request->validate(\App\Contexts\Enquiries\Application\SubmitEnquiry::RULES + [
            'quote_option' => ['nullable', 'string', 'max:60', 'in:'.implode(',', $quoteOptions)],
            'product_slug' => ['nullable', 'string', 'max:60', 'in:'.implode(',', array_keys(array_merge(config('products', []), config('products_specialist', []))))],
        ]);
        $quoteOption = $validated['quote_option'] ?? null;
        unset($validated['quote_option']);
        if ($quoteOption !== null && !empty($validated['product_slug'])) {
            $allowedForProduct = array_column(\App\Contexts\Enquiries\Application\PricingQuoteSelection::forProduct($validated['product_slug'], app()->getLocale()), 'key');
            if (!in_array($quoteOption, $allowedForProduct, true)) {
                throw \Illuminate\Validation\ValidationException::withMessages(['quote_option' => ['Choose a published price option for this product.']]);
            }
        }

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

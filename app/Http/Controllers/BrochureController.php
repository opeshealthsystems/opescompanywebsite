<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class BrochureController extends Controller
{
    public function download(string $locale, string $slug)
    {
        $products = array_merge(
            config('products', []),
            config('products_specialist', [])
        );

        if (! array_key_exists($slug, $products)) {
            abort(404);
        }

        $product = $products[$slug];
        $company = config('company');

        $pdf = Pdf::loadView('brochures.product', compact('product', 'company'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', false)
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'OPES-' . strtoupper(str_replace(' ', '-', $product['name'])) . '-Brochure.pdf';

        return $pdf->download($filename);
    }
}

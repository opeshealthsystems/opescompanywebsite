<?php

namespace App\Http\Controllers;

use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function pdf(Request $request, Quote $quote)
    {
        abort_unless(
            $request->user()?->hasAnyRole(['super_admin', 'admin']),
            403
        );

        $quote->load('items', 'lead');

        $pdf = Pdf::loadView('quotes.pdf', compact('quote'))
            ->setPaper('a4', 'portrait');

        $filename = strtolower($quote->reference) . '.pdf';

        return $pdf->download($filename);
    }
}

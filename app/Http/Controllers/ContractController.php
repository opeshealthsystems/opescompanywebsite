<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function pdf(Request $request, Contract $contract)
    {
        abort_unless(
            $request->user()?->hasAnyRole(['super_admin', 'admin']),
            403
        );

        $pdf = Pdf::loadView('contracts.pdf', compact('contract'))
            ->setPaper('a4', 'portrait');

        $filename = strtolower($contract->reference) . '.pdf';

        return $pdf->download($filename);
    }
}

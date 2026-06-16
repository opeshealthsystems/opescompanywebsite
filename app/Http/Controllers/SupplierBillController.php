<?php

namespace App\Http\Controllers;

use App\Models\SupplierBill;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SupplierBillController extends Controller
{
    public function pdf(Request $request, SupplierBill $supplierBill)
    {
        abort_unless(
            $request->user()?->hasAnyRole(['super_admin', 'admin']),
            403
        );

        $supplierBill->load('items', 'vendor', 'purchaseOrder');

        $pdf = Pdf::loadView('supplier-bills.pdf', ['bill' => $supplierBill])
            ->setPaper('a4', 'portrait');

        $filename = strtolower(str_replace('/', '-', $supplierBill->reference)) . '.pdf';

        return $pdf->download($filename);
    }
}

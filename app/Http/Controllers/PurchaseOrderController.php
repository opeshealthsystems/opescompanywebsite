<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function pdf(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless(
            $request->user()?->hasAnyRole(['super_admin', 'admin']),
            403
        );

        $purchaseOrder->load('items', 'vendor', 'requester', 'approver');

        $pdf = Pdf::loadView('purchase-orders.pdf', compact('purchaseOrder'))
            ->setPaper('a4', 'portrait');

        $filename = strtolower(str_replace('/', '-', $purchaseOrder->reference)) . '.pdf';

        return $pdf->download($filename);
    }
}

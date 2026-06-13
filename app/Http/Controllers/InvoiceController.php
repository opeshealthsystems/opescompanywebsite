<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function pdf(Request $request)
    {
        $user    = Auth::user();
        $id      = (int) $request->route('invoice');
        $invoice = Invoice::with('items', 'customer', 'issuer')->findOrFail($id);

        if ($user->hasAnyRole(['super_admin', 'admin', 'support'])) {
            // Admin/support can download any invoice
        } elseif ($user->hasRole('customer')) {
            abort_if((int) $invoice->customer_id !== $user->id, 403);
        } else {
            abort(403);
        }

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}

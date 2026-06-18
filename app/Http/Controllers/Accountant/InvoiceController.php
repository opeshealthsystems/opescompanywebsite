<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('customer')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->from,   fn ($q) => $q->where('created_at', '>=', $request->from))
            ->when($request->to,     fn ($q) => $q->where('created_at', '<=', $request->to.' 23:59:59'))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('accountant.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'issuer']);

        return view('accountant.invoices.show', compact('invoice'));
    }

    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid', 'paid_at' => now()]);

        return back()->with('success', 'Invoice marked as paid.');
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $invoices = Invoice::where('customer_id', $user->id)
            ->whereIn('status', ['sent', 'paid', 'overdue'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('customer.invoices.index', compact('invoices'));
    }

    public function show(Request $request)
    {
        $user    = Auth::user();
        $id      = (int) $request->route('id');
        $invoice = Invoice::with('items')->findOrFail($id);

        abort_if((int) $invoice->customer_id !== $user->id, 403);

        return view('customer.invoices.show', compact('invoice'));
    }
}

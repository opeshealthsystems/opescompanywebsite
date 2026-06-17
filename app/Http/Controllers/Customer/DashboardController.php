<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\License;
use App\Models\Ticket;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user    = Auth::user();
        $profile = $user->customerProfile;

        $activeLicenses  = License::where('customer_id', $user->id)->where('status', 'active')->count();
        $openTickets     = Ticket::where('user_id', $user->id)->whereNotIn('status', ['closed', 'resolved'])->count();
        $pendingInvoices = Invoice::where('customer_id', $user->id)->whereIn('status', ['sent', 'overdue'])->count();

        $recentTickets = Ticket::where('user_id', $user->id)
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        $allProducts = array_merge(config('products', []), config('products_specialist', []));

        return view('customer.dashboard', compact(
            'user', 'profile',
            'activeLicenses', 'openTickets', 'pendingInvoices', 'recentTickets',
            'allProducts'
        ));
    }
}

<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use App\Models\Lead;
use App\Models\License;
use App\Models\Ticket;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class Reports extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Reporting';
    protected static ?string $navigationLabel = 'Overview';
    protected static ?int $navigationSort     = 50;
    protected static string $view             = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }

    public function getViewData(): array
    {
        // Invoice totals are computed from invoice_items — no stored total/grand_total column
        $invoicedTotal  = DB::table('invoice_items')->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->whereIn('invoices.status', ['sent','paid','overdue'])->sum('invoice_items.total');
        $paidTotal      = DB::table('invoice_items')->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->where('invoices.status','paid')->sum('invoice_items.total');
        $overdueCount   = Invoice::where('status', 'overdue')->count();
        $overdueTotal   = DB::table('invoice_items')->join('invoices','invoice_items.invoice_id','=','invoices.id')
            ->where('invoices.status','overdue')->sum('invoice_items.total');

        $activeLicenses  = License::where('status', 'active')->count();
        $expiringLicenses = License::where('status', 'active')
            ->where('end_date', '<=', now()->addDays(30))
            ->where('end_date', '>=', now())
            ->count();

        $openTickets     = Ticket::whereIn('status', ['open', 'in_progress'])->count();
        $resolvedThisMonth = Ticket::where('status', 'resolved')
            ->whereMonth('updated_at', now()->month)
            ->count();

        $newLeads        = Lead::where('created_at', '>=', now()->subDays(30))->count();
        $qualifiedLeads  = Lead::where('status', 'qualified')->count();

        $licensesByProduct = License::where('status', 'active')
            ->selectRaw('product_name, count(*) as total')
            ->groupBy('product_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $recentInvoices = Invoice::with('customer')
            ->whereIn('status', ['sent', 'overdue'])
            ->orderByDesc('due_date')
            ->limit(6)
            ->get();

        return compact(
            'invoicedTotal', 'paidTotal', 'overdueCount', 'overdueTotal',
            'activeLicenses', 'expiringLicenses',
            'openTickets', 'resolvedThisMonth',
            'newLeads', 'qualifiedLeads',
            'licensesByProduct', 'recentInvoices'
        );
    }
}

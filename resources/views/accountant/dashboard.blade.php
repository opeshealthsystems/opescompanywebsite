<x-layouts.accountant title="Accountant Dashboard">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Accountant Dashboard</h1>
        <p class="cp-page-subtitle">{{ now()->format('l, F j, Y') }} · Financial Overview</p>
    </div>
</div>

{{-- KPI Row --}}
<div class="cp-stats-row-4" style="grid-template-columns:repeat(5,1fr)">
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-green">
            <i data-lucide="trending-up" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value" style="font-size:1.1rem">{{ number_format($revenueThisMonth, 0) }}</p>
            <p class="cp-stat-label">Revenue MTD (XAF)</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-blue">
            <i data-lucide="clock" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value" style="font-size:1.1rem">{{ number_format($outstanding, 0) }}</p>
            <p class="cp-stat-label">Outstanding (XAF)</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-red">
            <i data-lucide="alert-circle" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $overdueCount }}</p>
            <p class="cp-stat-label">Overdue Invoices</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-purple">
            <i data-lucide="dollar-sign" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value" style="font-size:1.1rem">{{ $lastPayroll ? number_format($lastPayroll->total_net ?? 0, 0) : '—' }}</p>
            <p class="cp-stat-label">Last Payroll Net</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-amber">
            <i data-lucide="receipt" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value" style="font-size:1.1rem">{{ number_format($expensesMtd, 0) }}</p>
            <p class="cp-stat-label">Expenses MTD (XAF)</p>
        </div>
    </div>
</div>

<div class="cp-section-grid">
    {{-- Overdue Invoices --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="alert-circle" style="width:17px;height:17px;color:#ef4444"></i>
                Overdue Invoices
            </h2>
            <a href="{{ route('accountant.invoices.index', ['locale' => $locale, 'status' => 'overdue']) }}" class="cp-btn-outline" style="font-size:.8125rem">View All</a>
        </div>
        @if($overdueInvoices->isNotEmpty())
        <table class="portal-table">
            <thead><tr><th>Reference</th><th>Customer</th><th>Amount</th><th>Due</th></tr></thead>
            <tbody>
                @foreach($overdueInvoices as $inv)
                <tr>
                    <td style="font-family:monospace;font-size:.8125rem;color:var(--text-muted)">{{ $inv->reference }}</td>
                    <td style="color:#f1f5f9">{{ $inv->customer->name ?? '—' }}</td>
                    <td style="color:#ef4444;font-weight:600">{{ number_format($inv->grand_total ?? 0, 0) }}</td>
                    <td style="color:#ef4444;font-size:.8125rem">{{ $inv->due_date?->format('M j, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="cp-empty-state">
            <i data-lucide="check-circle-2" style="width:32px;height:32px;color:#334155"></i>
            <p>No overdue invoices</p>
        </div>
        @endif
    </div>

    {{-- Recent Payments --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="check-circle-2" style="width:17px;height:17px;color:#00C896"></i>
                Recent Payments
            </h2>
            <a href="{{ route('accountant.invoices.index', ['locale' => $locale, 'status' => 'paid']) }}" class="cp-btn-outline" style="font-size:.8125rem">View All</a>
        </div>
        @if($recentPayments->isNotEmpty())
        <table class="portal-table">
            <thead><tr><th>Reference</th><th>Customer</th><th>Amount</th><th>Paid</th></tr></thead>
            <tbody>
                @foreach($recentPayments as $inv)
                <tr>
                    <td style="font-family:monospace;font-size:.8125rem;color:var(--text-muted)">{{ $inv->reference }}</td>
                    <td style="color:#f1f5f9">{{ $inv->customer->name ?? '—' }}</td>
                    <td style="color:#00C896;font-weight:600">{{ number_format($inv->grand_total ?? 0, 0) }}</td>
                    <td style="color:var(--text-muted);font-size:.8125rem">{{ $inv->paid_at?->format('M j') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="cp-empty-state"><p>No recent payments</p></div>
        @endif
    </div>
</div>

{{-- Quick Actions --}}
<div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:.5rem">
    <a href="{{ route('accountant.invoices.index', ['locale' => $locale]) }}" class="cp-btn-primary">
        <i data-lucide="file-text" style="width:15px;height:15px"></i> All Invoices
    </a>
    <a href="{{ route('accountant.payroll.index', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="dollar-sign" style="width:15px;height:15px"></i> Payroll Costs
    </a>
    <a href="{{ route('accountant.expenses.index', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="receipt" style="width:15px;height:15px"></i> Expenses
    </a>
    <a href="{{ route('accountant.reports', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="bar-chart-2" style="width:15px;height:15px"></i> P&L Report
    </a>
</div>
</x-layouts.accountant>

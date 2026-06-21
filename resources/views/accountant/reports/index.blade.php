<x-layouts.accountant title="Financial Reports">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Financial Reports</h1>
        <p class="cp-page-subtitle">Monthly P&amp;L · Outstanding AR · Payroll trend</p>
    </div>
    <button onclick="window.print()" class="cp-btn-outline">
        <i data-lucide="printer" style="width:15px;height:15px"></i> Print
    </button>
</div>

{{-- P&L Table --}}
<div class="cp-section-card" style="margin-bottom:1.5rem">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="bar-chart-2" style="width:17px;height:17px;color:#F59E0B"></i> Monthly P&amp;L (last 12 months)</h2>
    </div>
    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr><th>Month</th><th>Revenue</th><th>Payroll</th><th>Expenses</th><th>Net</th></tr>
        </thead>
        <tbody>
            @foreach($months as $m)
            @php
                $rev  = $revenue[$m]    ?? 0;
                $pay  = $payrollCost[$m]?? 0;
                $exp  = $expenses[$m]   ?? 0;
                $net  = $rev - $pay - $exp;
            @endphp
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ \Carbon\Carbon::parse($m.'-01')->format('M Y') }}</td>
                <td style="color:#00C896">{{ number_format($rev, 0) }}</td>
                <td style="color:#ef4444">{{ number_format($pay, 0) }}</td>
                <td style="color:#F59E0B">{{ number_format($exp, 0) }}</td>
                <td style="font-weight:700;color:{{ $net >= 0 ? '#00C896' : '#ef4444' }}">{{ number_format($net, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>

{{-- Payroll Trend (CSS bar chart) --}}
<div class="cp-section-card" style="margin-bottom:1.5rem">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="trending-up" style="width:17px;height:17px;color:#F59E0B"></i> Payroll Cost Trend</h2>
    </div>
    @php $maxPay = $payrollCost->max() ?: 1; @endphp
    <div style="display:flex;align-items:flex-end;gap:.5rem;height:120px;padding-bottom:.5rem">
        @foreach($months as $m)
        @php $val = $payrollCost[$m] ?? 0; $pct = ($val / $maxPay) * 100; @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:.25rem;height:100%;justify-content:flex-end">
            <div style="width:100%;background:rgba(139,92,246,.6);border-radius:3px 3px 0 0;height:{{ max(2, $pct) }}%" title="{{ number_format($val,0) }}"></div>
            <span style="color:var(--text-muted);font-size:.625rem;white-space:nowrap">{{ \Carbon\Carbon::parse($m.'-01')->format('M') }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- Outstanding AR --}}
<div class="cp-section-card">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="clock" style="width:17px;height:17px;color:#F59E0B"></i> Outstanding AR by Age</h2>
    </div>
    @if($ar->isNotEmpty())
    @foreach(['0–30d' => 'portal-badge-blue', '31–60d' => 'portal-badge-amber', '60d+' => 'portal-badge-red'] as $bucket => $badgeCls)
    @if(isset($ar[$bucket]))
    <div style="margin-bottom:1rem">
        <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem">
            <span class="portal-badge {{ $badgeCls }}">{{ $bucket }}</span>
            <span style="color:var(--text-muted);font-size:.8125rem">{{ $ar[$bucket]->count() }} invoice(s) · Total: {{ number_format($ar[$bucket]->sum('grand_total'), 0) }} XAF</span>
        </div>
        <table class="portal-table">
            <thead><tr><th>Reference</th><th>Customer</th><th>Amount</th><th>Due</th></tr></thead>
            <tbody>
                @foreach($ar[$bucket] as $inv)
                <tr>
                    <td style="font-family:monospace;font-size:.8125rem;color:var(--text-muted)">{{ $inv->reference }}</td>
                    <td style="color:#f1f5f9">{{ $inv->customer->name ?? '—' }}</td>
                    <td style="font-weight:600">{{ number_format($inv->grand_total ?? 0, 0) }}</td>
                    <td style="color:var(--text-muted);font-size:.8125rem">{{ $inv->due_date?->format('M j, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endforeach
    @else
    <div class="cp-empty-state">
        <i data-lucide="check-circle-2" style="width:32px;height:32px;color:#334155"></i>
        <p>No outstanding invoices</p>
    </div>
    @endif
</div>
</x-layouts.accountant>

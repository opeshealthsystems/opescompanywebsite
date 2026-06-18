<x-layouts.manager title="Reports">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Team Reports</h1>
        <p class="cp-page-subtitle">{{ $dept ? $dept->name.' · ' : '' }}Leave utilisation &amp; attendance summary</p>
    </div>
    <button onclick="window.print()" class="cp-btn-outline">
        <i data-lucide="printer" style="width:15px;height:15px"></i> Print
    </button>
</div>

{{-- Leave by Month --}}
<div class="cp-section-card" style="margin-bottom:1.5rem">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="calendar" style="width:17px;height:17px;color:#1A6FE8"></i> Leave Utilisation by Month</h2>
    </div>
    @if($leaveByMonth->isNotEmpty())
    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr><th>Month</th><th>Total Days Taken</th><th>Requests Approved</th></tr>
        </thead>
        <tbody>
            @foreach($leaveByMonth as $row)
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ \Carbon\Carbon::parse($row->month.'-01')->format('F Y') }}</td>
                <td>{{ number_format($row->total_days, 1) }}</td>
                <td>{{ $row->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    @else
    <div class="cp-empty-state"><p>No approved leave data available.</p></div>
    @endif
</div>

{{-- Leave by Type --}}
<div class="cp-section-grid">
<div class="cp-section-card">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="pie-chart" style="width:17px;height:17px;color:#8B5CF6"></i> Leave by Type</h2>
    </div>
    @if($leaveByType->isNotEmpty())
    <table class="portal-table">
        <thead><tr><th>Type</th><th>Days</th><th>Count</th></tr></thead>
        <tbody>
            @foreach($leaveByType as $row)
            <tr>
                <td><span class="portal-badge portal-badge-blue">{{ ucfirst($row->type) }}</span></td>
                <td>{{ number_format($row->total_days, 1) }}</td>
                <td>{{ $row->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="cp-empty-state"><p>No data.</p></div>
    @endif
</div>

{{-- Leave by Employee --}}
<div class="cp-section-card">
    <div class="cp-section-header">
        <h2 class="cp-section-title"><i data-lucide="users" style="width:17px;height:17px;color:#1A6FE8"></i> Leave by Employee</h2>
    </div>
    @if($leaveByEmployee->isNotEmpty())
    <table class="portal-table">
        <thead><tr><th>Employee</th><th>Days Taken</th><th>Requests</th></tr></thead>
        <tbody>
            @foreach($leaveByEmployee as $row)
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $row->employee->name ?? '—' }}</td>
                <td>{{ number_format($row->total_days, 1) }}</td>
                <td>{{ $row->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="cp-empty-state"><p>No data.</p></div>
    @endif
</div>
</div>
</x-layouts.manager>

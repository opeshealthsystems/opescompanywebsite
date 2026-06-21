<x-layouts.hr title="HR Dashboard">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">HR Dashboard</h1>
        <p class="cp-page-subtitle">{{ now()->format('l, F j, Y') }} · People Operations Overview</p>
    </div>
</div>

{{-- KPI Row --}}
<div class="cp-stats-row-4">
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-purple">
            <i data-lucide="users" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $totalEmployees }}</p>
            <p class="cp-stat-label">Total Employees</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-amber">
            <i data-lucide="clock" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $pendingLeave }}</p>
            <p class="cp-stat-label">Pending Leave</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-blue">
            <i data-lucide="dollar-sign" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $activePayroll ? ucfirst($activePayroll->status) : 'None' }}</p>
            <p class="cp-stat-label">Active Payroll</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-red">
            <i data-lucide="alert-circle" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $expiringContracts->count() }}</p>
            <p class="cp-stat-label">Contracts Expiring (30d)</p>
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem">
    <a href="{{ route('hr.leave.index', ['locale' => $locale]) }}" class="cp-btn-primary">
        <i data-lucide="calendar-check" style="width:15px;height:15px"></i> Review Leave Requests
    </a>
    <a href="{{ route('hr.payroll.index', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="dollar-sign" style="width:15px;height:15px"></i> View Payroll
    </a>
    <a href="{{ route('hr.employees.index', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="users" style="width:15px;height:15px"></i> All Employees
    </a>
</div>

<div class="cp-section-grid">
    {{-- Contracts Expiring --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="alert-circle" style="width:17px;height:17px;color:#ef4444"></i>
                Contracts Expiring Soon
            </h2>
        </div>
        @if($expiringContracts->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:.5rem">
            @foreach($expiringContracts as $ep)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:.625rem 0;border-bottom:1px solid #1e293b">
                <div>
                    <p style="color:#f1f5f9;font-size:.875rem;font-weight:500;margin:0">{{ $ep->user->name ?? '—' }}</p>
                    <p style="color:var(--text-muted);font-size:.75rem;margin:0">{{ ucfirst(str_replace('_',' ',$ep->employment_type)) }}</p>
                </div>
                <span class="portal-badge portal-badge-red">{{ $ep->contract_end_date?->format('M j') }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="cp-empty-state">
            <i data-lucide="check-circle-2" style="width:32px;height:32px;color:#334155"></i>
            <p>No contracts expiring within 30 days</p>
        </div>
        @endif
    </div>

    {{-- Recent Employees --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="user-plus" style="width:17px;height:17px;color:#8B5CF6"></i>
                Recent Employees
            </h2>
            <a href="{{ route('hr.employees.index', ['locale' => $locale]) }}" class="cp-btn-outline" style="font-size:.8125rem">View All</a>
        </div>
        <div style="display:flex;flex-direction:column;gap:.5rem">
            @foreach($recentEmployees as $emp)
            <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem 0;border-bottom:1px solid #1e293b">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(139,92,246,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="user" style="width:16px;height:16px;color:#8B5CF6"></i>
                </div>
                <div style="flex:1">
                    <p style="color:#f1f5f9;font-size:.875rem;font-weight:500;margin:0">{{ $emp->name }}</p>
                    <p style="color:var(--text-muted);font-size:.75rem;margin:0">{{ $emp->position ?? 'Employee' }} · {{ $emp->department ?? '—' }}</p>
                </div>
                <span style="color:var(--text-muted);font-size:.75rem">{{ $emp->created_at?->format('M j') }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Pending Leave --}}
    <div class="cp-section-card" style="grid-column:1/-1">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="calendar-check" style="width:17px;height:17px;color:#8B5CF6"></i>
                Pending Leave Requests
            </h2>
            <a href="{{ route('hr.leave.index', ['locale' => $locale, 'status' => 'pending']) }}" class="cp-btn-outline" style="font-size:.8125rem">View All</a>
        </div>
        @if($recentLeave->isNotEmpty())
        <div style="overflow-x:auto">
        <table class="portal-table">
            <thead>
                <tr><th>Employee</th><th>Type</th><th>Start</th><th>End</th><th>Days</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($recentLeave as $leave)
                <tr>
                    <td style="font-weight:500;color:#f1f5f9">{{ $leave->employee->name ?? '—' }}</td>
                    <td><span class="portal-badge portal-badge-purple">{{ ucfirst($leave->type) }}</span></td>
                    <td>{{ $leave->start_date?->format('M j') }}</td>
                    <td>{{ $leave->end_date?->format('M j, Y') }}</td>
                    <td>{{ $leave->total_days ?? '—' }}</td>
                    <td>
                        <div style="display:flex;gap:.375rem">
                            <form method="POST" action="{{ route('hr.leave.approve', ['locale' => $locale, 'id' => $leave->id]) }}" style="margin:0">
                                @csrf <button type="submit" class="portal-btn-approve">Approve</button>
                            </form>
                            <form method="POST" action="{{ route('hr.leave.reject', ['locale' => $locale, 'id' => $leave->id]) }}" style="margin:0">
                                @csrf <button type="submit" class="portal-btn-reject">Reject</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="cp-empty-state"><i data-lucide="check-circle-2" style="width:32px;height:32px;color:#334155"></i><p>No pending leave requests</p></div>
        @endif
    </div>
</div>
</x-layouts.hr>

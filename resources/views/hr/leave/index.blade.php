<x-layouts.hr title="Leave Management">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Leave Management</h1>
        <p class="cp-page-subtitle">{{ $leaves->total() }} requests · Review and approve</p>
    </div>
</div>

<div class="cp-section-card">
    <form method="GET" class="portal-filter-bar">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="approved"  {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
            <option value="rejected"  {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rejected</option>
        </select>
        <select name="department_id">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}" placeholder="From">
        <input type="date" name="to"   value="{{ request('to') }}"   placeholder="To">
        <button type="submit" class="cp-btn-primary">Filter</button>
        @if(request()->anyFilled(['status','department_id','from','to']))
            <a href="{{ route('hr.leave.index', ['locale' => $locale]) }}" class="cp-btn-outline">Clear</a>
        @endif
    </form>

    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr>
                <th>Employee</th><th>Dept</th><th>Type</th>
                <th>Start</th><th>End</th><th>Days</th>
                <th>Status</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaves as $leave)
            @php
                $badge = match($leave->status) {
                    'approved'  => 'portal-badge-green',
                    'rejected'  => 'portal-badge-red',
                    'cancelled' => 'portal-badge-gray',
                    default     => 'portal-badge-amber',
                };
            @endphp
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $leave->employee->name ?? '—' }}</td>
                <td style="color:var(--text-muted);font-size:.8125rem">{{ $leave->employee->department ?? '—' }}</td>
                <td><span class="portal-badge portal-badge-purple">{{ ucfirst($leave->type) }}</span></td>
                <td>{{ $leave->start_date?->format('M j') }}</td>
                <td>{{ $leave->end_date?->format('M j, Y') }}</td>
                <td>{{ $leave->total_days ?? '—' }}</td>
                <td><span class="portal-badge {{ $badge }}">{{ ucfirst($leave->status) }}</span></td>
                <td>
                    @if($leave->status === 'pending')
                    <div style="display:flex;gap:.375rem;flex-wrap:nowrap">
                        <form method="POST" action="{{ route('hr.leave.approve', ['locale' => $locale, 'id' => $leave->id]) }}" style="margin:0">
                            @csrf <button type="submit" class="portal-btn-approve">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('hr.leave.reject', ['locale' => $locale, 'id' => $leave->id]) }}" style="margin:0">
                            @csrf <button type="submit" class="portal-btn-reject">Reject</button>
                        </form>
                    </div>
                    @else
                    <span style="color:#334155;font-size:.8125rem">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:2rem;color:var(--text-faint)">No leave requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">{{ $leaves->links() }}</div>
</div>
</x-layouts.hr>

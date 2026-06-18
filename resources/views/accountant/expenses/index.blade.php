<x-layouts.accountant title="Expenses">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Expenses</h1>
        <p class="cp-page-subtitle">{{ $expenses->total() }} expense records</p>
    </div>
</div>

<div class="cp-section-card">
    <form method="GET" class="portal-filter-bar">
        <select name="status">
            <option value="">All Statuses</option>
            <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
        </select>
        <select name="category">
            <option value="">All Categories</option>
            @foreach(['payroll','rent','utilities','software','hardware','travel','marketing','legal','training','other'] as $cat)
                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
            @endforeach
        </select>
        <input type="date" name="from" value="{{ request('from') }}">
        <input type="date" name="to"   value="{{ request('to') }}">
        <button type="submit" class="cp-btn-primary">Filter</button>
        @if(request()->anyFilled(['status','category','from','to']))
            <a href="{{ route('accountant.expenses.index', ['locale' => $locale]) }}" class="cp-btn-outline">Clear</a>
        @endif
    </form>

    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr><th>Title</th><th>Category</th><th>Amount</th><th>Vendor</th><th>Date</th><th>Submitted By</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($expenses as $exp)
            @php
                $badge = match($exp->status) {
                    'approved' => 'portal-badge-green',
                    'rejected' => 'portal-badge-red',
                    'paid'     => 'portal-badge-blue',
                    default    => 'portal-badge-amber',
                };
            @endphp
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $exp->title }}</td>
                <td><span class="portal-badge portal-badge-blue">{{ ucfirst($exp->category) }}</span></td>
                <td style="font-weight:600">{{ number_format($exp->amount, 2) }} {{ $exp->currency ?? 'XAF' }}</td>
                <td style="color:#94a3b8">{{ $exp->vendor ?? '—' }}</td>
                <td style="color:#64748b;font-size:.8125rem">{{ $exp->expense_date?->format('M j, Y') }}</td>
                <td style="color:#94a3b8">{{ $exp->submitter->name ?? '—' }}</td>
                <td><span class="portal-badge {{ $badge }}">{{ ucfirst($exp->status) }}</span></td>
                <td>
                    @if($exp->status === 'pending')
                    <div style="display:flex;gap:.375rem;flex-wrap:nowrap">
                        <form method="POST" action="{{ route('accountant.expenses.approve', ['locale' => $locale, 'id' => $exp->id]) }}" style="margin:0">
                            @csrf <button type="submit" class="portal-btn-approve">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('accountant.expenses.reject', ['locale' => $locale, 'id' => $exp->id]) }}" style="margin:0">
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
                <td colspan="8" style="text-align:center;padding:2rem;color:#475569">No expenses found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">{{ $expenses->links() }}</div>
</div>
</x-layouts.accountant>

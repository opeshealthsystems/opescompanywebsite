<x-layouts.manager title="My Team">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">My Team</h1>
        <p class="cp-page-subtitle">{{ $dept ? $dept->name.' Department' : 'All employees' }}</p>
    </div>
</div>

<div class="cp-section-card" style="margin-bottom:1.5rem">
    <form method="GET" class="portal-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name…" style="min-width:200px">
        <button type="submit" class="cp-btn-primary">Search</button>
        @if(request('search'))
            <a href="{{ route('manager.team', ['locale' => $locale]) }}" class="cp-btn-outline">Clear</a>
        @endif
    </form>

    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Position</th>
                <th>Email</th>
                <th>Department</th>
                <th>Employee ID</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($team as $member)
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $member->name }}</td>
                <td>{{ $member->position ?? '—' }}</td>
                <td style="color:#94a3b8">{{ $member->email }}</td>
                <td>{{ $member->department ?? '—' }}</td>
                <td style="font-family:monospace;font-size:.8125rem;color:#64748b">{{ $member->employee_id ?? '—' }}</td>
                <td><span class="portal-badge portal-badge-green">Active</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:2rem;color:#475569">No team members found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>

    <div style="margin-top:1rem">
        {{ $team->links() }}
    </div>
</div>
</x-layouts.manager>

<x-layouts.hr title="Employees">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Employees</h1>
        <p class="cp-page-subtitle">{{ $employees->total() }} total employees</p>
    </div>
</div>

<div class="cp-section-card">
    <form method="GET" class="portal-filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…" style="min-width:200px">
        <select name="department_id">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
            @endforeach
        </select>
        <select name="employment_type">
            <option value="">All Types</option>
            <option value="full_time"  {{ request('employment_type') === 'full_time'  ? 'selected' : '' }}>Full-Time</option>
            <option value="part_time"  {{ request('employment_type') === 'part_time'  ? 'selected' : '' }}>Part-Time</option>
            <option value="contract"   {{ request('employment_type') === 'contract'   ? 'selected' : '' }}>Contract</option>
            <option value="intern"     {{ request('employment_type') === 'intern'     ? 'selected' : '' }}>Intern</option>
        </select>
        <button type="submit" class="cp-btn-primary">Filter</button>
        @if(request()->anyFilled(['search','department_id','employment_type']))
            <a href="{{ route('hr.employees.index', ['locale' => $locale]) }}" class="cp-btn-outline">Clear</a>
        @endif
    </form>

    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Position</th>
                <th>Type</th>
                <th>Hire Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($employees as $emp)
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $emp->name }}</td>
                <td style="color:var(--text-muted);font-size:.8125rem">{{ $emp->email }}</td>
                <td>{{ $emp->department->name ?? $emp->department ?? '—' }}</td>
                <td style="color:var(--text-muted)">{{ $emp->position ?? '—' }}</td>
                <td>
                    @if($emp->employeeProfile)
                        <span class="portal-badge portal-badge-blue">{{ ucfirst(str_replace('_',' ',$emp->employeeProfile->employment_type)) }}</span>
                    @else
                        <span class="portal-badge portal-badge-gray">—</span>
                    @endif
                </td>
                <td style="color:var(--text-muted);font-size:.8125rem">{{ $emp->hire_date ? \Carbon\Carbon::parse($emp->hire_date)->format('M j, Y') : '—' }}</td>
                <td>
                    <a href="{{ route('hr.employees.show', ['locale' => $locale, 'user' => $emp->id]) }}" class="cp-btn-outline" style="font-size:.75rem;padding:.25rem .625rem">
                        View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:2rem;color:var(--text-faint)">No employees found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div style="margin-top:1rem">{{ $employees->links() }}</div>
</div>
</x-layouts.hr>

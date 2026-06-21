<x-layouts.hr title="{{ $user->name }}">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">{{ $user->name }}</h1>
        <p class="cp-page-subtitle">{{ $user->position ?? 'Employee' }} · {{ $user->department ?? '—' }}</p>
    </div>
    <a href="{{ route('hr.employees.index', ['locale' => $locale]) }}" class="cp-btn-outline">
        <i data-lucide="arrow-left" style="width:15px;height:15px"></i> Back to Employees
    </a>
</div>

<div class="cp-section-grid">
    {{-- Basic Info --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="user" style="width:17px;height:17px;color:#8B5CF6"></i> Employee Information</h2>
        </div>
        <div style="display:flex;flex-direction:column;gap:.75rem">
            @php
                $rows = [
                    'Email'       => $user->email,
                    'Employee ID' => $user->employee_id ?? '—',
                    'Position'    => $user->position ?? '—',
                    'Department'  => $user->department ?? '—',
                    'Hire Date'   => $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('M j, Y') : '—',
                ];
            @endphp
            @foreach($rows as $label => $value)
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #1e293b">
                <span style="color:var(--text-muted);font-size:.875rem">{{ $label }}</span>
                <span style="color:#e2e8f0;font-size:.875rem">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Employment Profile --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="briefcase" style="width:17px;height:17px;color:#8B5CF6"></i> Employment Details</h2>
        </div>
        @if($user->employeeProfile)
        @php $ep = $user->employeeProfile; @endphp
        <div style="display:flex;flex-direction:column;gap:.75rem">
            @php
                $profileRows = [
                    'Employment Type'  => ucfirst(str_replace('_',' ', $ep->employment_type ?? '—')),
                    'Contract End'     => $ep->contract_end_date ? $ep->contract_end_date->format('M j, Y') : '—',
                    'Salary'           => $ep->salary ? number_format($ep->salary, 2).' '.($ep->currency ?? 'XAF') : '—',
                    'Bank'             => $ep->bank_name ?? '—',
                    'Emergency Contact'=> $ep->emergency_contact_name ?? '—',
                ];
            @endphp
            @foreach($profileRows as $label => $value)
            <div style="display:flex;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid #1e293b">
                <span style="color:var(--text-muted);font-size:.875rem">{{ $label }}</span>
                <span style="color:#e2e8f0;font-size:.875rem">{{ $value }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="cp-empty-state"><p>No employment profile on record.</p></div>
        @endif
    </div>

    {{-- Leave Balances --}}
    <div class="cp-section-card" style="grid-column:1/-1">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="calendar" style="width:17px;height:17px;color:#8B5CF6"></i> Leave Balances ({{ now()->year }})</h2>
        </div>
        @if($leaveBalances->isNotEmpty())
        <div style="overflow-x:auto">
        <table class="portal-table">
            <thead>
                <tr><th>Type</th><th>Entitled</th><th>Used</th><th>Remaining</th></tr>
            </thead>
            <tbody>
                @foreach($leaveBalances as $bal)
                <tr>
                    <td><span class="portal-badge portal-badge-blue">{{ ucfirst($bal->type) }}</span></td>
                    <td>{{ $bal->entitled_days }}</td>
                    <td>{{ $bal->used_days }}</td>
                    <td>
                        @php $rem = $bal->remainingDays(); @endphp
                        <span style="color:{{ $rem > 0 ? '#00C896' : '#ef4444' }};font-weight:600">{{ $rem }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="cp-empty-state"><p>No leave balances configured for this year.</p></div>
        @endif
    </div>
</div>
</x-layouts.hr>

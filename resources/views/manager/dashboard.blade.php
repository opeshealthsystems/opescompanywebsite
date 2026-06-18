<x-layouts.manager title="Manager Dashboard">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Welcome, {{ $user->name }}</h1>
        <p class="cp-page-subtitle">{{ $dept ? $dept->name.' Department' : 'Manager Portal' }} · {{ now()->format('l, F j, Y') }}</p>
    </div>
</div>

{{-- KPI Row --}}
<div class="cp-stats-row-4">
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-blue">
            <i data-lucide="users" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $teamSize }}</p>
            <p class="cp-stat-label">Team Size</p>
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
        <div class="cp-stat-icon cp-stat-icon-purple">
            <i data-lucide="clipboard-check" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $reviewsDue }}</p>
            <p class="cp-stat-label">Reviews Due</p>
        </div>
    </div>
    <div class="cp-stat-card">
        <div class="cp-stat-icon cp-stat-icon-green">
            <i data-lucide="building-2" style="width:22px;height:22px"></i>
        </div>
        <div>
            <p class="cp-stat-value">{{ $dept ? $dept->name : '—' }}</p>
            <p class="cp-stat-label">Department</p>
        </div>
    </div>
</div>

<div class="cp-section-grid">
    {{-- Pending Leave Requests --}}
    <div class="cp-section-card" style="grid-column: 1 / -1">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="calendar-check" style="width:17px;height:17px;color:#1A6FE8"></i>
                Pending Leave Requests
            </h2>
            <a href="{{ route('manager.leave.index', ['locale' => $locale]) }}" class="cp-btn-outline" style="font-size:.8125rem">View All</a>
        </div>
        @if($recentLeave->isNotEmpty())
        <div style="overflow-x:auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Type</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Days</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentLeave as $leave)
                <tr>
                    <td style="color:#f1f5f9;font-weight:500">{{ $leave->employee->name ?? '—' }}</td>
                    <td><span class="portal-badge portal-badge-blue">{{ ucfirst($leave->type) }}</span></td>
                    <td>{{ $leave->start_date?->format('M j, Y') }}</td>
                    <td>{{ $leave->end_date?->format('M j, Y') }}</td>
                    <td>{{ $leave->total_days ?? '—' }}</td>
                    <td style="display:flex;gap:.5rem">
                        <form method="POST" action="{{ route('manager.leave.approve', ['locale' => $locale, 'id' => $leave->id]) }}" style="margin:0">
                            @csrf
                            <button type="submit" class="portal-btn-approve">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('manager.leave.reject', ['locale' => $locale, 'id' => $leave->id]) }}" style="margin:0">
                            @csrf
                            <button type="submit" class="portal-btn-reject">Reject</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
        @else
        <div class="cp-empty-state">
            <i data-lucide="check-circle-2" style="width:32px;height:32px;color:#334155"></i>
            <p>No pending leave requests</p>
        </div>
        @endif
    </div>
</div>

<div class="cp-section-grid">
    {{-- Team Preview --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="users" style="width:17px;height:17px;color:#1A6FE8"></i>
                My Team
            </h2>
            <a href="{{ route('manager.team', ['locale' => $locale]) }}" class="cp-btn-outline" style="font-size:.8125rem">View All</a>
        </div>
        @if($team->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:.5rem">
            @foreach($team as $member)
            <div style="display:flex;align-items:center;gap:.75rem;padding:.5rem 0;border-bottom:1px solid #1e293b">
                <div style="width:34px;height:34px;border-radius:50%;background:rgba(26,111,232,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i data-lucide="user" style="width:16px;height:16px;color:#1A6FE8"></i>
                </div>
                <div style="flex:1;min-width:0">
                    <p style="color:#f1f5f9;font-size:.875rem;font-weight:500;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $member->name }}</p>
                    <p style="color:#64748b;font-size:.75rem;margin:0">{{ $member->position ?? 'Employee' }}</p>
                </div>
                <span class="portal-badge portal-badge-green">Active</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="cp-empty-state">
            <i data-lucide="users" style="width:32px;height:32px;color:#334155"></i>
            <p>No team members found</p>
        </div>
        @endif
    </div>

    {{-- Performance Reviews --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title">
                <i data-lucide="trending-up" style="width:17px;height:17px;color:#8B5CF6"></i>
                Recent Performance Reviews
            </h2>
            <a href="{{ route('manager.performance.index', ['locale' => $locale]) }}" class="cp-btn-outline" style="font-size:.8125rem">View All</a>
        </div>
        @if($upcomingReviews->isNotEmpty())
        <div style="display:flex;flex-direction:column;gap:.75rem">
            @foreach($upcomingReviews as $review)
            <div style="padding:.75rem;background:rgba(255,255,255,.03);border-radius:8px;border:1px solid #1e293b">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem">
                    <p style="color:#f1f5f9;font-size:.875rem;font-weight:500;margin:0">{{ $review->employee->name ?? '—' }}</p>
                    <span class="portal-badge portal-badge-amber">{{ ucfirst($review->status) }}</span>
                </div>
                <p style="color:#64748b;font-size:.75rem;margin:.25rem 0 0">{{ $review->review_period }} · {{ $review->review_date?->format('M j, Y') }}</p>
            </div>
            @endforeach
        </div>
        @else
        <div class="cp-empty-state">
            <i data-lucide="clipboard-check" style="width:32px;height:32px;color:#334155"></i>
            <p>No reviews initiated yet</p>
        </div>
        @endif
    </div>
</div>
</x-layouts.manager>

<x-layouts.tester title="Tester Dashboard">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">Welcome, {{ auth()->user()->name }}</h1>
            <p class="cp-page-subtitle">Your active testing assignments</p>
        </div>
    </div>

    @if($active->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="beaker" style="width:48px;height:48px;color:#334155"></i>
                <p>No active assignments.</p>
                <p style="font-size:0.8125rem">New testing assignments will appear here when assigned by admin.</p>
            </div>
        </div>
    @else
        <div style="display:grid;gap:1rem;">
            @foreach($active as $assignment)
            @php
                $statusColor = match($assignment->status) {
                    'pending'     => '#94a3b8',
                    'in_progress' => '#eab308',
                    default       => '#64748b',
                };
                $overdue = $assignment->isOverdue();
            @endphp
            <div class="cp-section-card" style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;">
                <div style="flex:1;">
                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;">
                        <span style="color:{{ $statusColor }};font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">
                            {{ \App\Models\TesterAssignment::statusOptions()[$assignment->status] ?? $assignment->status }}
                        </span>
                        @if($overdue)
                            <span style="color:#ef4444;font-size:0.7rem;font-weight:600;">&#9888; OVERDUE</span>
                        @endif
                    </div>
                    <p style="color:#e2e8f0;font-weight:600;font-size:0.9375rem;margin-bottom:0.25rem;">{{ $assignment->title }}</p>
                    <p style="color:#64748b;font-size:0.8125rem;">{{ $assignment->product_name }}</p>
                    @if($assignment->due_date)
                        <p style="color:{{ $overdue ? '#ef4444' : '#64748b' }};font-size:0.75rem;margin-top:0.25rem;">
                            Due: {{ $assignment->due_date->format('d M Y') }}
                        </p>
                    @endif
                </div>
                <a href="{{ route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}"
                   class="cp-btn-outline" style="white-space:nowrap;font-size:0.75rem;">View</a>
            </div>
            @endforeach
        </div>
    @endif

    @if($completed->isNotEmpty())
        <div style="margin-top:2rem;">
            <h2 style="color:#64748b;font-size:0.875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:1rem;">Recently Completed</h2>
            <div class="cp-section-card" style="padding:0;">
                @foreach($completed as $assignment)
                <div style="padding:0.75rem 1rem;border-bottom:1px solid #1e293b;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <span style="color:#64748b;font-size:0.875rem;">{{ $assignment->title }}</span>
                        <span style="color:#334155;font-size:0.75rem;margin-left:0.5rem;">{{ $assignment->product_name }}</span>
                    </div>
                    <span style="color:{{ $assignment->status === 'completed' ? '#00C896' : '#64748b' }};font-size:0.75rem;font-weight:600;text-transform:capitalize;">
                        {{ $assignment->status }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.tester>

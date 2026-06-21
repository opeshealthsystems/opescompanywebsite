<x-layouts.tester title="{{ $assignment->title }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $assignment->title }}</h1>
            <p class="cp-page-subtitle">{{ $assignment->product_name }}</p>
        </div>
        <a href="{{ route('tester.assignments', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
    </div>

    @php
        $statusColor = match($assignment->status) {
            'pending'     => 'var(--text-muted)',
            'in_progress' => '#eab308',
            'completed'   => '#00C896',
            'cancelled'   => 'var(--text-muted)',
            default       => 'var(--text-muted)',
        };
        $isActive = $assignment->isActive();
        $overdue  = $assignment->isOverdue();
    @endphp

    @if($overdue)
        <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;">
            <p style="color:#ef4444;font-weight:600;font-size:0.875rem;margin:0;">&#9888; This assignment is overdue</p>
            <p style="color:var(--text-muted);font-size:0.8rem;margin:0.25rem 0 0;">Due date was {{ $assignment->due_date->format('d M Y') }}. Please update the status or contact your admin.</p>
        </div>
    @endif

    <div class="cp-section-card" style="margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.25rem;">
            <span style="color:{{ $statusColor }};font-weight:700;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.05em;">
                {{ \App\Models\TesterAssignment::statusOptions()[$assignment->status] ?? $assignment->status }}
            </span>
            @if($assignment->due_date)
                <span style="color:{{ $overdue ? '#ef4444' : 'var(--text-muted)' }};font-size:0.8125rem;">
                    Due: {{ $assignment->due_date->format('d M Y') }}
                </span>
            @endif
        </div>

        <div style="margin-bottom:1.25rem;">
            <p style="color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">What to test</p>
            <p style="color:#e2e8f0;font-size:0.875rem;line-height:1.7;white-space:pre-wrap;">{{ $assignment->description }}</p>
        </div>

        @if($assignment->notes)
        <div style="border-top:1px solid #334155;padding-top:1rem;">
            <p style="color:var(--text-muted);font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">Admin Notes</p>
            <p style="color:var(--text-muted);font-size:0.875rem;line-height:1.6;">{{ $assignment->notes }}</p>
        </div>
        @endif
    </div>

    @if($isActive)
    <div class="cp-section-card" style="margin-bottom:1rem;">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">Update Status</h3>
        <form method="POST" action="{{ route('tester.assignments.status', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            @csrf
            @method('PATCH')
            @if($assignment->status === 'pending')
                <button type="submit" name="status" value="in_progress" class="cp-btn-primary">Start Testing</button>
            @elseif($assignment->status === 'in_progress')
                <button type="submit" name="status" value="completed" class="cp-btn-primary">Mark Complete</button>
            @endif
            <button type="submit" name="status" value="cancelled" class="cp-btn-outline" style="color:#ef4444;border-color:#ef4444;"
                onclick="return confirm('Cancel this assignment?')">Cancel Assignment</button>
        </form>
    </div>

    <div class="cp-section-card">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">File a Bug Report</h3>
        <form method="POST" action="{{ route('tester.assignments.bug-reports', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}">
            @csrf

            <div style="margin-bottom:1rem;">
                <label style="display:block;color:var(--text-muted);font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Bug Title *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;"
                    placeholder="Brief description of the bug">
                @error('subject') <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block;color:var(--text-muted);font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Priority *</label>
                <select name="priority" required
                    style="width:200px;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;">
                    @foreach(\App\Models\Ticket::priorityOptions() as $value => $label)
                        <option value="{{ $value }}" {{ old('priority', 'medium') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('priority') <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;color:var(--text-muted);font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Steps to Reproduce / Description *</label>
                <textarea name="description" required maxlength="10000" rows="6"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;resize:vertical;"
                    placeholder="Describe the bug and steps to reproduce it...">{{ old('description') }}</textarea>
                @error('description') <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="cp-btn-primary">Submit Bug Report</button>
        </form>
    </div>
    @endif

    @if($assignment->bugReports->isNotEmpty())
    <div style="margin-top:1.5rem;">
        <h2 style="color:var(--text-muted);font-size:0.875rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:1rem;">
            Bug Reports Filed ({{ $assignment->bugReports->count() }})
        </h2>
        <div class="cp-section-card" style="padding:0;">
            @foreach($assignment->bugReports as $bug)
            @php
                $priorityColor = match($bug->priority) {
                    'urgent' => '#ef4444', 'high' => '#f97316',
                    'medium' => '#3b82f6', 'low'  => 'var(--text-muted)', default => 'var(--text-muted)',
                };
            @endphp
            <div style="padding:0.875rem 1rem;border-bottom:1px solid #1e293b;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                    <span style="color:#e2e8f0;font-size:0.875rem;font-weight:500;">{{ $bug->subject }}</span>
                    <div style="display:flex;gap:0.5rem;align-items:center;">
                        <span style="color:{{ $priorityColor }};font-size:0.75rem;font-weight:600;text-transform:capitalize;">{{ $bug->priority }}</span>
                        <span style="color:var(--text-muted);font-size:0.75rem;">{{ $bug->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</x-layouts.tester>

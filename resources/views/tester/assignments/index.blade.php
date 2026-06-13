<x-layouts.tester title="My Assignments">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Assignments</h1>
            <p class="cp-page-subtitle">All testing assignments assigned to you</p>
        </div>
    </div>

    @if($assignments->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="clipboard-list" style="width:48px;height:48px;color:#334155"></i>
                <p>No assignments yet.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Title</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Product</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Due Date</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assignments as $assignment)
                    @php
                        $statusColor = match($assignment->status) {
                            'pending'     => '#94a3b8',
                            'in_progress' => '#eab308',
                            'completed'   => '#00C896',
                            'cancelled'   => '#64748b',
                            default       => '#94a3b8',
                        };
                        $overdue = $assignment->isOverdue();
                    @endphp
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;color:#e2e8f0;font-size:0.875rem;">{{ Str::limit($assignment->title, 45) }}</td>
                        <td style="padding:0.75rem;color:#94a3b8;font-size:0.875rem;">{{ $assignment->product_name }}</td>
                        <td style="padding:0.75rem;">
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;">
                                {{ \App\Models\TesterAssignment::statusOptions()[$assignment->status] ?? $assignment->status }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:{{ $overdue ? '#ef4444' : '#64748b' }};font-size:0.8125rem;">
                            {{ $assignment->due_date?->format('d M Y') ?? '—' }}
                            @if($overdue) <span style="font-size:0.7rem;">&#9888;</span> @endif
                        </td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('tester.assignments.show', ['locale' => app()->getLocale(), 'id' => $assignment->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $assignments->links() }}
            </div>
        </div>
    @endif
</x-layouts.tester>

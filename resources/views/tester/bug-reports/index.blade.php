<x-layouts.tester title="My Bug Reports">

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Bug Reports</h1>
        <p class="cp-page-subtitle">All bug reports you've filed across testing assignments</p>
    </div>
</div>

@if($reports->isEmpty())
<div class="bg-slate-900 border border-slate-800 rounded-xl p-10 text-center">
    <i data-lucide="bug" style="width:40px;height:40px;color:#334155;margin:0 auto 12px"></i>
    <p class="text-slate-400 text-sm">No bug reports filed yet.</p>
    <p class="text-slate-500 text-xs mt-1">Bug reports appear here when you file them on an assignment.</p>
</div>
@else
<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="border-bottom:1px solid #1e293b">
                <th style="text-align:left;padding:.75rem 1rem;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Subject</th>
                <th style="text-align:left;padding:.75rem 1rem;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Assignment</th>
                <th style="text-align:left;padding:.75rem 1rem;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Priority</th>
                <th style="text-align:left;padding:.75rem 1rem;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Status</th>
                <th style="text-align:left;padding:.75rem 1rem;color:var(--text-muted);font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Filed</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            @php
                $priorityColor = match($report->priority) {
                    'urgent' => '#ef4444',
                    'high'   => '#f97316',
                    'medium' => '#F59E0B',
                    default  => 'var(--text-muted)',
                };
                $statusColor = match($report->status) {
                    'open'        => '#1A6FE8',
                    'in_progress' => '#F59E0B',
                    'resolved'    => '#00C896',
                    'closed'      => 'var(--text-muted)',
                    default       => 'var(--text-muted)',
                };
            @endphp
            <tr style="border-bottom:1px solid #0f172a">
                <td style="padding:.75rem 1rem;color:#e2e8f0;font-size:.875rem">{{ Str::limit($report->subject, 50) }}</td>
                <td style="padding:.75rem 1rem;color:var(--text-muted);font-size:.8125rem">
                    {{ $report->testerAssignment?->product_name ?? '—' }}
                </td>
                <td style="padding:.75rem 1rem">
                    <span style="color:{{ $priorityColor }};font-size:.75rem;font-weight:700;text-transform:uppercase">
                        {{ ucfirst($report->priority) }}
                    </span>
                </td>
                <td style="padding:.75rem 1rem">
                    <span style="color:{{ $statusColor }};font-size:.75rem;font-weight:700;text-transform:uppercase">
                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                    </span>
                </td>
                <td style="padding:.75rem 1rem;color:var(--text-faint);font-size:.8125rem">
                    {{ $report->created_at->format('d M Y') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding:1rem">{{ $reports->links() }}</div>
</div>
@endif
</x-layouts.tester>

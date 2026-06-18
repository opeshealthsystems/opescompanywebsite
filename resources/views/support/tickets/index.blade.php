<x-layouts.support title="Ticket Queue">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <div>
        <h1 class="cp-page-title">Ticket Queue</h1>
        <p class="cp-page-subtitle">All platform tickets</p>
    </div>
    <form method="GET" class="flex gap-2 flex-wrap">
        <select name="status" onchange="this.form.submit()"
                class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
            <option value="">All Statuses</option>
            @foreach(\App\Models\Ticket::statusOptions() as $val => $label)
            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <label class="flex items-center gap-2 text-sm text-slate-400 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 cursor-pointer">
            <input type="checkbox" name="mine" value="1" {{ request('mine') ? 'checked' : '' }} onchange="this.form.submit()">
            Mine only
        </label>
    </form>
</div>

<div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr style="border-bottom:1px solid #1e293b">
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Subject</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">From</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Priority</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Status</th>
                <th style="text-align:left;padding:.75rem 1rem;color:#64748b;font-size:.6875rem;text-transform:uppercase;letter-spacing:.05em">Created</th>
                <th style="padding:.75rem 1rem"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            @php
                $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'#64748b' };
                $sc = match($ticket->status) { 'open'=>'#1A6FE8','in_progress'=>'#F59E0B','resolved'=>'#00C896','closed'=>'#64748b', default=>'#64748b' };
            @endphp
            <tr style="border-bottom:1px solid #0f172a">
                <td style="padding:.75rem 1rem;color:#e2e8f0;font-size:.875rem">{{ Str::limit($ticket->subject, 50) }}</td>
                <td style="padding:.75rem 1rem;color:#64748b;font-size:.8125rem">{{ $ticket->customer?->name ?? '—' }}</td>
                <td style="padding:.75rem 1rem"><span style="color:{{ $pc }};font-size:.75rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span></td>
                <td style="padding:.75rem 1rem"><span style="color:{{ $sc }};font-size:.75rem;font-weight:700;text-transform:uppercase">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                <td style="padding:.75rem 1rem;color:#475569;font-size:.8125rem">{{ $ticket->created_at->format('d M Y') }}</td>
                <td style="padding:.75rem 1rem;text-align:right">
                    <a href="{{ route('support.tickets.show', ['locale'=>$locale,'ticket'=>$ticket->id]) }}"
                       style="font-size:.75rem;color:#F97316;text-decoration:none;font-weight:600">Open →</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:3rem;text-align:center;color:#475569">No tickets found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:1rem">{{ $tickets->links() }}</div>
</div>
</x-layouts.support>

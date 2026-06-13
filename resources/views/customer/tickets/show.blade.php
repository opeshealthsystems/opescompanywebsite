<x-layouts.customer title="{{ $ticket->reference_number }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $ticket->subject }}</h1>
            <p class="cp-page-subtitle">
                <span style="font-family:monospace;color:#00C896;">{{ $ticket->reference_number }}</span>
                &middot; Opened {{ $ticket->created_at->diffForHumans() }}
            </p>
        </div>
        <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
    </div>

    @php
        $statusColor = match($ticket->status) {
            'open'             => '#ef4444',
            'in_progress'      => '#eab308',
            'pending_customer' => '#3b82f6',
            'resolved'         => '#00C896',
            'closed'           => '#64748b',
            default            => '#94a3b8',
        };
        $isOpen = $ticket->isOpen();
    @endphp

    <div style="display:flex;gap:0.75rem;align-items:center;margin-bottom:1.5rem;">
        <span style="color:{{ $statusColor }};font-weight:700;font-size:0.875rem;text-transform:uppercase;letter-spacing:0.05em;">
            {{ \App\Models\Ticket::statusOptions()[$ticket->status] ?? $ticket->status }}
        </span>
        <span style="color:#64748b;font-size:0.8rem;">&#183;</span>
        <span style="color:#94a3b8;font-size:0.8125rem;">{{ \App\Models\Ticket::typeLabel($ticket->type) }}</span>
        <span style="color:#64748b;font-size:0.8rem;">&#183;</span>
        <span style="color:#94a3b8;font-size:0.8125rem;text-transform:capitalize;">{{ $ticket->priority }} priority</span>
    </div>

    <div class="cp-section-card" style="margin-bottom:1rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
            <span style="color:#e2e8f0;font-weight:600;font-size:0.875rem;">Original Request</span>
            <span style="color:#64748b;font-size:0.75rem;">{{ $ticket->created_at->format('d M Y, H:i') }}</span>
        </div>
        <p style="color:#94a3b8;font-size:0.875rem;line-height:1.7;white-space:pre-wrap;">{{ $ticket->description }}</p>
    </div>

    @foreach($ticket->publicReplies as $reply)
    @php
        $isStaff = $reply->author?->hasAnyRole(['super_admin', 'admin', 'support']);
        $bgColor = $isStaff ? '#0f172a' : '#1e293b';
        $borderColor = $isStaff ? '#00C896' : '#334155';
        $authorLabel = $isStaff ? 'OPES Support' : ($reply->author?->name ?? 'Unknown');
    @endphp
    <div style="background:{{ $bgColor }};border:1px solid {{ $borderColor }};border-radius:10px;padding:1rem 1.25rem;margin-bottom:0.75rem;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
            <span style="color:{{ $isStaff ? '#00C896' : '#e2e8f0' }};font-weight:600;font-size:0.875rem;">{{ $authorLabel }}</span>
            <span style="color:#64748b;font-size:0.75rem;">{{ $reply->created_at->format('d M Y, H:i') }}</span>
        </div>
        <p style="color:#94a3b8;font-size:0.875rem;line-height:1.7;white-space:pre-wrap;">{{ $reply->body }}</p>
    </div>
    @endforeach

    @if($ticket->resolution)
    <div style="background:rgba(0,200,150,0.06);border:1px solid rgba(0,200,150,0.2);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1rem;">
        <p style="color:#00C896;font-weight:600;font-size:0.875rem;margin-bottom:0.5rem;">&#10003; Resolution</p>
        <p style="color:#94a3b8;font-size:0.875rem;line-height:1.7;">{{ $ticket->resolution }}</p>
    </div>
    @endif

    @if($isOpen)
    <div class="cp-section-card" style="margin-top:1.25rem;">
        <h3 style="color:#e2e8f0;font-size:0.9rem;font-weight:600;margin-bottom:1rem;">Add a Reply</h3>
        <form method="POST" action="{{ route('customer.tickets.reply', ['locale' => app()->getLocale(), 'id' => $ticket->id]) }}">
            @csrf
            <textarea name="body" required maxlength="10000" rows="5"
                style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;resize:vertical;margin-bottom:0.75rem;"
                placeholder="Type your reply...">{{ old('body') }}</textarea>
            @error('body')
                <p style="color:#ef4444;font-size:0.75rem;margin-bottom:0.5rem;">{{ $message }}</p>
            @enderror
            <button type="submit" class="cp-btn-primary">Send Reply</button>
        </form>
    </div>
    @else
    <div style="text-align:center;padding:1.5rem;color:#64748b;font-size:0.875rem;">
        This ticket is {{ $ticket->status }}. <a href="{{ route('customer.tickets.create', ['locale' => app()->getLocale()]) }}" style="color:#00C896;">Open a new ticket</a> if you need further assistance.
    </div>
    @endif
</x-layouts.customer>

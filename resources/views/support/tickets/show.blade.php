<x-layouts.support title="{{ $ticket->subject }}">
@php $locale = app()->getLocale(); @endphp

<div class="flex items-start justify-between mb-6 flex-wrap gap-3">
    <div>
        <a href="{{ route('support.tickets', ['locale'=>$locale]) }}" class="text-xs text-slate-500 hover:text-slate-300 no-underline">← Back to Queue</a>
        <h1 class="text-xl font-bold text-white mt-1">{{ $ticket->subject }}</h1>
        <div class="flex items-center gap-2 mt-1 flex-wrap">
            @php $pc = match($ticket->priority) { 'urgent'=>'#ef4444','high'=>'#f97316','medium'=>'#F59E0B', default=>'var(--text-muted)' }; @endphp
            <span style="color:{{ $pc }};font-size:.6875rem;font-weight:700;text-transform:uppercase">{{ $ticket->priority }}</span>
            <span class="text-slate-600">·</span>
            <span class="text-slate-400 text-xs">{{ $ticket->customer?->name ?? 'Unknown customer' }}</span>
            <span class="text-slate-600">·</span>
            <span class="text-slate-400 text-xs">{{ $ticket->created_at->format('d M Y H:i') }}</span>
        </div>
    </div>
    <form method="POST" action="{{ route('support.tickets.status', ['locale'=>$locale,'ticket'=>$ticket->id]) }}" class="flex items-center gap-2">
        @csrf @method('PATCH')
        <select name="status" class="bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 focus:outline-none">
            @foreach(\App\Models\Ticket::statusOptions() as $val => $label)
            <option value="{{ $val }}" {{ $ticket->status === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#F97316">Update</button>
    </form>
</div>

@if(!$ticket->assignee)
<div class="bg-amber-900/20 border border-amber-700 rounded-lg px-5 py-3 mb-6 flex items-center justify-between">
    <p class="text-amber-300 text-sm">This ticket is unassigned.</p>
    <form method="POST" action="{{ route('support.tickets.assign', ['locale'=>$locale,'ticket'=>$ticket->id]) }}">
        @csrf @method('PATCH')
        <button type="submit" class="text-sm font-semibold text-amber-300 hover:text-amber-100">Claim it →</button>
    </form>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 flex flex-col gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-semibold text-slate-400">{{ $ticket->customer?->name ?? 'Customer' }}</span>
                <span class="text-xs text-slate-600">{{ $ticket->created_at->format('d M Y H:i') }}</span>
            </div>
            <p class="text-slate-300 text-sm whitespace-pre-wrap">{{ $ticket->description }}</p>
        </div>

        @foreach($ticket->publicReplies as $reply)
        @php $isSupport = $reply->author?->hasAnyRole(['super_admin','admin','support']); @endphp
        <div class="rounded-xl p-5 border {{ $isSupport ? 'border-orange-900/50 bg-orange-900/10' : 'bg-slate-900 border-slate-800' }}">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs font-semibold {{ $isSupport ? 'text-orange-300' : 'text-slate-400' }}">
                    {{ $reply->author?->name ?? 'Staff' }}
                    @if($isSupport) <span class="font-normal text-orange-500/70">(Support)</span> @endif
                </span>
                <span class="text-xs text-slate-600">{{ $reply->created_at->format('d M Y H:i') }}</span>
            </div>
            <p class="text-slate-300 text-sm whitespace-pre-wrap">{{ $reply->body }}</p>
        </div>
        @endforeach

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-sm font-semibold text-white mb-3">Add Reply</h3>
            <form method="POST" action="{{ route('support.tickets.reply', ['locale'=>$locale,'ticket'=>$ticket->id]) }}">
                @csrf
                <textarea name="body" rows="4" required
                          class="w-full bg-slate-800 border border-slate-700 rounded-lg px-3 py-2.5 text-sm text-white focus:outline-none focus:border-orange-500 resize-none"
                          placeholder="Type your reply…"></textarea>
                <div class="flex items-center justify-between mt-3">
                    <label class="flex items-center gap-2 text-xs text-slate-400 cursor-pointer">
                        <input type="checkbox" name="is_internal" value="1" class="rounded">
                        Internal note (hidden from customer)
                    </label>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-semibold text-white" style="background:#F97316">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="flex flex-col gap-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-5">
            <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-3">Details</h3>
            <dl class="flex flex-col gap-2.5 text-sm">
                <div><dt class="text-slate-500 text-xs">Type</dt><dd class="text-slate-300">{{ ucfirst($ticket->type) }}</dd></div>
                <div><dt class="text-slate-500 text-xs">Assigned to</dt><dd class="text-slate-300">{{ $ticket->assignee?->name ?? 'Unassigned' }}</dd></div>
                @if($ticket->sla_resolution_due_at)
                <div>
                    <dt class="text-slate-500 text-xs">SLA deadline</dt>
                    <dd class="{{ $ticket->isSlaResolutionBreached() ? 'text-red-400 font-semibold' : 'text-slate-300' }}">
                        {{ $ticket->sla_resolution_due_at->format('d M Y H:i') }}
                        @if($ticket->isSlaResolutionBreached()) (breached) @endif
                    </dd>
                </div>
                @endif
                @if($ticket->testerAssignment)
                <div>
                    <dt class="text-slate-500 text-xs">Assignment</dt>
                    <dd class="text-slate-300 text-xs">{{ $ticket->testerAssignment->title }}</dd>
                </div>
                @endif
            </dl>
        </div>
    </div>
</div>
</x-layouts.support>

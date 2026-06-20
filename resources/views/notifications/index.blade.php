<x-layouts.app title="Notifications">
    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Notifications</h1>
            <form method="POST" action="{{ route('notifications.read-all', ['locale' => app()->getLocale()]) }}">
                @csrf
                <button class="text-sm text-emerald-600 hover:underline">Mark all read</button>
            </form>
        </div>

        <div class="space-y-3">
            @forelse($notifications as $n)
                <div class="bg-white border {{ $n->read_at ? 'border-slate-200' : 'border-emerald-300' }} rounded-xl p-4 flex items-start gap-3">
                    <div class="flex-1">
                        <h3 class="font-semibold text-slate-900">{{ $n->data['title'] ?? 'Notification' }}</h3>
                        <p class="text-sm text-slate-500 mt-0.5">{{ $n->data['body'] ?? '' }}</p>
                        <p class="text-xs text-slate-400 mt-1">{{ $n->created_at?->diffForHumans() }}</p>
                    </div>
                    @if(($n->data['url'] ?? null) || ! $n->read_at)
                        <form method="POST" action="{{ route('notifications.read', ['locale' => app()->getLocale(), 'id' => $n->id]) }}">
                            @csrf
                            <button class="text-xs px-3 py-1.5 bg-emerald-600 text-white rounded-lg">{{ ($n->data['url'] ?? null) ? 'Open' : 'Mark read' }}</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-xl p-10 text-center text-slate-400">No notifications yet.</div>
            @endforelse
        </div>

        <div class="mt-6">{{ $notifications->links() }}</div>
    </div>
</x-layouts.app>

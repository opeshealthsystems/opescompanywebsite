<x-layouts.practitioner title="My Suggestions">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My Suggestions</h1>
            <p class="text-slate-500 mt-1">Submit ideas and feature requests to help us improve OPES.</p>
        </div>
        <a href="{{ route('practitioner.suggestions.create', ['locale' => app()->getLocale()]) }}"
           class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
            + New Suggestion
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($suggestions->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-400">
            You haven't submitted any suggestions yet. Share your ideas with us!
        </div>
    @else
        <div class="space-y-4">
            @foreach($suggestions as $suggestion)
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="font-semibold text-slate-800">{{ $suggestion->title }}</h3>
                            <p class="text-sm text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($suggestion->body, 120) }}</p>
                        </div>
                        <div class="ml-4 shrink-0 flex flex-col items-end gap-2">
                            @php
                                $statusColors = [
                                    'pending'      => 'bg-amber-100 text-amber-700',
                                    'under_review' => 'bg-blue-100 text-blue-700',
                                    'accepted'     => 'bg-green-100 text-green-700',
                                    'implemented'  => 'bg-emerald-100 text-emerald-700',
                                    'declined'     => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$suggestion->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ \App\Models\Suggestion::statusOptions()[$suggestion->status] ?? $suggestion->status }}
                            </span>
                            <span class="text-xs text-slate-400">{{ $suggestion->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    @if($suggestion->admin_response)
                        <div class="mt-3 pt-3 border-t border-slate-100">
                            <p class="text-xs font-medium text-slate-500 uppercase tracking-wide mb-1">OPES Response</p>
                            <p class="text-sm text-slate-700">{{ $suggestion->admin_response }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.practitioner>

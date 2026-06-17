<x-layouts.practitioner title="My Applications">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">My Applications</h1>
            <p class="text-slate-400 text-sm">Track the status of all your programme applications.</p>
        </div>
        <a href="{{ route('practitioner.programs', ['locale' => app()->getLocale()]) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-600 text-slate-300 text-sm hover:border-slate-400 hover:text-white transition-colors no-underline">
            <i data-lucide="plus" style="width:15px;height:15px"></i> Browse Programmes
        </a>
    </div>

    @if($applications->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <i data-lucide="clipboard-list" style="width:40px;height:40px;color:#475569;margin:0 auto 16px"></i>
            <p class="text-slate-400 text-base">You haven't applied to any programmes yet.</p>
            <a href="{{ route('practitioner.programs', ['locale' => app()->getLocale()]) }}"
               class="inline-block mt-4 text-sm text-emerald-400 hover:text-emerald-300 no-underline">
                Browse open programmes →
            </a>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-800">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Programme</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Status</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wide px-5 py-3">Submitted</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($applications as $application)
                        @php
                            $statusColors = [
                                'pending'  => 'bg-amber-900 text-amber-300',
                                'approved' => 'bg-emerald-900 text-emerald-300',
                                'rejected' => 'bg-red-900 text-red-300',
                            ];
                        @endphp
                        <tr class="hover:bg-slate-800/50 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-medium text-white">{{ $application->program->title ?? '—' }}</p>
                                <p class="text-xs text-slate-500 capitalize mt-0.5">{{ $application->program->type ?? '' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColors[$application->status] ?? 'bg-slate-700 text-slate-300' }} capitalize">
                                    {{ $application->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-slate-400">
                                {{ $application->created_at->format('M j, Y') }}
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('practitioner.applications.show', ['locale' => app()->getLocale(), 'application' => $application->id]) }}"
                                   class="text-sm text-emerald-400 hover:text-emerald-300 no-underline transition-colors">
                                    View →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</x-layouts.practitioner>

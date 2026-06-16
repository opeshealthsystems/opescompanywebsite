<x-layouts.practitioner title="Open Programmes">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white mb-1">Open Programmes</h1>
            <p class="text-slate-400 text-sm">Browse and apply to currently open practitioner programmes.</p>
        </div>
    </div>

    @if($programs->isEmpty())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-12 text-center">
            <i data-lucide="beaker" style="width:40px;height:40px;color:#475569;margin:0 auto 16px"></i>
            <p class="text-slate-400 text-base">No programmes currently open.</p>
            <p class="text-slate-500 text-sm mt-1">Check back soon — new programmes are added regularly.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($programs as $program)
                <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 flex flex-col gap-3 hover:border-slate-600 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <h2 class="text-white font-semibold text-base leading-snug flex-1">{{ $program->title }}</h2>
                        @if($program->type === 'paid')
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-900 text-emerald-300 whitespace-nowrap">Paid</span>
                        @else
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-700 text-slate-300 whitespace-nowrap">Volunteer</span>
                        @endif
                    </div>

                    @if($program->description)
                        <p class="text-slate-400 text-sm line-clamp-3 leading-relaxed">{{ Str::limit($program->description, 120) }}</p>
                    @endif

                    <div class="flex items-center gap-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1">
                            <i data-lucide="users" style="width:13px;height:13px"></i>
                            {{ $program->applications_count }}
                            @if($program->max_participants) / {{ $program->max_participants }} @endif
                            participants
                        </span>
                        @if($program->starts_at)
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar" style="width:13px;height:13px"></i>
                                {{ $program->starts_at->format('M j, Y') }}
                            </span>
                        @endif
                    </div>

                    <div class="mt-auto pt-2 flex items-center justify-between gap-3">
                        <a href="{{ route('practitioner.programs.show', ['locale' => app()->getLocale(), 'program' => $program->id]) }}"
                           class="text-sm text-emerald-400 hover:text-emerald-300 no-underline transition-colors">
                            View details →
                        </a>
                        @if(in_array($program->id, $myApplicationProgramIds))
                            <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-700 text-slate-400">
                                Already Applied
                            </span>
                        @else
                            <form method="POST" action="{{ route('practitioner.programs.apply', ['locale' => app()->getLocale(), 'program' => $program->id]) }}">
                                @csrf
                                <button type="submit"
                                    class="text-sm font-semibold px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white transition-colors border-0 cursor-pointer">
                                    Apply Now
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.practitioner>

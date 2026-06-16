<x-layouts.practitioner title="{{ $program->title }}">
    <div class="mb-6">
        <a href="{{ route('practitioner.programs', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-slate-400 hover:text-white no-underline transition-colors flex items-center gap-1 w-fit">
            <i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back to Programmes
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 mb-6">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-white mb-2">{{ $program->title }}</h1>
                <div class="flex items-center gap-3 flex-wrap">
                    @if($program->type === 'paid')
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-900 text-emerald-300">Paid</span>
                    @else
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-700 text-slate-300">Volunteer</span>
                    @endif
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-900 text-blue-300 capitalize">{{ $program->status }}</span>
                </div>
            </div>
            <div class="text-right text-sm text-slate-400 shrink-0">
                <div class="flex items-center gap-1 justify-end">
                    <i data-lucide="users" style="width:14px;height:14px"></i>
                    <span>{{ $program->applications_count }}{{ $program->max_participants ? ' / '.$program->max_participants : '' }} applicants</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5 pt-4 border-t border-slate-800">
            @if($program->starts_at)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Starts</p>
                <p class="text-sm font-medium text-white">{{ $program->starts_at->format('M j, Y') }}</p>
            </div>
            @endif
            @if($program->ends_at)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Ends</p>
                <p class="text-sm font-medium text-white">{{ $program->ends_at->format('M j, Y') }}</p>
            </div>
            @endif
            @if($program->max_participants)
            <div>
                <p class="text-xs text-slate-500 mb-0.5">Max Participants</p>
                <p class="text-sm font-medium text-white">{{ $program->max_participants }}</p>
            </div>
            @endif
        </div>

        @if($program->description)
        <div>
            <p class="text-xs text-slate-500 mb-2 font-semibold uppercase tracking-wide">About this Programme</p>
            <p class="text-slate-300 text-sm leading-relaxed whitespace-pre-line">{{ $program->description }}</p>
        </div>
        @endif
    </div>

    @if($myApplication)
        <div class="bg-slate-900 border border-slate-700 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-3">
                <i data-lucide="clipboard-check" style="width:20px;height:20px;color:#00C896"></i>
                <h2 class="text-white font-semibold text-base">Your Application</h2>
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-400">Status:</span>
                    @php
                        $statusColors = [
                            'pending'  => 'bg-amber-900 text-amber-300',
                            'approved' => 'bg-emerald-900 text-emerald-300',
                            'rejected' => 'bg-red-900 text-red-300',
                        ];
                    @endphp
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColors[$myApplication->status] ?? 'bg-slate-700 text-slate-300' }} capitalize">
                        {{ $myApplication->status }}
                    </span>
                </div>
                <a href="{{ route('practitioner.applications.show', ['locale' => app()->getLocale(), 'application' => $myApplication->id]) }}"
                   class="text-sm text-emerald-400 hover:text-emerald-300 no-underline transition-colors">
                    View Application →
                </a>
            </div>
        </div>
    @elseif($program->isOpen() && !$program->isFull())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
            <h2 class="text-white font-semibold text-base mb-1">Apply to This Programme</h2>
            <p class="text-slate-400 text-sm mb-5">Tell us why you want to participate (optional).</p>

            <form method="POST" action="{{ route('practitioner.programs.apply', ['locale' => app()->getLocale(), 'program' => $program->id]) }}">
                @csrf
                @if($errors->any())
                    <div class="bg-red-900/30 border border-red-700 rounded-lg px-4 py-3 mb-4 text-sm text-red-300">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <div class="mb-5">
                    <label for="motivation" class="block text-sm font-medium text-slate-300 mb-1.5">Motivation <span class="text-slate-500">(optional)</span></label>
                    <textarea id="motivation" name="motivation" rows="5"
                        placeholder="Why do you want to join this programme? What do you hope to contribute?"
                        class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 resize-y"
                        maxlength="2000">{{ old('motivation') }}</textarea>
                    <p class="text-xs text-slate-500 mt-1">Max 2,000 characters.</p>
                </div>
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm rounded-lg transition-colors border-0 cursor-pointer">
                    Submit Application
                </button>
            </form>
        </div>
    @elseif($program->isFull())
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 text-center">
            <i data-lucide="users" style="width:32px;height:32px;color:#475569;margin:0 auto 12px"></i>
            <p class="text-slate-400">This programme has reached its maximum number of participants.</p>
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 text-center">
            <p class="text-slate-400">This programme is not currently accepting applications.</p>
        </div>
    @endif
</x-layouts.practitioner>

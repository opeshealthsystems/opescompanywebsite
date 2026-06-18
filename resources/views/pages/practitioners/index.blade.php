<x-layouts.app>
@php $locale = app()->getLocale(); @endphp
<section class="py-16 px-4" style="min-height:80vh">
    <div style="max-width:1200px;margin:0 auto">

        <div class="text-center mb-12">
            <span class="inline-block text-xs font-semibold text-emerald-400 uppercase tracking-widest mb-3">Verified Contributors</span>
            <h1 class="text-4xl font-bold text-white mb-4">Practitioner Directory</h1>
            <p class="text-slate-400 text-lg max-w-xl mx-auto">
                Healthcare professionals who have participated in OPES product testing programs and contributed findings.
            </p>
        </div>

        <form method="GET" class="flex flex-wrap gap-3 mb-8 justify-center">
            <select name="profession" onchange="this.form.submit()"
                    class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-emerald-500">
                <option value="">All Professions</option>
                @foreach($professions as $key => $label)
                <option value="{{ $key }}" {{ request('profession') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <select name="country" onchange="this.form.submit()"
                    class="bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-slate-300 focus:outline-none focus:border-emerald-500">
                <option value="">All Countries</option>
                @foreach($countries as $country)
                <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>{{ $country }}</option>
                @endforeach
            </select>
            @if(request()->hasAny(['profession','country']))
            <a href="{{ route('practitioners.index', ['locale' => $locale]) }}"
               class="flex items-center gap-1 text-sm text-slate-400 hover:text-white bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 no-underline">
                Clear filters
            </a>
            @endif
        </form>

        @if($practitioners->isEmpty())
        <div class="text-center py-20 text-slate-500">
            <i data-lucide="users" style="width:48px;height:48px;margin:0 auto 12px"></i>
            <p>No practitioners found for the selected filters.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach($practitioners as $profile)
            @php
                $s    = $stats[$profile->user_id] ?? ['programs'=>0,'findings'=>0,'avgRating'=>null];
                $tier = $profile->user?->practitionerTier();
            @endphp
            <a href="{{ route('practitioners.show', ['locale'=>$locale,'id'=>$profile->user_id]) }}"
               class="bg-slate-900 border border-slate-800 hover:border-emerald-700 rounded-xl p-6 flex flex-col gap-4 transition-colors no-underline group">
                <div class="flex items-start justify-between">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-xl font-bold text-white flex-shrink-0"
                         style="background:linear-gradient(135deg,#00C896,#1A6FE8)">
                        {{ strtoupper(substr($profile->user->name, 0, 1)) }}
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        @if($profile->is_verified)
                        <span class="flex items-center gap-1 text-xs font-semibold text-emerald-400 bg-emerald-900/30 px-2 py-0.5 rounded-full border border-emerald-800">
                            <i data-lucide="shield-check" style="width:12px;height:12px"></i> Verified
                        </span>
                        @endif
                        @if($tier)
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $tier->tailwindBadge() }}">{{ $tier->label() }}</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="font-semibold text-white group-hover:text-emerald-300 transition-colors">{{ $profile->user->name }}</p>
                    <p class="text-sm text-slate-400">
                        {{ \App\Models\PractitionerProfile::professionOptions()[$profile->profession] ?? $profile->profession }}
                        @if($profile->specialty) · {{ $profile->specialty }} @endif
                    </p>
                    @if($profile->workplace_name)
                    <p class="text-xs text-slate-500 mt-1">{{ $profile->workplace_name }}, {{ $profile->workplace_country }}</p>
                    @endif
                </div>
                <div class="flex gap-4 pt-3 border-t border-slate-800 text-center">
                    <div class="flex-1">
                        <p class="text-lg font-bold text-white">{{ $s['programs'] }}</p>
                        <p class="text-xs text-slate-500">Programs</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-lg font-bold text-white">{{ $s['findings'] }}</p>
                        <p class="text-xs text-slate-500">Findings</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-lg font-bold text-white">{{ $s['avgRating'] ? number_format($s['avgRating'],1) : '—' }}</p>
                        <p class="text-xs text-slate-500">Avg Rating</p>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="flex justify-center">{{ $practitioners->links() }}</div>
        @endif
    </div>
</section>
</x-layouts.app>

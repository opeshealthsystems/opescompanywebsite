<x-layouts.app>
@php
    $locale    = app()->getLocale();
    $isFr      = $locale === 'fr';
    $tier      = $profile->user?->practitionerTier();
    $avgRating = $ratingBreakdown['overall'];
    $profLabel = \App\Models\PractitionerProfile::professionOptions()[$profile->profession] ?? $profile->profession;
@endphp

<section class="py-12 px-4" style="min-height:80vh">
<div style="max-width:900px;margin:0 auto">

    {{-- Back link --}}
    <a href="{{ route('practitioners.index', ['locale' => $locale]) }}"
       class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white mb-8 no-underline transition-colors">
        <i data-lucide="arrow-left" style="width:16px;height:16px"></i>
        {{ $isFr ? 'Retour à l\'annuaire' : 'Back to Directory' }}
    </a>

    {{-- Hero card --}}
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 mb-6">
        <div class="flex flex-col sm:flex-row gap-6 items-start">
            <div class="w-20 h-20 rounded-full flex-shrink-0 flex items-center justify-center text-3xl font-bold text-white"
                 style="background:linear-gradient(135deg,#00C896,#1A6FE8)">
                {{ strtoupper(substr($profile->user?->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <h1 class="text-2xl font-bold text-white">{{ $profile->user?->name ?? '—' }}</h1>
                    @if($profile->is_verified)
                    <span class="flex items-center gap-1 text-xs font-semibold text-emerald-400 bg-emerald-900/30 px-2 py-0.5 rounded-full border border-emerald-800">
                        <i data-lucide="shield-check" style="width:12px;height:12px"></i> Verified
                    </span>
                    @endif
                    @if($tier)
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $tier->tailwindBadge() }}">{{ $tier->label() }}</span>
                    @endif
                </div>
                <p class="text-slate-300 font-medium">
                    {{ $profLabel }}
                    @if($profile->specialty) · {{ $profile->specialty }} @endif
                </p>
                @if($profile->workplace_name)
                <p class="text-sm text-slate-500 mt-1 flex items-center gap-1">
                    <i data-lucide="map-pin" style="width:13px;height:13px"></i>
                    {{ $profile->workplace_name }}@if($profile->workplace_city), {{ $profile->workplace_city }}@endif, {{ $profile->workplace_country }}
                </p>
                @endif
                @if($profile->years_of_experience)
                <p class="text-sm text-slate-500 mt-0.5 flex items-center gap-1">
                    <i data-lucide="briefcase" style="width:13px;height:13px"></i>
                    {{ $profile->years_of_experience }} {{ $isFr ? 'ans d\'expérience' : 'years of experience' }}
                </p>
                @endif
            </div>
            {{-- Contribution stats --}}
            <div class="flex gap-6 sm:flex-col sm:items-end text-center sm:text-right">
                <div>
                    <p class="text-2xl font-bold text-white">{{ $approvedApplications->count() }}</p>
                    <p class="text-xs text-slate-500">{{ $isFr ? 'Programmes' : 'Programs' }}</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $publishedFindings->count() }}</p>
                    <p class="text-xs text-slate-500">{{ $isFr ? 'Constats' : 'Findings' }}</p>
                </div>
                @if($avgRating)
                <div>
                    <p class="text-2xl font-bold text-emerald-400">{{ number_format($avgRating, 1) }}</p>
                    <p class="text-xs text-slate-500">{{ $isFr ? 'Note moy.' : 'Avg Rating' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column --}}
        <div class="lg:col-span-2 flex flex-col gap-6">

            {{-- Bio --}}
            @if($profile->bio)
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-3">{{ $isFr ? 'À propos' : 'About' }}</h2>
                <p class="text-slate-300 leading-relaxed">{{ $profile->bio }}</p>
            </div>
            @endif

            {{-- OPES Testimonial --}}
            @if($profile->opes_testimonial)
            <div class="bg-emerald-950/40 border border-emerald-900/50 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-emerald-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i data-lucide="quote" style="width:14px;height:14px"></i>
                    {{ $isFr ? 'Témoignage OPES' : 'OPES Testimonial' }}
                </h2>
                <p class="text-slate-200 leading-relaxed italic">"{{ $profile->opes_testimonial }}"</p>
            </div>
            @endif

            {{-- Programs participated --}}
            @if($approvedApplications->isNotEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">{{ $isFr ? 'Programmes participés' : 'Programs Participated' }}</h2>
                <div class="flex flex-col gap-3">
                    @foreach($approvedApplications as $application)
                    @php $prog = $application->program; @endphp
                    <div class="flex items-center justify-between py-2 border-b border-slate-800 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $prog?->title ?? 'Unknown Program' }}</p>
                            <p class="text-xs text-slate-500">{{ $prog?->product_name }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($prog?->type === 'paid')
                            <span class="text-xs font-semibold text-emerald-400 bg-emerald-900/30 px-2 py-0.5 rounded-full border border-emerald-800">{{ $isFr ? 'Payé' : 'Paid' }}</span>
                            @else
                            <span class="text-xs font-semibold text-slate-400 bg-slate-800 px-2 py-0.5 rounded-full">{{ $isFr ? 'Bénévole' : 'Volunteer' }}</span>
                            @endif
                            @if($application->reviewed_at)
                            <span class="text-xs text-slate-500">{{ $application->reviewed_at->format('M Y') }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent findings --}}
            @if($publishedFindings->isNotEmpty())
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">{{ $isFr ? 'Constats récents' : 'Recent Findings' }}</h2>
                <div class="flex flex-col gap-4">
                    @foreach($publishedFindings as $finding)
                    <div class="pb-4 border-b border-slate-800 last:border-0">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs text-slate-500">
                                {{ $finding->application?->program?->product_name ?? 'Unknown Product' }}
                            </p>
                            @if($finding->overall_rating)
                            <div class="flex items-center gap-1">
                                <i data-lucide="star" style="width:12px;height:12px;color:#00C896"></i>
                                <span class="text-xs font-semibold text-emerald-400">{{ number_format($finding->overall_rating, 1) }}</span>
                            </div>
                            @endif
                        </div>
                        @if($finding->findings_text)
                        <p class="text-sm text-slate-300 leading-relaxed line-clamp-3">{{ $finding->findings_text }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Right sidebar --}}
        <div class="flex flex-col gap-6">

            {{-- Rating breakdown --}}
            @if($ratingBreakdown['overall'])
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">{{ $isFr ? 'Évaluation détaillée' : 'Rating Breakdown' }}</h2>
                @php
                    $ratingItems = [
                        'Overall'        => $ratingBreakdown['overall'],
                        'Usability'      => $ratingBreakdown['usability'],
                        'Wait Time'      => $ratingBreakdown['wait_time'],
                        'Data Integrity' => $ratingBreakdown['data_integrity'],
                    ];
                @endphp
                @foreach($ratingItems as $label => $value)
                @if($value)
                <div class="mb-3">
                    <div class="flex justify-between text-xs text-slate-400 mb-1">
                        <span>{{ $label }}</span>
                        <span class="font-semibold text-white">{{ number_format($value, 1) }}/5</span>
                    </div>
                    <div class="h-1.5 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full"
                             style="width:{{ min(100, ($value / 5) * 100) }}%;background:linear-gradient(90deg,#00C896,#1A6FE8)">
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @endif

            {{-- Quick details --}}
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-widest mb-4">{{ $isFr ? 'Détails' : 'Details' }}</h2>
                <dl class="flex flex-col gap-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500 mb-0.5">{{ $isFr ? 'Profession' : 'Profession' }}</dt>
                        <dd class="text-slate-200">{{ $profLabel }}</dd>
                    </div>
                    @if($profile->specialty)
                    <div>
                        <dt class="text-xs text-slate-500 mb-0.5">{{ $isFr ? 'Spécialité' : 'Specialty' }}</dt>
                        <dd class="text-slate-200">{{ $profile->specialty }}</dd>
                    </div>
                    @endif
                    @if($profile->workplace_country)
                    <div>
                        <dt class="text-xs text-slate-500 mb-0.5">{{ $isFr ? 'Pays' : 'Country' }}</dt>
                        <dd class="text-slate-200">{{ $profile->workplace_country }}</dd>
                    </div>
                    @endif
                    @if($profile->years_of_experience)
                    <div>
                        <dt class="text-xs text-slate-500 mb-0.5">{{ $isFr ? 'Expérience' : 'Experience' }}</dt>
                        <dd class="text-slate-200">{{ $profile->years_of_experience }} {{ $isFr ? 'ans d\'expérience' : 'years' }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

        </div>
    </div>

</div>
</section>
</x-layouts.app>

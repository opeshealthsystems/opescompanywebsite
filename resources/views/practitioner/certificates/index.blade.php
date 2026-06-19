<x-layouts.practitioner title="Certificates">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">My Certificates</h1>
        <p class="text-slate-400 mt-1">Download certificates for courses you have completed.</p>
    </div>

    @if(isset($councilMembership) && $councilMembership)
        <div class="bg-gradient-to-r from-emerald-900/40 to-slate-900 border border-emerald-700/50 rounded-xl p-5 mb-6 flex items-center gap-3">
            <i data-lucide="shield-check" style="width:28px;height:28px" class="text-emerald-400"></i>
            <div>
                <h3 class="font-semibold text-white">Clinical Validation Advisory Council</h3>
                <p class="text-xs text-emerald-300/80 mt-0.5">
                    {{ $councilMembership->title }} &middot; since {{ $councilMembership->term_start?->format('M Y') }}
                </p>
            </div>
        </div>
    @endif

    @if(!empty($validationCertificates) && $validationCertificates->isNotEmpty())
        <h2 class="text-lg font-semibold text-white mb-3">Validation Certificates</h2>
        <div class="space-y-4 mb-8">
            @foreach($validationCertificates as $vcert)
                <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="badge-check" style="width:28px;height:28px" class="text-emerald-400"></i>
                        <div>
                            <h3 class="font-semibold text-white">{{ $vcert->cohortMember?->cohort?->name ?? 'Validation Cohort' }}
                                <span class="ml-2 text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300">{{ ucfirst($vcert->tier) }} &middot; {{ $vcert->score }}/100</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $vcert->certificate_number }} &middot; Issued {{ $vcert->issued_at?->format('d M Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('practitioner.certificates.validation-download', ['locale' => app()->getLocale(), 'certificate' => $vcert->id]) }}"
                       class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition no-underline">
                        Download
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    @if($certificates->isEmpty())
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-10 text-center text-slate-500">
            You have not earned any certificates yet. Complete a course to earn one.
        </div>
    @else
        <div class="space-y-4">
            @foreach($certificates as $cert)
                <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="award" style="width:28px;height:28px" class="text-emerald-400"></i>
                        <div>
                            <h3 class="font-semibold text-white">{{ $cert->course?->title ?? 'Course' }}</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $cert->certificate_number }} &middot; Issued {{ $cert->issued_at?->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('certificates.pdf', ['locale' => app()->getLocale(), 'certificate' => $cert->id]) }}"
                       class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition no-underline">
                        Download
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.practitioner>

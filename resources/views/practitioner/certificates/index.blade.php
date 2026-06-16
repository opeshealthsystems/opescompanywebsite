<x-layouts.practitioner title="Certificates">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">My Certificates</h1>
        <p class="text-slate-400 mt-1">Download certificates for courses you have completed.</p>
    </div>

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

@php $locale = app()->getLocale(); @endphp

<x-layouts.app
    title="OPES Training &amp; Certification"
    description="Learn to get the most out of OPES products with structured courses and certification paths.">

{{-- ── PAGE HEADER ───────────────────────────────────────────────── --}}
<div class="pi-header">
    <div class="section-label" style="justify-content:center;margin-bottom:16px">
        <i data-lucide="graduation-cap" style="width:12px;height:12px"></i>
        Training Academy
    </div>
    <h1 class="pi-title">OPES Training &amp; Certification</h1>
    <p class="pi-sub">Structured courses to help your team master OPES products — from first login to advanced workflows. Earn certificates as you go.</p>
</div>

{{-- ── COURSE GRID ──────────────────────────────────────────────── --}}
<div class="section pi-section">
    @if($courses->isEmpty())
        <div style="text-align:center;padding:64px 16px;color:var(--text-muted)">
            <i data-lucide="book-open" style="width:40px;height:40px;color:var(--text-faint);margin-bottom:16px"></i>
            <p style="font-size:16px">No courses are available right now. Please check back soon.</p>
        </div>
    @else
        <div class="pi-grid">
            @foreach($courses as $course)
            <a href="{{ route('courses.show', ['locale' => $locale, 'course' => $course->slug]) }}"
               class="pi-card" style="flex-direction:column;align-items:stretch;overflow:hidden;padding:0">

                {{-- Cover --}}
                @if($course->cover_image)
                    <img src="{{ Storage::url($course->cover_image) }}" alt="{{ $course->getLocalizedTitle($locale) }}"
                         style="width:100%;height:150px;object-fit:cover;display:block">
                @else
                    <div style="width:100%;height:150px;background:linear-gradient(135deg,rgba(0,200,150,0.18),rgba(26,111,232,0.18));display:flex;align-items:center;justify-content:center">
                        <i data-lucide="graduation-cap" style="width:36px;height:36px;color:#00C896"></i>
                    </div>
                @endif

                <div class="pi-card-body" style="padding:18px">
                    {{-- Level badge --}}
                    <div style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:600;letter-spacing:.03em;text-transform:uppercase;color:#00C896;background:rgba(0,200,150,0.1);border:1px solid rgba(0,200,150,0.2);border-radius:999px;padding:4px 10px;margin-bottom:10px">
                        {{ \App\Models\Course::levelOptions()[$course->level] ?? ucfirst($course->level) }}
                    </div>

                    <div class="pi-card-name">{{ $course->getLocalizedTitle($locale) }}</div>

                    @if($course->description)
                        <p class="pi-card-tagline">{{ Str::limit(strip_tags($course->description), 110) }}</p>
                    @endif

                    {{-- Meta row --}}
                    <div style="display:flex;gap:16px;margin-top:14px;font-size:12.5px;color:var(--text-muted)">
                        @if($course->duration_hours)
                        <span style="display:inline-flex;align-items:center;gap:5px">
                            <i data-lucide="clock" style="width:13px;height:13px"></i>{{ $course->duration_hours }}h
                        </span>
                        @endif
                        <span style="display:inline-flex;align-items:center;gap:5px">
                            <i data-lucide="users" style="width:13px;height:13px"></i>{{ $course->enrollments_count }} enrolled
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    @endif
</div>

{{-- ── CTA ───────────────────────────────────────────────────────── --}}
<div class="demo-section">
    <div class="section-label" style="justify-content:center;color:#00C896;margin-bottom:12px">
        <i data-lucide="award" style="width:13px;height:13px"></i>
        Get Certified
    </div>
    <h2>Empower your team with OPES skills</h2>
    <p>Sign in to enroll, track your progress, and download certificates of completion.</p>
    <a href="{{ url($locale.'/contact') }}" class="btn-primary" style="display:inline-flex;margin-top:8px">
        Talk to our team
        <i data-lucide="arrow-right" style="width:15px;height:15px"></i>
    </a>
</div>

</x-layouts.app>

@php
    $locale = app()->getLocale();
    $levelLabel = \App\Models\Course::levelOptions()[$course->level] ?? ucfirst($course->level);
@endphp

<x-layouts.app
    :title="$course->getLocalizedTitle($locale) . ' — OPES Training'"
    :description="$course->description ? Str::limit(strip_tags($course->description), 155) : null">

{{-- ── BREADCRUMB ──────────────────────────────────────────────── --}}
<div class="pd-breadcrumb">
    <a href="{{ url($locale) }}">Home</a>
    <span>›</span>
    <a href="{{ route('courses.index', ['locale' => $locale]) }}">Courses</a>
    <span>›</span>
    <span class="pd-breadcrumb-current">{{ $course->getLocalizedTitle($locale) }}</span>
</div>

{{-- ── HERO ────────────────────────────────────────────────────── --}}
<div class="pd-hero">
    <div class="pd-hero-inner">
        <div>
            <div class="pd-badge">
                <i data-lucide="bar-chart-3" style="width:10px;height:10px"></i>
                {{ $levelLabel }}
            </div>
            <h1 class="pd-hero-name">{{ $course->getLocalizedTitle($locale) }}</h1>

            <div style="display:flex;gap:20px;margin:16px 0 8px;font-size:13.5px;color:#94a3b8">
                @if($course->duration_hours)
                <span style="display:inline-flex;align-items:center;gap:6px">
                    <i data-lucide="clock" style="width:15px;height:15px"></i>{{ $course->duration_hours }} hours
                </span>
                @endif
                <span style="display:inline-flex;align-items:center;gap:6px">
                    <i data-lucide="users" style="width:15px;height:15px"></i>{{ $course->enrollments()->count() }} enrolled
                </span>
                <span style="display:inline-flex;align-items:center;gap:6px">
                    <i data-lucide="book-open" style="width:15px;height:15px"></i>{{ $course->lessons->count() }} lessons
                </span>
            </div>

            <div class="pd-hero-ctas">
                @auth
                    @if($enrollment)
                        <a href="{{ url('/' . $locale . '/' . (auth()->user()->hasRole('practitioner') ? 'practitioner' : 'customer') . '/courses/' . $course->slug) }}" class="btn-primary">
                            <i data-lucide="play-circle" style="width:15px;height:15px"></i>
                            Continue Learning
                        </a>
                    @else
                        <a href="{{ url('/' . $locale . '/' . (auth()->user()->hasRole('practitioner') ? 'practitioner' : 'customer') . '/courses/' . $course->slug) }}" class="btn-primary">
                            <i data-lucide="plus-circle" style="width:15px;height:15px"></i>
                            Enroll Now
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-primary">
                        <i data-lucide="log-in" style="width:15px;height:15px"></i>
                        Sign in to Enroll
                    </a>
                @endauth
            </div>

            @auth
                @if($enrollment)
                    <p style="margin-top:14px;display:inline-flex;align-items:center;gap:7px;font-size:13.5px;color:#00C896">
                        <i data-lucide="check-circle" style="width:15px;height:15px"></i>
                        You're enrolled in this course.
                    </p>
                @endif
            @endauth
        </div>
    </div>
</div>

{{-- ── COVER BANNER ────────────────────────────────────────────── --}}
@if($course->cover_image)
<div class="section" style="padding-top:0">
    <img src="{{ Storage::url($course->cover_image) }}" alt="{{ $course->getLocalizedTitle($locale) }}"
         style="width:100%;max-height:380px;object-fit:cover;border-radius:14px;display:block">
</div>
@endif

{{-- ── DESCRIPTION ─────────────────────────────────────────────── --}}
@if($course->description)
<div class="section">
    <h2 class="pi-cat-title" style="margin-bottom:14px">About this course</h2>
    <div style="color:#cbd5e1;line-height:1.75;font-size:15px;max-width:760px">
        {!! nl2br(e($locale === 'fr' && $course->description_fr ? $course->description_fr : $course->description)) !!}
    </div>
</div>
@endif

{{-- ── LESSON LIST ─────────────────────────────────────────────── --}}
<div class="section">
    <h2 class="pi-cat-title" style="margin-bottom:14px">What you'll learn</h2>

    @if($course->lessons->isEmpty())
        <p style="color:#94a3b8">Lessons are being prepared for this course.</p>
    @else
        <div style="display:flex;flex-direction:column;gap:10px;max-width:760px">
            @foreach($course->lessons as $i => $lesson)
            <div style="display:flex;align-items:center;gap:16px;padding:14px 18px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px">
                <div style="flex:0 0 32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;background:rgba(0,200,150,0.1);color:#00C896;font-weight:700;font-size:13px">
                    {{ $i + 1 }}
                </div>
                <div style="flex:1 1 auto;min-width:0">
                    <div style="color:#e2e8f0;font-weight:600;font-size:14.5px">
                        {{ $locale === 'fr' && $lesson->title_fr ? $lesson->title_fr : $lesson->title }}
                    </div>
                </div>
                @if($lesson->duration_minutes)
                <div style="flex:0 0 auto;display:inline-flex;align-items:center;gap:5px;color:#94a3b8;font-size:12.5px">
                    <i data-lucide="clock" style="width:13px;height:13px"></i>{{ $lesson->duration_minutes }} min
                </div>
                @endif
                <i data-lucide="lock" style="width:14px;height:14px;color:#475569;flex:0 0 auto"></i>
            </div>
            @endforeach
        </div>
        @guest
        <p style="margin-top:16px;font-size:13px;color:#94a3b8">
            <i data-lucide="lock" style="width:13px;height:13px;vertical-align:-2px"></i>
            Lesson content unlocks after you sign in and enroll.
        </p>
        @endguest
    @endif
</div>

</x-layouts.app>

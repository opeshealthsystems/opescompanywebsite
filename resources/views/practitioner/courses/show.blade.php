<x-layouts.practitioner title="{{ $course->title }}">
    <div class="mb-6">
        <a href="{{ route('practitioner.courses', ['locale' => app()->getLocale()]) }}" class="text-sm text-slate-400 hover:text-white no-underline">&larr; Back to courses</a>
        <h1 class="text-2xl font-bold text-white mt-2">{{ $course->title }}</h1>
        <div class="flex items-center gap-2 mt-2">
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-800 text-slate-300 capitalize">{{ $course->level }}</span>
            @if($course->duration_hours)
                <span class="text-xs text-slate-500">{{ $course->duration_hours }} hours</span>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-900 border border-red-700 text-red-200 text-sm px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    @if($course->description)
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 mb-6 text-slate-300">
            {!! nl2br(e($course->description)) !!}
        </div>
    @endif

    @if($enrollment)
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-5 mb-6">
            <div class="flex justify-between text-sm text-slate-400 mb-1">
                <span>Your progress</span>
                <span>{{ $enrollment->progressPercent() }}%</span>
            </div>
            <div class="h-2.5 bg-slate-800 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: {{ $enrollment->progressPercent() }}%"></div>
            </div>
            @if($enrollment->isComplete())
                <div class="mt-4 flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-900 text-emerald-200">Completed</span>
                    @if($enrollment->certificate)
                        <a href="{{ route('certificates.pdf', ['locale' => app()->getLocale(), 'certificate' => $enrollment->certificate->id]) }}"
                           class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition no-underline">
                            Download Certificate
                        </a>
                    @endif
                </div>
            @endif
        </div>
    @else
        <form method="POST" action="{{ route('practitioner.courses.enroll', ['locale' => app()->getLocale(), 'course' => $course->slug]) }}" class="mb-6">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition border-0 cursor-pointer">
                Enroll in this course
            </button>
        </form>
    @endif

    <h2 class="text-lg font-semibold text-white mb-3">Lessons</h2>
    @if($course->lessons->isEmpty())
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6 text-center text-slate-500">No lessons yet.</div>
    @else
        <div class="space-y-2">
            @foreach($course->lessons as $i => $lesson)
                @php($done = in_array($lesson->id, $completedLessonIds))
                <div class="bg-slate-900 rounded-xl border border-slate-800 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($done)
                            <i data-lucide="check-circle-2" style="width:18px;height:18px" class="text-emerald-400"></i>
                        @else
                            <span class="w-5 h-5 rounded-full border border-slate-600 inline-flex items-center justify-center text-xs text-slate-500">{{ $i + 1 }}</span>
                        @endif
                        <span class="text-slate-200">{{ $lesson->title }}</span>
                        @if($lesson->duration_minutes)
                            <span class="text-xs text-slate-500">{{ $lesson->duration_minutes }} min</span>
                        @endif
                    </div>
                    @if($enrollment)
                        <a href="{{ route('practitioner.lessons.show', ['locale' => app()->getLocale(), 'course' => $course->slug, 'lesson' => $lesson->id]) }}"
                           class="text-sm text-emerald-400 hover:text-emerald-300 no-underline">
                            {{ $done ? 'Review' : 'Start' }} &rarr;
                        </a>
                    @else
                        <i data-lucide="lock" style="width:16px;height:16px" class="text-slate-600"></i>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.practitioner>

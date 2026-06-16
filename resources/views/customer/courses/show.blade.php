<x-layouts.customer title="{{ $course->title }}">
    <div class="mb-6">
        <a href="{{ route('customer.courses', ['locale' => app()->getLocale()]) }}" class="text-sm text-slate-500 hover:text-slate-700 no-underline">&larr; Back to courses</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $course->title }}</h1>
        <div class="flex items-center gap-2 mt-2">
            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 capitalize">{{ $course->level }}</span>
            @if($course->duration_hours)
                <span class="text-xs text-slate-400">{{ $course->duration_hours }} hours</span>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    @if($course->description)
        <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 text-slate-600">
            {!! nl2br(e($course->description)) !!}
        </div>
    @endif

    @if($enrollment)
        <div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
            <div class="flex justify-between text-sm text-slate-500 mb-1">
                <span>Your progress</span>
                <span>{{ $enrollment->progressPercent() }}%</span>
            </div>
            <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: {{ $enrollment->progressPercent() }}%"></div>
            </div>
            @if($enrollment->isComplete())
                <div class="mt-4 flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Completed</span>
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
        <form method="POST" action="{{ route('customer.courses.enroll', ['locale' => app()->getLocale(), 'course' => $course->slug]) }}" class="mb-6">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition border-0 cursor-pointer">
                Enroll in this course
            </button>
        </form>
    @endif

    <h2 class="text-lg font-semibold text-slate-900 mb-3">Lessons</h2>
    @if($course->lessons->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-6 text-center text-slate-400">No lessons yet.</div>
    @else
        <div class="space-y-2">
            @foreach($course->lessons as $i => $lesson)
                @php($done = in_array($lesson->id, $completedLessonIds))
                <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($done)
                            <i data-lucide="check-circle-2" style="width:18px;height:18px" class="text-emerald-500"></i>
                        @else
                            <span class="w-5 h-5 rounded-full border border-slate-300 inline-flex items-center justify-center text-xs text-slate-400">{{ $i + 1 }}</span>
                        @endif
                        <span class="text-slate-700">{{ $lesson->title }}</span>
                        @if($lesson->duration_minutes)
                            <span class="text-xs text-slate-400">{{ $lesson->duration_minutes }} min</span>
                        @endif
                    </div>
                    @if($enrollment)
                        <a href="{{ route('customer.lessons.show', ['locale' => app()->getLocale(), 'course' => $course->slug, 'lesson' => $lesson->id]) }}"
                           class="text-sm text-emerald-600 hover:text-emerald-700 no-underline">
                            {{ $done ? 'Review' : 'Start' }} &rarr;
                        </a>
                    @else
                        <i data-lucide="lock" style="width:16px;height:16px" class="text-slate-300"></i>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.customer>

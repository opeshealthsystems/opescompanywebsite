<x-layouts.practitioner title="{{ $lesson->title }}">
    @php($done = in_array($lesson->id, $completedLessonIds))
    <div class="mb-6">
        <a href="{{ route('practitioner.courses.show', ['locale' => app()->getLocale(), 'course' => $course->slug]) }}" class="text-sm text-slate-400 hover:text-white no-underline">&larr; {{ $course->title }}</a>
        <h1 class="text-2xl font-bold text-white mt-2">{{ $lesson->title }}</h1>
    </div>

    @if($lesson->video_url)
        <div class="bg-black rounded-xl border border-slate-800 overflow-hidden mb-6" style="aspect-ratio:16/9">
            <iframe src="{{ $lesson->video_url }}" class="w-full h-full" style="border:0" allowfullscreen></iframe>
        </div>
    @endif

    @if($lesson->content)
        <div class="bg-slate-900 rounded-xl border border-slate-800 p-6 mb-6 text-slate-300 leading-relaxed">
            {!! nl2br(e($lesson->content)) !!}
        </div>
    @endif

    <div class="flex items-center justify-between gap-3 mb-6">
        <div>
            @if($prevLesson)
                <a href="{{ route('practitioner.lessons.show', ['locale' => app()->getLocale(), 'course' => $course->slug, 'lesson' => $prevLesson->id]) }}"
                   class="px-4 py-2 bg-slate-800 text-slate-200 text-sm rounded-lg hover:bg-slate-700 transition no-underline">&larr; Previous</a>
            @endif
        </div>

        <form method="POST" action="{{ route('practitioner.lessons.done', ['locale' => app()->getLocale(), 'course' => $course->slug, 'lesson' => $lesson->id]) }}">
            @csrf
            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition border-0 cursor-pointer">
                {{ $done ? 'Mark Complete Again' : 'Mark Complete' }}
            </button>
        </form>

        <div>
            @if($nextLesson)
                <a href="{{ route('practitioner.lessons.show', ['locale' => app()->getLocale(), 'course' => $course->slug, 'lesson' => $nextLesson->id]) }}"
                   class="px-4 py-2 bg-slate-800 text-slate-200 text-sm rounded-lg hover:bg-slate-700 transition no-underline">Next &rarr;</a>
            @endif
        </div>
    </div>
</x-layouts.practitioner>

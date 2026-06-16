<x-layouts.customer title="Courses">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Courses</h1>
        <p class="text-slate-500 mt-1">Expand your skills with our training catalog.</p>
    </div>

    @if($courses->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-400">
            No courses available at this time. Check back later.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($courses as $course)
                @php($enrollment = $myEnrollments->get($course->id))
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 capitalize">{{ $course->level }}</span>
                        @if($course->duration_hours)
                            <span class="text-xs text-slate-400">{{ $course->duration_hours }}h</span>
                        @endif
                    </div>
                    <h3 class="font-semibold text-slate-800 text-lg">{{ $course->title }}</h3>
                    @if($course->description)
                        <p class="text-sm text-slate-500 mt-1 flex-1">{{ \Illuminate\Support\Str::limit($course->description, 110) }}</p>
                    @else
                        <div class="flex-1"></div>
                    @endif

                    <div class="mt-4">
                        @if($enrollment)
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-slate-500 mb-1">
                                    <span>Progress</span>
                                    <span>{{ $enrollment->progressPercent() }}%</span>
                                </div>
                                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500" style="width: {{ $enrollment->progressPercent() }}%"></div>
                                </div>
                            </div>
                            <a href="{{ route('customer.courses.show', ['locale' => app()->getLocale(), 'course' => $course->slug]) }}"
                               class="block text-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition no-underline">
                                {{ $enrollment->isComplete() ? 'Review' : 'Continue' }}
                            </a>
                        @else
                            <a href="{{ route('customer.courses.show', ['locale' => app()->getLocale(), 'course' => $course->slug]) }}"
                               class="block text-center px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200 transition no-underline">
                                View Course
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.customer>

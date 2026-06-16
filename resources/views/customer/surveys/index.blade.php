<x-layouts.customer title="Surveys">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">Surveys</h1>
        <p class="text-slate-500 mt-1">Help us improve by completing available surveys.</p>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif

    @if($surveys->isEmpty())
        <div class="bg-white rounded-xl border border-slate-200 p-10 text-center text-slate-400">
            No active surveys at this time. Check back later.
        </div>
    @else
        <div class="space-y-4">
            @foreach($surveys as $survey)
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-slate-800">{{ $survey->title }}</h3>
                        @if($survey->description)
                            <p class="text-sm text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($survey->description, 100) }}</p>
                        @endif
                    </div>
                    <div class="ml-4 shrink-0">
                        @if(in_array($survey->id, $myResponseIds))
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">Completed</span>
                        @else
                            <a href="{{ route('customer.surveys.show', ['locale' => app()->getLocale(), 'survey' => $survey->id]) }}"
                               class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition">
                                Take Survey
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.customer>

<x-layouts.practitioner title="{{ $survey->title }}">
    <div class="mb-6">
        <a href="{{ route('practitioner.surveys', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-emerald-600 hover:underline">← Back to Surveys</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $survey->title }}</h1>
        @if($survey->description)
            <p class="text-slate-500 mt-1">{{ $survey->description }}</p>
        @endif
    </div>

    @if($response->isSubmitted())
        <div class="bg-green-50 border border-green-200 rounded-xl p-8 text-center">
            <p class="text-green-700 font-semibold text-lg">Thank you!</p>
            <p class="text-green-600 mt-1">You have already submitted this survey.</p>
        </div>
    @else
        <form method="POST" action="{{ route('practitioner.surveys.submit', ['locale' => app()->getLocale(), 'survey' => $survey->id]) }}" class="space-y-5">
            @csrf
            @foreach($survey->questions as $question)
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <label class="block font-medium text-slate-800 mb-3">
                        {{ $question->question }}
                        @if($question->is_required)<span class="text-red-500 ml-1">*</span>@endif
                    </label>

                    @if($question->type === 'text')
                        <textarea name="q_{{ $question->id }}" rows="3"
                            class="w-full border border-slate-300 rounded-lg p-3 text-sm"
                            @if($question->is_required) required @endif
                        >{{ old("q_{$question->id}", $existingAnswers[$question->id] ?? '') }}</textarea>

                    @elseif($question->type === 'rating')
                        <div class="flex gap-4">
                            @foreach(range(1,5) as $r)
                                <label class="flex flex-col items-center gap-1 cursor-pointer">
                                    <input type="radio" name="q_{{ $question->id }}" value="{{ $r }}"
                                        {{ old("q_{$question->id}") == $r ? 'checked' : '' }}
                                        @if($question->is_required) required @endif>
                                    <span class="text-sm text-slate-600">{{ $r }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($question->type === 'multiple_choice')
                        <div class="space-y-2">
                            @foreach($question->options ?? [] as $option)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="q_{{ $question->id }}" value="{{ $option }}"
                                        {{ old("q_{$question->id}") === $option ? 'checked' : '' }}
                                        @if($question->is_required) required @endif>
                                    <span class="text-sm text-slate-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($question->type === 'yes_no')
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="q_{{ $question->id }}" value="yes"
                                    {{ old("q_{$question->id}") === 'yes' ? 'checked' : '' }}
                                    @if($question->is_required) required @endif>
                                <span class="text-sm text-slate-700">Yes</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="q_{{ $question->id }}" value="no"
                                    {{ old("q_{$question->id}") === 'no' ? 'checked' : '' }}>
                                <span class="text-sm text-slate-700">No</span>
                            </label>
                        </div>
                    @endif

                    @error("q_{$question->id}")
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <button type="submit"
                class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition">
                Submit Survey
            </button>
        </form>
    @endif
</x-layouts.practitioner>

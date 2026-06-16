<x-layouts.practitioner title="Submit Findings">
    <div class="mb-6">
        <a href="{{ route('practitioner.applications.show', ['locale' => app()->getLocale(), 'application' => $application->id]) }}"
           class="text-sm text-slate-400 hover:text-white no-underline transition-colors flex items-center gap-1 w-fit">
            <i data-lucide="arrow-left" style="width:14px;height:14px"></i> Back to Application
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white mb-1">Submit Findings</h1>
        <p class="text-slate-400 text-sm">{{ $application->program->title }}</p>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl p-6">
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-700 rounded-lg px-4 py-3 mb-5 text-sm text-red-300">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('practitioner.findings.store', ['locale' => app()->getLocale(), 'application' => $application->id]) }}">
            @csrf

            <div class="mb-6">
                <h2 class="text-base font-semibold text-white mb-4">Ratings <span class="text-slate-500 font-normal text-sm">(optional, 1 = Poor, 5 = Excellent)</span></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="overall_rating" class="block text-sm font-medium text-slate-300 mb-1.5">Overall Rating</label>
                        <select id="overall_rating" name="overall_rating"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">— Select —</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected(old('overall_rating') == $i)>{{ $i }} — {{ ['','Poor','Fair','Good','Very Good','Excellent'][$i] }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="wait_time_rating" class="block text-sm font-medium text-slate-300 mb-1.5">Wait Time Rating</label>
                        <select id="wait_time_rating" name="wait_time_rating"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">— Select —</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected(old('wait_time_rating') == $i)>{{ $i }} — {{ ['','Poor','Fair','Good','Very Good','Excellent'][$i] }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="data_integrity_rating" class="block text-sm font-medium text-slate-300 mb-1.5">Data Integrity Rating</label>
                        <select id="data_integrity_rating" name="data_integrity_rating"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">— Select —</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected(old('data_integrity_rating') == $i)>{{ $i }} — {{ ['','Poor','Fair','Good','Very Good','Excellent'][$i] }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="usability_rating" class="block text-sm font-medium text-slate-300 mb-1.5">Usability Rating</label>
                        <select id="usability_rating" name="usability_rating"
                            class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500">
                            <option value="">— Select —</option>
                            @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" @selected(old('usability_rating') == $i)>{{ $i }} — {{ ['','Poor','Fair','Good','Very Good','Excellent'][$i] }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-5">
                <label for="findings_text" class="block text-sm font-medium text-slate-300 mb-1.5">Findings <span class="text-slate-500">(optional)</span></label>
                <textarea id="findings_text" name="findings_text" rows="8"
                    placeholder="Share your detailed observations, insights, and recommendations..."
                    class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 resize-y"
                    maxlength="5000">{{ old('findings_text') }}</textarea>
                <p class="text-xs text-slate-500 mt-1">Max 5,000 characters.</p>
            </div>

            <div class="mb-6">
                <label for="video_url" class="block text-sm font-medium text-slate-300 mb-1.5">Video URL <span class="text-slate-500">(optional)</span></label>
                <input type="url" id="video_url" name="video_url"
                    value="{{ old('video_url') }}"
                    placeholder="https://www.youtube.com/watch?v=..."
                    class="w-full bg-slate-800 border border-slate-700 rounded-lg px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                    maxlength="500">
                <p class="text-xs text-slate-500 mt-1">Link to a supporting video recording.</p>
            </div>

            <div class="flex items-center gap-4">
                <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm rounded-lg transition-colors border-0 cursor-pointer">
                    Submit Findings
                </button>
                <a href="{{ route('practitioner.applications.show', ['locale' => app()->getLocale(), 'application' => $application->id]) }}"
                   class="text-sm text-slate-400 hover:text-white no-underline transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-layouts.practitioner>

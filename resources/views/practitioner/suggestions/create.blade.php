<x-layouts.practitioner title="New Suggestion">
    <div class="mb-6">
        <a href="{{ route('practitioner.suggestions', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-emerald-600 hover:underline">← Back to Suggestions</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Submit a Suggestion</h1>
        <p class="text-slate-500 mt-1">Help us improve OPES by sharing your ideas and feedback.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form method="POST" action="{{ route('practitioner.suggestions.store', ['locale' => app()->getLocale()]) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select name="category" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select category...</option>
                    @foreach($categoryOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('category')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Details <span class="text-red-500">*</span></label>
                <textarea name="body" rows="6" required minlength="20"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Describe your idea in detail (at least 20 characters)...">{{ old('body') }}</textarea>
                @error('body')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition">
                Submit Suggestion
            </button>
        </form>
    </div>
</x-layouts.practitioner>

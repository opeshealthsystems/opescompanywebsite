<x-layouts.practitioner title="Report a Bug">
    <div class="mb-6">
        <a href="{{ route('practitioner.bug-reports', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-emerald-600 hover:underline">← Back to Bug Reports</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Report a Bug</h1>
        <p class="text-slate-500 mt-1">Help us improve OPES by reporting bugs you find while testing.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form method="POST" action="{{ route('practitioner.bug-reports.store', ['locale' => app()->getLocale()]) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Severity <span class="text-red-500">*</span></label>
                <select name="severity" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($severityOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('severity', 'medium') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('severity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Product <span class="text-slate-400">(optional)</span></label>
                <input type="text" name="product_slug" value="{{ old('product_slug') }}" maxlength="100"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="e.g. opescare">
                @error('product_slug')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="5" required minlength="10"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Describe the bug in detail (at least 10 characters)...">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Steps to Reproduce <span class="text-slate-400">(optional)</span></label>
                <textarea name="steps_to_reproduce" rows="4"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="1. Go to...&#10;2. Click on...&#10;3. See error">{{ old('steps_to_reproduce') }}</textarea>
                @error('steps_to_reproduce')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Screenshot URL <span class="text-slate-400">(optional)</span></label>
                <input type="url" name="screenshot_url" value="{{ old('screenshot_url') }}" maxlength="500"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="https://...">
                @error('screenshot_url')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <button type="submit"
                class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition">
                Submit Bug Report
            </button>
        </form>
    </div>
</x-layouts.practitioner>

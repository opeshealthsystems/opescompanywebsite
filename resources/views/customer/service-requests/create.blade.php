<x-layouts.customer title="New Service Request">
    <div class="mb-6">
        <a href="{{ route('customer.service-requests', ['locale' => app()->getLocale()]) }}"
           class="text-sm text-emerald-600 hover:underline">← Back to Service Requests</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">Schedule a Service Visit</h1>
        <p class="text-slate-500 mt-1">Request an OPES technician visit for installation, maintenance, or training.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form method="POST" action="{{ route('customer.service-requests.store', ['locale' => app()->getLocale()]) }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Service Type <span class="text-red-500">*</span></label>
                <select name="type" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select type...</option>
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Preferred Date <span class="text-red-500">*</span></label>
                <input type="date" name="preferred_date" value="{{ old('preferred_date') }}" required
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('preferred_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Preferred Time (optional)</label>
                <input type="time" name="preferred_time" value="{{ old('preferred_time') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Facility / Location (optional)</label>
                <input type="text" name="location" value="{{ old('location') }}" maxlength="200"
                    placeholder="e.g. Ward 3, Building A"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Description (optional)</label>
                <textarea name="description" rows="4" maxlength="1000"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Describe the issue or what you need...">{{ old('description') }}</textarea>
            </div>

            <button type="submit"
                class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition">
                Submit Request
            </button>
        </form>
    </div>
</x-layouts.customer>

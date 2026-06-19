<x-layouts.practitioner title="New Session">
@php $locale = app()->getLocale(); @endphp

<div class="mb-6">
    <a href="{{ route('practitioner.validation.sessions.index', ['locale' => $locale]) }}"
       class="text-sm text-emerald-400 hover:underline">&larr; Back to Daily Sessions</a>
    <h1 class="text-2xl font-bold text-white mt-2">New Test Session</h1>
    <p class="text-slate-400 text-sm mt-1">Record the testing you completed. Only your cohort's assigned products are available.</p>
</div>

<div class="bg-slate-900 border border-slate-800 rounded-xl p-6 max-w-2xl">
    <form method="POST" action="{{ route('practitioner.validation.sessions.store', ['locale' => $locale]) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Product <span class="text-red-400">*</span></label>
            <select name="validation_product_id" required
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Select a product…</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ (int) old('validation_product_id') === $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                @endforeach
            </select>
            @error('validation_product_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Module <span class="text-red-400">*</span></label>
            <select name="validation_module_id" required
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Select a module…</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ (int) old('validation_module_id') === $module->id ? 'selected' : '' }}>{{ $module->name }}</option>
                @endforeach
            </select>
            @error('validation_module_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Workflow <span class="text-red-400">*</span></label>
            <select name="validation_workflow_id" required
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">Select a workflow…</option>
                @foreach($workflows as $workflow)
                    <option value="{{ $workflow->id }}" {{ (int) old('validation_workflow_id') === $workflow->id ? 'selected' : '' }}>{{ $workflow->name }}</option>
                @endforeach
            </select>
            @error('validation_workflow_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Facility Context <span class="text-slate-500">(optional)</span></label>
            <input type="text" name="facility_context" value="{{ old('facility_context') }}" maxlength="200"
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                placeholder="e.g. District hospital, outpatient ward">
            @error('facility_context')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Date <span class="text-red-400">*</span></label>
            <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @error('date')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Start Time <span class="text-slate-500">(optional)</span></label>
                <input type="time" name="start_time" value="{{ old('start_time') }}"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('start_time')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">End Time <span class="text-slate-500">(optional)</span></label>
                <input type="time" name="end_time" value="{{ old('end_time') }}"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                @error('end_time')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Tasks Completed <span class="text-red-400">*</span></label>
            <input type="number" name="tasks_completed" value="{{ old('tasks_completed', 0) }}" min="0" max="999" required
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @error('tasks_completed')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Comments <span class="text-slate-500">(optional)</span></label>
            <textarea name="comments" rows="4" maxlength="3000"
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                placeholder="Notes about today's testing…">{{ old('comments') }}</textarea>
            @error('comments')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-300 mb-1">Screenshots <span class="text-slate-500">(optional)</span></label>
            <input type="file" name="screenshots[]" multiple accept="image/jpeg,image/png,application/pdf"
                class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-emerald-600 file:text-white file:text-sm file:font-semibold hover:file:bg-emerald-700 file:cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <p class="text-xs text-slate-500 mt-1">JPG, PNG or PDF. Max 5 MB each.</p>
            @error('screenshots.*')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <button type="submit"
            class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition">
            Record Session
        </button>
    </form>
</div>
</x-layouts.practitioner>

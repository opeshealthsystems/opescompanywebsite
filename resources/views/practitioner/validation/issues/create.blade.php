<x-layouts.practitioner title="Report Issue">
@php $locale = app()->getLocale(); @endphp

<div class="mb-6">
    <a href="{{ route('practitioner.validation.issues.index', ['locale' => $locale]) }}"
       class="text-sm text-emerald-400 hover:underline">&larr; Back to Issue Reports</a>
    <h1 class="text-2xl font-bold text-white mt-2">Report an Issue</h1>
    <p class="text-slate-400 text-sm mt-1">File a formal issue report. Only your cohort's assigned products are available.</p>
</div>

<form method="POST" action="{{ route('practitioner.validation.issues.store', ['locale' => $locale]) }}" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- LEFT: Classification --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-5">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wide">Classification</h2>

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
                <label class="block text-sm font-medium text-slate-300 mb-1">Test Case <span class="text-slate-500">(optional)</span></label>
                <select name="validation_test_case_id"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">— none —</option>
                    @foreach($testCases as $testCase)
                        <option value="{{ $testCase->id }}" {{ (int) old('validation_test_case_id') === $testCase->id ? 'selected' : '' }}>{{ $testCase->title ?? $testCase->name ?? ('Test case #'.$testCase->id) }}</option>
                    @endforeach
                </select>
                @error('validation_test_case_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Issue Type <span class="text-red-400">*</span></label>
                <select name="issue_type" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select a type…</option>
                    @foreach(\App\Models\IssueReport::issueTypeOptions() as $value => $label)
                        <option value="{{ $value }}" {{ old('issue_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('issue_type')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Severity <span class="text-red-400">*</span></label>
                <select name="severity" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Select severity…</option>
                    @foreach(\App\Models\IssueReport::severityOptions() as $value => $label)
                        <option value="{{ $value }}" {{ old('severity') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('severity')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- RIGHT: Details --}}
        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 space-y-5">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wide">Details</h2>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" maxlength="200" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Short summary of the issue">
                @error('title')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Description <span class="text-red-400">*</span></label>
                <textarea name="description" rows="4" maxlength="5000" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="What is the problem?">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Steps to Reproduce <span class="text-red-400">*</span></label>
                <textarea name="steps_to_reproduce" rows="4" maxlength="5000" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="1. … 2. … 3. …">{{ old('steps_to_reproduce') }}</textarea>
                @error('steps_to_reproduce')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Expected Result <span class="text-red-400">*</span></label>
                <textarea name="expected_result" rows="2" maxlength="2000" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="What should have happened?">{{ old('expected_result') }}</textarea>
                @error('expected_result')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Actual Result <span class="text-red-400">*</span></label>
                <textarea name="actual_result" rows="2" maxlength="2000" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="What actually happened?">{{ old('actual_result') }}</textarea>
                @error('actual_result')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Clinical Impact <span class="text-red-400">*</span></label>
                <textarea name="clinical_impact" rows="2" maxlength="2000" required
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="How does this affect patient care or clinical workflow?">{{ old('clinical_impact') }}</textarea>
                @error('clinical_impact')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Recommendation <span class="text-slate-500">(optional)</span></label>
                <textarea name="recommendation" rows="2" maxlength="2000"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="Suggested fix or improvement">{{ old('recommendation') }}</textarea>
                @error('recommendation')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-300 mb-1">Attachments <span class="text-slate-500">(optional)</span></label>
                <input type="file" name="attachments[]" multiple accept="image/jpeg,image/png,application/pdf"
                    class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 text-sm text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-emerald-600 file:text-white file:text-sm file:font-semibold hover:file:bg-emerald-700 file:cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p class="text-xs text-slate-500 mt-1">JPG, PNG or PDF. Max 10 MB each.</p>
                @error('attachments.*')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="mt-6 max-w-xl">
        <button type="submit"
            class="w-full py-3 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition">
            Submit Issue Report
        </button>
    </div>
</form>
</x-layouts.practitioner>

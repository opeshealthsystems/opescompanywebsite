<x-layouts.customer title="New Support Ticket">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">New Support Ticket</h1>
            <p class="cp-page-subtitle">Describe your issue and we'll get back to you</p>
        </div>
        <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">&larr; Back</a>
    </div>

    <div class="cp-section-card">
        <form method="POST" action="{{ route('customer.tickets.store', ['locale' => app()->getLocale()]) }}">
            @csrf

            <div style="margin-bottom:1.25rem;">
                <label style="display:block;color:var(--text-muted);font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Subject *</label>
                <input type="text" name="subject" value="{{ old('subject') }}" required maxlength="255"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;"
                    placeholder="Brief description of your issue">
                @error('subject')
                    <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
                <div>
                    <label style="display:block;color:var(--text-muted);font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Category *</label>
                    <select name="type" required
                        style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;">
                        @foreach(\App\Models\Ticket::typeOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label style="display:block;color:var(--text-muted);font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Priority *</label>
                    <select name="priority" required
                        style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;">
                        @foreach(\App\Models\Ticket::priorityOptions() as $value => $label)
                            <option value="{{ $value }}" {{ old('priority', 'medium') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('priority')
                        <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block;color:var(--text-muted);font-size:0.8125rem;font-weight:600;margin-bottom:0.5rem;">Description *</label>
                <textarea name="description" required maxlength="10000" rows="7"
                    style="width:100%;background:#0f172a;border:1px solid #334155;border-radius:8px;padding:0.625rem 0.875rem;color:#e2e8f0;font-size:0.875rem;box-sizing:border-box;resize:vertical;"
                    placeholder="Please provide as much detail as possible...">{{ old('description') }}</textarea>
                @error('description')
                    <p style="color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex;gap:0.75rem;">
                <button type="submit" class="cp-btn-primary">Submit Ticket</button>
                <a href="{{ route('customer.tickets', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.customer>

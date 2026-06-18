<x-layouts.hr title="Departments">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Departments</h1>
        <p class="cp-page-subtitle">{{ $departments->count() }} departments · Headcount and leads</p>
    </div>
</div>

<div class="cp-section-card">
    <div style="overflow-x:auto">
    <table class="portal-table">
        <thead>
            <tr><th>Department</th><th>Code</th><th>Head</th><th>Parent</th><th>Members</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($departments as $dept)
            <tr>
                <td style="font-weight:500;color:#f1f5f9">{{ $dept->name }}</td>
                <td style="font-family:monospace;font-size:.8125rem;color:#64748b">{{ $dept->code }}</td>
                <td style="color:#94a3b8">{{ $dept->head->name ?? '—' }}</td>
                <td style="color:#64748b;font-size:.8125rem">{{ $dept->parent->name ?? '—' }}</td>
                <td>
                    <span class="portal-badge portal-badge-blue">{{ $dept->members_count }}</span>
                </td>
                <td>
                    <span class="portal-badge {{ $dept->is_active ? 'portal-badge-green' : 'portal-badge-gray' }}">
                        {{ $dept->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>
                    <button type="button"
                            onclick="openHeadModal({{ $dept->id }}, '{{ addslashes($dept->name) }}')"
                            class="cp-btn-outline" style="font-size:.75rem;padding:.25rem .625rem">
                        Change Head
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:2rem;color:#475569">No departments found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

{{-- Change Head Modal --}}
<div id="head-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center">
    <div style="background:#1E293B;border:1px solid #334155;border-radius:12px;padding:2rem;width:100%;max-width:420px;margin:1rem">
        <h3 style="color:#f1f5f9;margin:0 0 1rem">Change Department Head</h3>
        <p id="modal-dept-name" style="color:#94a3b8;font-size:.875rem;margin:0 0 1.25rem"></p>
        <form id="head-form" method="POST">
            @csrf
            <label style="display:block;color:#94a3b8;font-size:.8125rem;margin-bottom:.5rem">Select New Head</label>
            <select name="head_id" required style="width:100%;background:#0F172A;border:1px solid #334155;color:#e2e8f0;border-radius:6px;padding:.5rem .75rem;margin-bottom:1rem">
                <option value="">Select employee…</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                @endforeach
            </select>
            <div style="display:flex;gap:.75rem;justify-content:flex-end">
                <button type="button" onclick="closeHeadModal()" class="cp-btn-outline">Cancel</button>
                <button type="submit" class="cp-btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openHeadModal(deptId, deptName) {
    document.getElementById('modal-dept-name').textContent = deptName;
    document.getElementById('head-form').action = '{{ url($locale."/hr/departments") }}/' + deptId + '/head';
    var m = document.getElementById('head-modal');
    m.style.display = 'flex';
}
function closeHeadModal() {
    document.getElementById('head-modal').style.display = 'none';
}
</script>
</x-layouts.hr>

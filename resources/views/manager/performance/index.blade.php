<x-layouts.manager title="Performance Reviews">
@php $locale = app()->getLocale(); @endphp

<div class="cp-page-header">
    <div>
        <h1 class="cp-page-title">Performance Reviews</h1>
        <p class="cp-page-subtitle">Manage and initiate performance reviews for your team</p>
    </div>
</div>

<div class="cp-section-grid">
    {{-- Review List --}}
    <div class="cp-section-card" style="grid-column:1/-1">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="trending-up" style="width:17px;height:17px;color:#1A6FE8"></i> Reviews</h2>
        </div>

        <form method="GET" class="portal-filter-bar">
            <select name="status">
                <option value="">All Statuses</option>
                <option value="draft"       {{ request('status') === 'draft'       ? 'selected' : '' }}>Draft</option>
                <option value="submitted"   {{ request('status') === 'submitted'   ? 'selected' : '' }}>Submitted</option>
                <option value="acknowledged"{{ request('status') === 'acknowledged'? 'selected' : '' }}>Acknowledged</option>
            </select>
            <button type="submit" class="cp-btn-primary">Filter</button>
        </form>

        <div style="overflow-x:auto">
        <table class="portal-table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Period</th>
                    <th>Review Date</th>
                    <th>Rating</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $review)
                @php
                    $badge = match($review->status) {
                        'submitted'    => 'portal-badge-blue',
                        'acknowledged' => 'portal-badge-green',
                        default        => 'portal-badge-amber',
                    };
                    $rating = $review->overall_rating ?? 0;
                @endphp
                <tr>
                    <td style="font-weight:500;color:#f1f5f9">{{ $review->employee->name ?? '—' }}</td>
                    <td>{{ $review->review_period }}</td>
                    <td>{{ $review->review_date?->format('M j, Y') }}</td>
                    <td>
                        @if($rating > 0)
                        <span style="color:#F59E0B;font-size:.875rem">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $rating ? '★' : '☆' }}
                            @endfor
                        </span>
                        @else
                        <span style="color:#334155">—</span>
                        @endif
                    </td>
                    <td><span class="portal-badge {{ $badge }}">{{ ucfirst($review->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:2rem;color:#475569">No reviews yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        <div style="margin-top:1rem">{{ $reviews->links() }}</div>
    </div>

    {{-- New Review Form --}}
    <div class="cp-section-card">
        <div class="cp-section-header">
            <h2 class="cp-section-title"><i data-lucide="plus-circle" style="width:17px;height:17px;color:#1A6FE8"></i> Initiate New Review</h2>
        </div>
        <form method="POST" action="{{ route('manager.performance.store', ['locale' => $locale]) }}" class="cp-form">
            @csrf
            <div style="display:flex;flex-direction:column;gap:1rem">
                <div>
                    <label style="display:block;color:#94a3b8;font-size:.8125rem;margin-bottom:.375rem">Employee</label>
                    <select name="user_id" required style="width:100%;background:#0F172A;border:1px solid #334155;color:#e2e8f0;border-radius:6px;padding:.5rem .75rem">
                        <option value="">Select employee…</option>
                        @foreach($teamMembers as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;color:#94a3b8;font-size:.8125rem;margin-bottom:.375rem">Review Period</label>
                    <input type="text" name="review_period" placeholder="e.g. Q2 2026" required
                           style="width:100%;background:#0F172A;border:1px solid #334155;color:#e2e8f0;border-radius:6px;padding:.5rem .75rem">
                </div>
                <div>
                    <label style="display:block;color:#94a3b8;font-size:.8125rem;margin-bottom:.375rem">Review Date</label>
                    <input type="date" name="review_date" required
                           style="width:100%;background:#0F172A;border:1px solid #334155;color:#e2e8f0;border-radius:6px;padding:.5rem .75rem">
                </div>
                <div>
                    <label style="display:block;color:#94a3b8;font-size:.8125rem;margin-bottom:.375rem">Strengths</label>
                    <textarea name="strengths" rows="2" style="width:100%;background:#0F172A;border:1px solid #334155;color:#e2e8f0;border-radius:6px;padding:.5rem .75rem;resize:vertical"></textarea>
                </div>
                <div>
                    <label style="display:block;color:#94a3b8;font-size:.8125rem;margin-bottom:.375rem">Areas for Improvement</label>
                    <textarea name="areas_for_improvement" rows="2" style="width:100%;background:#0F172A;border:1px solid #334155;color:#e2e8f0;border-radius:6px;padding:.5rem .75rem;resize:vertical"></textarea>
                </div>
                <div>
                    <label style="display:block;color:#94a3b8;font-size:.8125rem;margin-bottom:.375rem">Goals for Next Period</label>
                    <textarea name="goals_for_next_period" rows="2" style="width:100%;background:#0F172A;border:1px solid #334155;color:#e2e8f0;border-radius:6px;padding:.5rem .75rem;resize:vertical"></textarea>
                </div>
                <button type="submit" class="cp-btn-primary" style="align-self:flex-start">
                    <i data-lucide="plus" style="width:15px;height:15px"></i> Initiate Review
                </button>
            </div>
        </form>
    </div>
</div>
</x-layouts.manager>

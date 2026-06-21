<x-layouts.customer title="{{ $document->title }}">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">{{ $document->title }}</h1>
            <p class="cp-page-subtitle">Ref: {{ $document->reference_number }} &middot; {{ \App\Models\DocumentTemplate::typeLabel($document->type) }}</p>
        </div>
        <div style="display:flex;gap:0.75rem;align-items:center;flex-wrap:wrap;">
            @if($document->status === 'pending_signature' && $document->signature_token)
                <a href="{{ route('documents.sign', $document->signature_token) }}" class="cp-btn-primary">
                    <i data-lucide="pen-line" style="width:15px;height:15px"></i> Sign Document
                </a>
            @endif
            <a href="{{ route('documents.pdf', $document) }}" class="cp-btn-outline">
                <i data-lucide="download" style="width:15px;height:15px"></i> Download PDF
            </a>
            <a href="{{ route('customer.documents', ['locale' => app()->getLocale()]) }}" class="cp-btn-outline">
                &larr; Back
            </a>
        </div>
    </div>

    @if($document->isSigned())
        <div style="background:rgba(0,200,150,0.08);border:1px solid rgba(0,200,150,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;">
            <span style="color:#00C896;font-size:1.5rem;font-weight:700;">&#10003;</span>
            <div>
                <p style="color:#00C896;font-weight:600;font-size:0.9rem;margin:0;">Signed by {{ $document->signed_by_name }}</p>
                <p style="color:var(--text-muted);font-size:0.8rem;margin:0.1rem 0 0;">{{ $document->signed_at?->format('d M Y, H:i') }} UTC</p>
            </div>
        </div>
    @elseif($document->status === 'pending_signature')
        <div style="background:rgba(234,179,8,0.08);border:1px solid rgba(234,179,8,0.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1rem;">
            <span style="color:#eab308;font-size:1.5rem;">&#9203;</span>
            <div>
                <p style="color:#eab308;font-weight:600;font-size:0.9rem;margin:0;">Awaiting your signature</p>
                <p style="color:var(--text-muted);font-size:0.8rem;margin:0.1rem 0 0;">
                    Token expires {{ $document->signature_token_expires_at?->format('d M Y') }}
                </p>
            </div>
        </div>
    @endif

    <div class="cp-section-card" style="padding:0;">
        <div style="background:#1e293b;padding:0.75rem 1.25rem;border-bottom:1px solid #334155;border-radius:12px 12px 0 0;display:flex;justify-content:space-between;align-items:center;">
            <span style="color:var(--text-muted);font-size:0.8125rem;">Document Content</span>
            <span style="color:var(--text-muted);font-size:0.75rem;">Issued {{ $document->created_at->format('d M Y') }}</span>
        </div>
        <div style="padding:2rem;background:white;border-radius:0 0 12px 12px;overflow-x:auto;">
            {!! $document->body_rendered ?? '<em style="color:var(--text-muted);">No content available.</em>' !!}
        </div>
    </div>
</x-layouts.customer>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $document->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #1e293b; background: #fff; }
        .doc-page { padding: 2.5rem 2.5rem 2rem; min-height: 100vh; }
        .doc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
        .doc-company { font-size: 1.5rem; font-weight: 700; color: #0f172a; letter-spacing: -0.02em; }
        .doc-company-sub { font-size: 0.75rem; color: #64748b; margin-top: 0.2rem; }
        .doc-stamp { text-align: right; }
        .doc-stamp-label { font-size: 1rem; font-weight: 700; color: #00C896; text-transform: uppercase; letter-spacing: 0.1em; }
        .doc-stamp-ref { font-size: 0.8rem; color: #475569; margin-top: 0.25rem; }
        .doc-divider { border: none; border-top: 2px solid #00C896; margin: 1rem 0; }
        .doc-meta-row { display: flex; justify-content: space-between; margin: 1.5rem 0; }
        .doc-meta-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.06em; }
        .doc-meta-value { font-size: 0.875rem; color: #1e293b; margin-top: 0.2rem; }
        .doc-table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .doc-table th { background: #0f172a; color: #f1f5f9; padding: 0.6rem 0.75rem; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; }
        .doc-table td { padding: 0.6rem 0.75rem; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem; }
        .doc-table tfoot td { border-top: 2px solid #0f172a; font-weight: 700; padding: 0.75rem; }
        .text-right { text-align: right; }
        .doc-total-row td { background: #f8fafc; }
        .doc-footer-note { margin-top: 2rem; font-size: 0.75rem; color: #64748b; line-height: 1.6; }
        .doc-signature-block { margin-top: 3rem; }
        .doc-sig-line { border-top: 1px solid #94a3b8; width: 200px; margin-bottom: 0.5rem; }
        .doc-sig-label { font-size: 0.75rem; color: #64748b; }
        .doc-section-title { font-size: 1.1rem; font-weight: 700; text-align: center; margin: 1.5rem 0 1rem; color: #0f172a; text-transform: uppercase; letter-spacing: 0.05em; }
        .doc-clause-title { font-size: 0.9rem; font-weight: 700; margin: 1.25rem 0 0.4rem; color: #0f172a; }
        .doc-body-text { font-size: 0.875rem; line-height: 1.7; color: #334155; margin-bottom: 0.75rem; }
        .doc-date { font-size: 0.875rem; color: #64748b; margin-bottom: 1rem; }
        .doc-recipient { font-size: 0.9375rem; font-weight: 600; color: #0f172a; }
        .doc-recipient-addr { font-size: 0.8125rem; color: #64748b; margin-bottom: 0.5rem; }
        .doc-subject { font-size: 0.875rem; margin: 1.25rem 0 1.5rem; color: #0f172a; }
        .doc-signatures-row { display: flex; justify-content: space-between; margin-top: 2rem; }
        .doc-sig-col { width: 45%; }
        .doc-logo-block { flex: 1; }
        .doc-signed-banner {
            background: #f0fdf4; border: 2px solid #00C896; border-radius: 8px;
            padding: 1rem 1.5rem; margin-bottom: 1.5rem;
        }
        .doc-signed-badge { color: #00C896; font-size: 1.5rem; font-weight: 700; }
        .doc-signed-details { font-size: 0.8125rem; color: #334155; }
        .doc-signed-signature { font-family: 'Georgia', serif; font-size: 1.25rem; color: #0f172a; margin-top: 0.25rem; }
        @media print {
            .doc-page { padding: 0; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    @if($document->isSigned())
    <div class="doc-signed-banner">
        <div class="doc-signed-badge">&#10003; SIGNED</div>
        <div class="doc-signed-details">
            <div>Digitally signed by <strong>{{ $document->signed_by_name }}</strong></div>
            <div>{{ $document->signed_at?->format('d M Y, H:i') }} UTC</div>
            @if(isset($document->signed_data['typed_name']))
            <div class="doc-signed-signature">{{ $document->signed_data['typed_name'] }}</div>
            @endif
        </div>
    </div>
    @endif

    {!! $document->body_rendered !!}

    <div style="margin-top:3rem; padding-top:1rem; border-top:1px solid #e2e8f0; font-size:0.7rem; color:#94a3b8; text-align:center;">
        Document Reference: {{ $document->reference_number }} | Issued by OPES Health Systems SARL
        @if($document->valid_until) | Valid until: {{ $document->valid_until->format('d M Y') }} @endif
    </div>
</body>
</html>

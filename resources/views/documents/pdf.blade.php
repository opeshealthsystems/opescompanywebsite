<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $document->title }}</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 13px; color: #1e293b; background: #fff; }
        .doc-page { padding: 40px 40px 32px; }
        /* Header: table layout for DomPDF compatibility (no flexbox) */
        .doc-header { display: table; width: 100%; margin-bottom: 16px; }
        .doc-logo-block { display: table-cell; vertical-align: top; }
        .doc-stamp { display: table-cell; vertical-align: top; text-align: right; width: 220px; }
        .doc-company { font-size: 24px; font-weight: 700; color: #0f172a; }
        .doc-company-sub { font-size: 12px; color: #64748b; margin-top: 3px; }
        .doc-stamp-label { font-size: 16px; font-weight: 700; color: #00C896; text-transform: uppercase; }
        .doc-stamp-ref { font-size: 13px; color: #475569; margin-top: 4px; }
        .doc-divider { border: none; border-top: 2px solid #00C896; margin: 16px 0; }
        /* Meta row: table layout */
        .doc-meta-row { display: table; width: 100%; margin: 24px 0; }
        .doc-meta-row > div { display: table-cell; vertical-align: top; }
        .doc-meta-row > div:last-child { text-align: right; }
        .doc-meta-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; }
        .doc-meta-value { font-size: 14px; color: #1e293b; margin-top: 3px; }
        .doc-table { width: 100%; border-collapse: collapse; margin: 24px 0; }
        .doc-table th { background: #0f172a; color: #f1f5f9; padding: 10px 12px; font-size: 12px; text-transform: uppercase; text-align: left; }
        .doc-table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .doc-table tfoot td { border-top: 2px solid #0f172a; font-weight: 700; padding: 12px; }
        .text-right { text-align: right; }
        .doc-total-row td { background: #f8fafc; }
        .doc-footer-note { margin-top: 32px; font-size: 12px; color: #64748b; line-height: 1.6; }
        .doc-signature-block { margin-top: 48px; }
        .doc-sig-line { border-top: 1px solid #94a3b8; width: 200px; margin-bottom: 8px; }
        .doc-sig-label { font-size: 12px; color: #64748b; }
        .doc-section-title { font-size: 18px; font-weight: 700; text-align: center; margin: 24px 0 16px; color: #0f172a; text-transform: uppercase; }
        .doc-clause-title { font-size: 14px; font-weight: 700; margin: 20px 0 6px; color: #0f172a; }
        .doc-body-text { font-size: 14px; line-height: 1.7; color: #334155; margin-bottom: 12px; }
        .doc-date { font-size: 14px; color: #64748b; margin-bottom: 16px; }
        .doc-recipient { font-size: 15px; font-weight: 600; color: #0f172a; }
        .doc-recipient-addr { font-size: 13px; color: #64748b; margin-bottom: 8px; }
        .doc-subject { font-size: 14px; margin: 20px 0 24px; color: #0f172a; }
        /* Signatures: table layout for DomPDF compatibility */
        .doc-signatures-row { display: table; width: 100%; margin-top: 32px; }
        .doc-sig-col { display: table-cell; vertical-align: top; width: 45%; }
        .doc-signed-banner {
            background: #f0fdf4; border: 2px solid #00C896;
            padding: 16px 24px; margin-bottom: 24px;
        }
        .doc-signed-badge { color: #00C896; font-size: 24px; font-weight: 700; }
        .doc-signed-details { font-size: 13px; color: #334155; margin-top: 4px; }
        .doc-signed-signature { font-family: 'Georgia', serif; font-size: 20px; color: #0f172a; margin-top: 4px; }
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

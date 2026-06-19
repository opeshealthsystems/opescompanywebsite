<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; margin: 0; padding: 40px; }
        .frame { border: 6px solid #047857; padding: 40px 60px; text-align: center; }
        .brand { color: #047857; font-size: 18px; letter-spacing: 3px; text-transform: uppercase; }
        h1 { font-size: 34px; margin: 18px 0 6px; }
        .subtitle { color: #64748b; font-size: 14px; }
        .name { font-size: 28px; font-weight: bold; margin: 28px 0 6px; }
        .detail { font-size: 14px; color: #334155; margin: 4px 0; }
        .tier { display: inline-block; margin-top: 16px; padding: 6px 18px; border-radius: 999px;
                background: #ecfdf5; color: #047857; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .footer { margin-top: 40px; display: flex; justify-content: space-between; font-size: 12px; color: #64748b; }
        .sigline { border-top: 1px solid #94a3b8; width: 200px; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="brand">OPES Health Systems</div>
        <h1>Certificate of Clinical Validation</h1>
        <div class="subtitle">This certifies that</div>
        <div class="name">{{ $certificate->cohortMember?->user?->name ?? 'Practitioner' }}</div>
        <div class="detail">successfully completed the clinical validation programme as part of</div>
        <div class="detail"><strong>{{ $certificate->cohortMember?->cohort?->name ?? 'Validation Cohort' }}</strong>
            @if($certificate->cohortMember?->cohort?->specialty) — {{ $certificate->cohortMember->cohort->specialty }} @endif
        </div>
        <div class="tier">{{ ucfirst($certificate->tier) }} &middot; Score {{ $certificate->score }}/100</div>
        <div class="footer">
            <div class="sigline">Clinical Validation Lead</div>
            <div style="text-align:right">
                <div>Certificate No. {{ $certificate->certificate_number }}</div>
                <div>Issued {{ $certificate->issued_at?->format('d M Y') }}</div>
            </div>
        </div>
    </div>
</body>
</html>

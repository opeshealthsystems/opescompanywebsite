<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 0; }
    body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; color: #1e293b; }
    .cert { width: 100%; height: 560px; padding: 40px 60px; box-sizing: border-box; text-align: center; border: 12px solid #00C896; }
    .inner { border: 2px solid #00C896; padding: 30px; height: 100%; box-sizing: border-box; }
    .brand { color: #00C896; font-size: 24px; font-weight: bold; letter-spacing: 3px; }
    .title { font-size: 40px; color: #1e293b; margin: 18px 0 6px; }
    .subtitle { color: #64748b; font-size: 14px; }
    .name { font-size: 34px; color: #0f172a; margin: 22px 0; border-bottom: 2px solid #cbd5e1; display: inline-block; padding: 0 40px 8px; }
    .course { font-size: 22px; color: #00C896; font-weight: bold; margin: 12px 0 24px; }
    .meta { font-size: 12px; color: #64748b; margin-top: 34px; }
    .sig { margin-top: 26px; font-size: 14px; color: #1e293b; border-top: 1px solid #94a3b8; display: inline-block; padding-top: 6px; }
</style>
</head>
<body>
    <div class="cert">
        <div class="inner">
            <div class="brand">OPES HEALTH SYSTEMS</div>
            <div class="title">Certificate of Completion</div>
            <div class="subtitle">This is to certify that</div>
            <div class="name">{{ $certificate->user->name }}</div>
            <div class="subtitle">has successfully completed the course</div>
            <div class="course">{{ $certificate->course->title }}</div>
            <div class="sig">OPES Health Systems</div>
            <div class="meta">
                Certificate No: {{ $certificate->certificate_number }} &nbsp;|&nbsp;
                Issued: {{ $certificate->issued_at->format('d F Y') }}
            </div>
        </div>
    </div>
</body>
</html>

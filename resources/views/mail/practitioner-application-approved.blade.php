<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; color: #1e293b; background: #f8fafc; margin: 0; padding: 0; }
.wrap { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
.header { background: #0f172a; padding: 28px 32px; }
.header-title { color: #00C896; font-size: 20px; font-weight: 700; margin: 0 0 4px; }
.header-sub { color: #475569; font-size: 13px; margin: 0; }
.body { padding: 32px; }
.hi { font-size: 16px; font-weight: 600; color: #0f172a; margin: 0 0 12px; }
.intro { font-size: 14px; color: #334155; line-height: 1.7; margin: 0 0 24px; }
.badge { display: inline-block; background: #dcfce7; color: #166534; font-size: 13px; font-weight: 700; padding: 6px 14px; border-radius: 99px; margin: 0 0 24px; }
.info-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px 20px; margin: 0 0 24px; }
.info-label { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 4px; }
.info-value { font-size: 15px; font-weight: 700; color: #0f172a; margin: 0; }
.cta { display: inline-block; background: #00C896; color: #fff; text-decoration: none; padding: 13px 28px; border-radius: 6px; font-weight: 700; font-size: 14px; }
.divider { height: 1px; background: #e2e8f0; margin: 24px 0; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">Application Approved</p>
        <p class="header-sub">OPES Practitioner Programme</p>
    </div>
    <div class="body">
        <p class="hi">Congratulations, {{ $application->practitioner->name }}!</p>
        <span class="badge">✓ Approved</span>
        <p class="intro">
            Your application for the programme below has been approved. You can now log in to your practitioner portal to submit your findings.
        </p>

        <div class="info-box">
            <p class="info-label">Programme</p>
            <p class="info-value">{{ $application->program->title }}</p>
        </div>

        <a href="{{ config('app.url') }}/en/practitioner/applications/{{ $application->id }}" class="cta">View My Application</a>

        <div class="divider"></div>
        <p style="font-size:13px;color:#64748b;margin:0">
            Questions? Contact us at <a href="mailto:support@opeshealthsystems.com" style="color:#00C896">support@opeshealthsystems.com</a>
        </p>
    </div>
    <div class="footer">OPES Health Systems · Bonamousadi, Douala, Cameroon · OHADA Law</div>
</div>
</body>
</html>

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
.feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 0 0 28px; }
.feature { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px 16px; }
.feature-title { font-size: 13px; font-weight: 700; color: #0f172a; margin: 0 0 4px; }
.feature-desc { font-size: 12px; color: #64748b; margin: 0; line-height: 1.5; }
.cta { display: inline-block; background: #00C896; color: #fff; text-decoration: none; padding: 13px 28px; border-radius: 6px; font-weight: 700; font-size: 14px; }
.divider { height: 1px; background: #e2e8f0; margin: 24px 0; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">Welcome to the OPES Practitioner Programme</p>
        <p class="header-sub">Empowering healthcare professionals across Africa</p>
    </div>
    <div class="body">
        <p class="hi">Welcome to the OPES Practitioner Programme, {{ $user->name }}!</p>
        <p class="intro">
            We are delighted to have you on board. The OPES Practitioner Programme connects verified healthcare professionals with cutting-edge digital tools designed to improve patient care, streamline clinical workflows, and support evidence-based practice across Africa.
        </p>

        <div class="feature-grid">
            <div class="feature">
                <p class="feature-title">🩺 Clinical Tools</p>
                <p class="feature-desc">Access digital tools tailored to your specialty and clinical environment.</p>
            </div>
            <div class="feature">
                <p class="feature-title">📊 Practice Analytics</p>
                <p class="feature-desc">Track patient outcomes and monitor key performance indicators.</p>
            </div>
            <div class="feature">
                <p class="feature-title">🤝 Collaboration</p>
                <p class="feature-desc">Connect with peers and specialists across the OPES network.</p>
            </div>
            <div class="feature">
                <p class="feature-title">📚 Resources</p>
                <p class="feature-desc">Continuing medical education, guidelines, and clinical references.</p>
            </div>
        </div>

        <a href="{{ config('app.url') }}/en/practitioner/dashboard" class="cta">Go to Practitioner Dashboard</a>

        <div class="divider"></div>
        <p style="font-size:13px;color:#64748b;margin:0">
            Questions? Reply to this email or contact us at <a href="mailto:support@opeshealthsystems.com" style="color:#00C896">support@opeshealthsystems.com</a>
        </p>
    </div>
    <div class="footer">OPES Health Systems · Bonamousadi, Douala, Cameroon · OHADA Law</div>
</div>
</body>
</html>

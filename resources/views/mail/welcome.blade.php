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
        <p class="header-title">Welcome to OPES Health Systems</p>
        <p class="header-sub">Africa's most complete digital health ecosystem</p>
    </div>
    <div class="body">
        <p class="hi">Hello {{ $user->name }},</p>
        <p class="intro">
            Your account is ready. You now have access to the OPES customer portal where you can manage your licenses, download documents, track invoices, and open support tickets.
        </p>

        <div class="feature-grid">
            <div class="feature">
                <p class="feature-title">📋 Licenses</p>
                <p class="feature-desc">View your active software licenses and renewal dates.</p>
            </div>
            <div class="feature">
                <p class="feature-title">🎫 Support</p>
                <p class="feature-desc">Submit and track support tickets with our team.</p>
            </div>
            <div class="feature">
                <p class="feature-title">🧾 Invoices</p>
                <p class="feature-desc">Download PDF invoices and track payment status.</p>
            </div>
            <div class="feature">
                <p class="feature-title">📄 Documents</p>
                <p class="feature-desc">Access contracts, proposals, and signed documents.</p>
            </div>
        </div>

        <a href="{{ config('app.url') }}/en/customer/dashboard" class="cta">Go to Customer Portal</a>

        <div class="divider"></div>
        <p style="font-size:13px;color:#64748b;margin:0">
            Questions? Reply to this email or visit <a href="mailto:support@opeshealthsystems.com" style="color:#00C896">support@opeshealthsystems.com</a>
        </p>
    </div>
    <div class="footer">OPES Health Systems · Bonamousadi, Douala, Cameroon · OHADA Law</div>
</div>
</body>
</html>

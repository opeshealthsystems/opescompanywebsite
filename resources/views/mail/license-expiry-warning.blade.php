<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; color: #1e293b; background: #f8fafc; margin: 0; padding: 0; }
.wrap { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
.header { background: #0f172a; padding: 24px 32px; }
.header-title { color: #f59e0b; font-size: 18px; font-weight: 700; margin: 0; }
.body { padding: 32px; }
.warning-box { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 16px 20px; margin: 16px 0 24px; }
.warning-box p { margin: 0; font-size: 14px; color: #92400e; line-height: 1.6; }
.days-badge { display: inline-block; background: #f59e0b; color: #fff; font-weight: 900; font-size: 2rem; padding: 8px 20px; border-radius: 8px; margin-bottom: 4px; line-height: 1; }
.days-label { font-size: 12px; color: #92400e; font-weight: 600; }
.detail-row { margin-bottom: 10px; display: flex; gap: 12px; }
.label { color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; min-width: 110px; padding-top: 1px; }
.value { color: #1e293b; font-size: 14px; font-weight: 500; }
.cta { display: inline-block; margin-top: 8px; background: #00C896; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; }
.cta-outline { display: inline-block; margin-top: 8px; margin-left: 8px; background: transparent; color: #64748b; text-decoration: none; padding: 11px 20px; border-radius: 6px; font-weight: 600; font-size: 13px; border: 1px solid #e2e8f0; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">⚠️ License Expiring Soon</p>
    </div>
    <div class="body">
        <p style="color:#334155;font-size:15px;margin-top:0">Dear {{ $license->customer?->name }},</p>

        <div style="text-align:center;padding:16px 0">
            <div class="days-badge">{{ $daysLeft }}</div><br>
            <span class="days-label">days remaining on your license</span>
        </div>

        <div class="warning-box">
            <p>Your <strong>{{ $license->product_name }}</strong> license expires on <strong>{{ $license->end_date->format('d F Y') }}</strong>. To avoid disruption to your facility's operations, please renew before this date.</p>
        </div>

        <div class="detail-row">
            <span class="label">Product</span>
            <span class="value">{{ $license->product_name }}</span>
        </div>
        <div class="detail-row">
            <span class="label">License Key</span>
            <span class="value" style="font-family:monospace;color:#475569">{{ $license->license_key }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Plan</span>
            <span class="value">{{ \App\Models\License::planLabel($license->plan) }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Expiry Date</span>
            <span class="value" style="color:#dc2626;font-weight:700">{{ $license->end_date->format('d F Y') }}</span>
        </div>

        <div style="margin-top:24px">
            <a href="{{ config('app.url') }}/en/customer/tickets/create?subject=License+Renewal+Request&type=billing" class="cta">Request Renewal</a>
            <a href="{{ config('app.url') }}/en/customer/licenses/{{ $license->id }}" class="cta-outline">View License</a>
        </div>

        <p style="color:#94a3b8;font-size:12px;margin-top:24px">Need help? Contact <a href="mailto:support@opeshealthsystems.com" style="color:#00C896">support@opeshealthsystems.com</a> or call your account manager.</p>
    </div>
    <div class="footer">OPES Health Systems · support@opeshealthsystems.com · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

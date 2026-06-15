<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; color: #1e293b; background: #f8fafc; margin: 0; padding: 0; }
.wrap { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
.header { background: #0f172a; padding: 24px 32px; }
.header-title { color: #00C896; font-size: 18px; font-weight: 700; margin: 0; }
.body { padding: 32px; }
.key-box { background: #0f172a; border-radius: 8px; padding: 20px 24px; margin: 20px 0; text-align: center; }
.key-label { color: #475569; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 8px; }
.key-value { color: #00C896; font-size: 20px; font-weight: 900; font-family: monospace; letter-spacing: 3px; margin: 0; }
.detail-row { margin-bottom: 10px; display: flex; gap: 12px; }
.label { color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; min-width: 110px; padding-top: 1px; }
.value { color: #1e293b; font-size: 14px; font-weight: 500; }
.cta { display: inline-block; margin-top: 24px; background: #00C896; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">Your OPES License is Ready</p>
    </div>
    <div class="body">
        <p style="color:#334155;font-size:15px;margin-top:0">Dear {{ $license->customer?->name }},</p>
        <p style="color:#334155;font-size:14px">Your license for <strong>{{ $license->product_name }}</strong> has been issued and is now active. Please keep your license key in a safe place.</p>

        <div class="key-box">
            <p class="key-label">License Key</p>
            <p class="key-value">{{ $license->license_key }}</p>
        </div>

        <div class="detail-row">
            <span class="label">Product</span>
            <span class="value">{{ $license->product_name }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Plan</span>
            <span class="value" style="text-transform:capitalize">{{ \App\Models\License::planLabel($license->plan) }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Seats</span>
            <span class="value">{{ $license->seats }} {{ Str::plural('user', $license->seats) }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Valid From</span>
            <span class="value">{{ $license->start_date->format('d F Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Expires</span>
            <span class="value">{{ $license->end_date->format('d F Y') }}</span>
        </div>

        <a href="{{ config('app.url') }}/en/customer/licenses/{{ $license->id }}" class="cta">View License in Portal</a>

        <p style="color:#94a3b8;font-size:12px;margin-top:20px">Need help getting started? Contact <a href="mailto:support@opeshealthsystems.com" style="color:#00C896">support@opeshealthsystems.com</a></p>
    </div>
    <div class="footer">OPES Health Systems · support@opeshealthsystems.com · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

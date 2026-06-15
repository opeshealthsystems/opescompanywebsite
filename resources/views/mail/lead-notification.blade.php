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
.row { display: flex; margin-bottom: 12px; }
.label { color: #64748b; font-size: 13px; font-weight: 600; width: 140px; flex-shrink: 0; }
.value { color: #1e293b; font-size: 14px; }
.message-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin-top: 16px; color: #334155; font-size: 14px; line-height: 1.6; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">New Demo Request — OPES Health Systems</p>
    </div>
    <div class="body">
        <p style="color:#64748b;font-size:14px;margin-top:0">A new enquiry has been submitted via the website.</p>

        <div class="row">
            <span class="label">Name</span>
            <span class="value">{{ $lead->name }}</span>
        </div>
        <div class="row">
            <span class="label">Email</span>
            <span class="value"><a href="mailto:{{ $lead->email }}" style="color:#1A6FE8">{{ $lead->email }}</a></span>
        </div>
        @if($lead->phone)
        <div class="row">
            <span class="label">Phone</span>
            <span class="value">{{ $lead->phone }}</span>
        </div>
        @endif
        @if($lead->facility_type)
        <div class="row">
            <span class="label">Facility Type</span>
            <span class="value">{{ $lead->facility_type }}</span>
        </div>
        @endif
        @if($lead->products)
        <div class="row">
            <span class="label">Products Interest</span>
            <span class="value">{{ $lead->products }}</span>
        </div>
        @endif
        <div class="row">
            <span class="label">Source</span>
            <span class="value">{{ $lead->source }}</span>
        </div>
        <div class="row">
            <span class="label">Language</span>
            <span class="value">{{ strtoupper($lead->locale ?? 'en') }}</span>
        </div>

        @if($lead->message)
        <p style="color:#64748b;font-size:13px;font-weight:600;margin-top:20px;margin-bottom:6px">MESSAGE</p>
        <div class="message-box">{{ $lead->message }}</div>
        @endif
    </div>
    <div class="footer">OPES Health Systems · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

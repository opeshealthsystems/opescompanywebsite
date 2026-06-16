<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;max-width:600px;margin:auto;padding:20px;color:#1e293b;">
<h2 style="color:#00C896;">OPES Health Systems</h2>
<p>Dear {{ $contract->lead->name ?? 'Valued Partner' }},</p>
<p>
    This is a reminder that your contract <strong>{{ $contract->title }}</strong>
    (Reference: <strong>{{ $contract->reference }}</strong>) is set to expire in
    <strong>{{ $daysLeft }} day{{ $daysLeft !== 1 ? 's' : '' }}</strong>
    on <strong>{{ $contract->end_date->format('d M Y') }}</strong>.
</p>
<p>Please contact us to discuss renewal options before the expiry date.</p>
<hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">
<p style="color:#64748b;font-size:12px;">
    OPES Health Systems &bull; This is an automated notification.
</p>
</body>
</html>

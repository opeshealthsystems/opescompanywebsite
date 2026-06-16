<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#1e293b;max-width:600px;margin:0 auto;padding:24px">
    <h2 style="color:#00C896">OPES Health Systems</h2>
    <p>Dear {{ $bugReport->practitioner->name }},</p>
    <p>Thank you for reporting the bug: <strong>{{ $bugReport->title }}</strong></p>
    <p>Our team has reviewed your report and provided the following response:</p>
    <blockquote style="border-left:4px solid #00C896;margin:16px 0;padding:12px 16px;background:#f0fdf4;color:#166534">
        {{ $bugReport->admin_response }}
    </blockquote>
    <p><strong>Status:</strong> {{ \App\Models\PractitionerBugReport::statusOptions()[$bugReport->status] ?? ucwords(str_replace('_', ' ', $bugReport->status)) }}</p>
    <p>Thank you for helping us improve OPES.</p>
    <p>Best regards,<br>OPES Health Systems Team</p>
</body>
</html>

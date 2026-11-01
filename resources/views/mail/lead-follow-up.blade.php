<!doctype html>
<html lang="en">
<body style="margin:0;background:#f4f7fa;color:#172b45;font-family:Arial,sans-serif;line-height:1.6">
<div style="max-width:600px;margin:24px auto;padding:28px;background:#fff;border:1px solid #d8e1ea;border-radius:8px">
    <p style="margin-top:0;color:#123d69;font-weight:700">OPES Health Systems</p>
    @if($recipientName)<p>Hello {{ $recipientName }},</p>@endif
    <div style="white-space:pre-wrap">{{ $messageBody }}</div>
    <p style="margin-bottom:0">Regards,<br>OPES Health Systems</p>
</div>
</body>
</html>

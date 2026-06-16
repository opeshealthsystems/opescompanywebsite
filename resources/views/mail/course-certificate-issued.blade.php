<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#1e293b;max-width:600px;margin:0 auto;padding:24px">
    <h2 style="color:#00C896">OPES Health Systems</h2>
    <p>Dear {{ $certificate->user->name }},</p>
    <p>Congratulations on completing the course:</p>
    <p style="font-size:18px;font-weight:bold;color:#00C896">{{ $certificate->course->title }}</p>
    <p>Your certificate of completion has been issued.</p>
    <p><strong>Certificate Number:</strong> {{ $certificate->certificate_number }}</p>
    <p>Log in to your portal and visit the Certificates page to download your certificate as a PDF.</p>
    <p>Well done, and thank you for learning with OPES.</p>
    <p>Best regards,<br>OPES Health Systems Team</p>
</body>
</html>

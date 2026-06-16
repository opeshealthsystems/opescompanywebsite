<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#1e293b;max-width:600px;margin:0 auto;padding:24px">
    <h2 style="color:#00C896">OPES Health Systems</h2>
    <p>Dear {{ $enrollment->user->name }},</p>
    <p>You have successfully enrolled in the course:</p>
    <p style="font-size:18px;font-weight:bold;color:#00C896">{{ $enrollment->course->title }}</p>
    <p>You can now start learning at your own pace. Log in to your portal and head to the Courses section to begin.</p>
    <p>We wish you a great learning experience.</p>
    <p>Best regards,<br>OPES Health Systems Team</p>
</body>
</html>

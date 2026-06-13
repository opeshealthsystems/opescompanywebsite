<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Signed — OPES Health Systems</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div style="text-align:center;margin-bottom:1rem;">
            <span class="auth-brand-opes">OPES</span>
            <span class="auth-brand-name"> Health Systems</span>
        </div>
        <div class="auth-card" style="text-align:center;padding:3rem;">
            <div style="font-size:3.5rem;color:#00C896;margin-bottom:1rem;">&#10003;</div>
            <h1 style="color:#f1f5f9;font-size:1.5rem;font-weight:700;">Document Signed Successfully</h1>
            <p style="color:#94a3b8;margin-top:0.75rem;">Reference: <strong style="color:#e2e8f0;">{{ $reference }}</strong></p>
            <p style="color:#64748b;font-size:0.875rem;margin-top:1rem;line-height:1.6;">
                Your digital signature has been recorded. If you have questions, contact <a href="mailto:support@opeshealthsystems.com" class="auth-link">support@opeshealthsystems.com</a>.
            </p>
        </div>
        <p class="auth-footer-note">&copy; {{ date('Y') }} OPES Health Systems SARL</p>
    </div>
</body>
</html>

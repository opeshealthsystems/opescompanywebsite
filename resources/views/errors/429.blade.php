<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 — Too Many Requests · OPES Health Systems</title>
    @vite(['resources/css/app.css'])
</head>
<body>
<div style="min-height:100vh;display:flex;align-items:center;justify-content:center;flex-direction:column;text-align:center;padding:48px">
    <div style="font-size:80px;font-weight:900;background:linear-gradient(90deg,#00C896,#1A6FE8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;line-height:1;margin-bottom:16px">429</div>
    <h1 style="font-family:'Plus Jakarta Sans',sans-serif;font-size:24px;color:#e2e8f0;margin-bottom:12px">Too Many Requests</h1>
    <p style="color:#64748b;font-size:15px;max-width:420px;margin-bottom:32px">You've sent too many requests in a short period. Please slow down and try again in a moment.</p>
    <div style="display:flex;gap:14px;flex-wrap:wrap;justify-content:center">
        <a href="javascript:location.reload()" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#00C896,#009d77);color:#fff;font-weight:700;font-size:14px;padding:12px 24px;border-radius:10px;text-decoration:none">
            Try Again
        </a>
        <a href="/{{ app()->getLocale() }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,0.06);color:#94a3b8;font-weight:600;font-size:14px;padding:12px 24px;border-radius:10px;text-decoration:none;border:1px solid rgba(255,255,255,0.1)">
            Back to Home
        </a>
    </div>
    <div style="margin-top:48px;font-size:12px;color:#334155">OPES Health Systems SARL · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

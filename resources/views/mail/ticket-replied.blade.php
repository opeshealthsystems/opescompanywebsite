<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
body { font-family: Arial, sans-serif; color: #1e293b; background: #f8fafc; margin: 0; padding: 0; }
.wrap { max-width: 600px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; }
.header { background: #0f172a; padding: 24px 32px; }
.header-title { color: #1A6FE8; font-size: 18px; font-weight: 700; margin: 0; }
.body { padding: 32px; }
.ticket-id { display: inline-block; background: #f1f5f9; border-radius: 6px; padding: 4px 10px; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 16px; }
.reply-box { background: #f8fafc; border-left: 3px solid #1A6FE8; border-radius: 0 6px 6px 0; padding: 16px; margin-top: 16px; color: #334155; font-size: 14px; line-height: 1.6; }
.cta { display: inline-block; margin-top: 24px; background: #1A6FE8; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">New Reply on Your Support Ticket</p>
    </div>
    <div class="body">
        <p style="color:#334155;font-size:15px;margin-top:0">The OPES support team has replied to your ticket.</p>

        <span class="ticket-id">Ticket #{{ $ticket->id }}</span>
        <p style="color:#475569;font-size:14px;font-weight:600;margin-top:4px">{{ $ticket->subject }}</p>

        <p style="color:#64748b;font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px">Support Response</p>
        <div class="reply-box">{{ $reply->body }}</div>

        <p style="margin-top:24px;margin-bottom:4px">
            <a href="{{ config('app.url') }}" class="cta">View Full Conversation</a>
        </p>
        <p style="color:#94a3b8;font-size:12px;margin-top:16px">To reply, visit the customer portal. Do not reply to this email.</p>
    </div>
    <div class="footer">OPES Health Systems · support@opeshealthsystems.com · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

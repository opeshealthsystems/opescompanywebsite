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
.ticket-id { display: inline-block; background: #f1f5f9; border-radius: 6px; padding: 4px 10px; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 16px; }
.detail-row { margin-bottom: 10px; }
.label { color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px; }
.value { color: #1e293b; font-size: 14px; }
.message-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin-top: 20px; color: #334155; font-size: 14px; line-height: 1.6; }
.cta { display: inline-block; margin-top: 24px; background: #00C896; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">Support Ticket Received</p>
    </div>
    <div class="body">
        <p style="color:#334155;font-size:15px;margin-top:0">Thank you for contacting OPES Health Systems support. We have received your ticket and will respond within one business day.</p>

        <span class="ticket-id">Ticket #{{ $ticket->id }}</span>

        <div class="detail-row">
            <span class="label">Subject</span>
            <span class="value" style="font-weight:600">{{ $ticket->subject }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Type</span>
            <span class="value" style="text-transform:capitalize">{{ str_replace('_', ' ', $ticket->type) }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Priority</span>
            <span class="value" style="text-transform:capitalize">{{ $ticket->priority }}</span>
        </div>
        <div class="detail-row">
            <span class="label">Status</span>
            <span class="value" style="text-transform:capitalize">{{ $ticket->status }}</span>
        </div>

        <div class="message-box">{{ $ticket->description }}</div>

        <p style="margin-top:24px;margin-bottom:4px">
            <a href="{{ config('app.url') }}" class="cta">View in Customer Portal</a>
        </p>
        <p style="color:#94a3b8;font-size:12px;margin-top:16px">You will be notified when our team responds. Do not reply to this email — use the customer portal to add further information.</p>
    </div>
    <div class="footer">OPES Health Systems · support@opeshealthsystems.com · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

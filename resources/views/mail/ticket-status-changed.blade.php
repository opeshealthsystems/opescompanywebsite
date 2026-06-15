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
.status-pill { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; }
.status-resolved { background: #dcfce7; color: #166534; }
.status-closed { background: #f1f5f9; color: #475569; }
.status-pending { background: #fef9c3; color: #713f12; }
.status-default { background: #e0f2fe; color: #075985; }
.detail-row { margin-bottom: 10px; }
.label { color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px; }
.value { color: #1e293b; font-size: 14px; }
.resolution-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #00C896; border-radius: 6px; padding: 16px; margin-top: 16px; font-size: 14px; color: #166534; line-height: 1.6; }
.action-box { background: #fffbeb; border: 1px solid #fde68a; border-left: 4px solid #f59e0b; border-radius: 6px; padding: 16px; margin-top: 16px; font-size: 14px; color: #92400e; line-height: 1.6; }
.cta { display: inline-block; margin-top: 24px; background: #00C896; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">Support Ticket Update</p>
    </div>
    <div class="body">
        <p style="color:#334155;font-size:15px;margin-top:0">Dear {{ $ticket->user?->name }},</p>

        <span class="ticket-id">Ticket #{{ $ticket->id }}</span><br>

        @php
        $statusClass = match($newStatus) {
            'resolved'         => 'status-resolved',
            'closed'           => 'status-closed',
            'pending_customer' => 'status-pending',
            default            => 'status-default',
        };
        $statusLabel = match($newStatus) {
            'resolved'         => 'Resolved',
            'closed'           => 'Closed',
            'pending_customer' => 'Action Required',
            'in_progress'      => 'In Progress',
            default            => ucfirst(str_replace('_', ' ', $newStatus)),
        };
        @endphp

        <span class="status-pill {{ $statusClass }}">{{ $statusLabel }}</span>

        <div class="detail-row">
            <span class="label">Subject</span>
            <span class="value" style="font-weight:600">{{ $ticket->subject }}</span>
        </div>
        <div class="detail-row">
            <span class="label">New Status</span>
            <span class="value">{{ $statusLabel }}</span>
        </div>

        @if($newStatus === 'resolved' && $ticket->resolution)
        <div class="resolution-box">
            <strong style="display:block;margin-bottom:8px">Resolution:</strong>
            {{ $ticket->resolution }}
        </div>
        @endif

        @if($newStatus === 'pending_customer')
        <div class="action-box">
            Our support team has replied and is waiting for additional information from you. Please log in to the customer portal to respond.
        </div>
        @endif

        @if($newStatus === 'resolved')
        <p style="font-size:14px;color:#334155;margin-top:16px">If your issue is not fully resolved, you can reopen this ticket from the customer portal within 7 days.</p>
        @endif

        <a href="{{ config('app.url') }}/en/customer/tickets/{{ $ticket->id }}" class="cta">View Ticket</a>

        <p style="color:#94a3b8;font-size:12px;margin-top:20px">Do not reply to this email — use the customer portal to add further information.</p>
    </div>
    <div class="footer">OPES Health Systems · support@opeshealthsystems.com · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

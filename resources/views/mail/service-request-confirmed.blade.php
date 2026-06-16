<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#1e293b;max-width:600px;margin:0 auto;padding:24px">
    <h2 style="color:#00C896">OPES Health Systems</h2>
    <p>Dear {{ $serviceRequest->customer->name }},</p>
    <p>Your service request <strong>{{ $serviceRequest->reference_number }}</strong> has been confirmed.</p>
    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0;color:#64748b;width:40%">Service Type</td>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0">{{ \App\Models\ServiceRequest::typeOptions()[$serviceRequest->type] ?? $serviceRequest->type }}</td>
        </tr>
        <tr>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0;color:#64748b">Confirmed Date</td>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0">{{ optional($serviceRequest->confirmed_date)->format('d M Y') ?? $serviceRequest->preferred_date->format('d M Y') }}</td>
        </tr>
        @if($serviceRequest->confirmed_time)
        <tr>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0;color:#64748b">Time</td>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0">{{ $serviceRequest->confirmed_time }}</td>
        </tr>
        @endif
        @if($serviceRequest->location)
        <tr>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0;color:#64748b">Location</td>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0">{{ $serviceRequest->location }}</td>
        </tr>
        @endif
    </table>
    @if($serviceRequest->admin_notes)
        <p><strong>Notes from OPES:</strong> {{ $serviceRequest->admin_notes }}</p>
    @endif
    <p>Our technician will arrive at the scheduled time. Please ensure access to the relevant areas.</p>
    <p>Best regards,<br>OPES Health Systems Team</p>
</body>
</html>

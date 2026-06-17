<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;color:#1e293b;max-width:600px;margin:0 auto;padding:24px">
    <h2 style="color:#00C896">OPES Health Systems</h2>
    <p>Dear {{ $application->practitioner->name }},</p>
    <p>Your compensation for the programme
        <strong>{{ optional($application->program)->title }}</strong> has been sent.</p>
    <table style="width:100%;border-collapse:collapse;margin:16px 0">
        <tr>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0;color:#64748b;width:40%">Amount</td>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0">{{ number_format((float) $application->payout_amount, 2) }} {{ $application->payout_currency }}</td>
        </tr>
        <tr>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0;color:#64748b">Method</td>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0">{{ strtoupper((string) $application->payout_provider) }} Mobile Money</td>
        </tr>
        <tr>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0;color:#64748b">Reference</td>
            <td style="padding:8px;border-bottom:1px solid #e2e8f0">{{ $application->payout_reference }}</td>
        </tr>
    </table>
    <p>If you do not receive the funds shortly, please contact the OPES team with the reference above.</p>
    <p>Thank you for contributing to OPES Health Systems.</p>
    <p>Best regards,<br>OPES Health Systems Team</p>
</body>
</html>

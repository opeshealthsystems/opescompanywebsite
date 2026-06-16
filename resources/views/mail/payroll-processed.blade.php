<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:sans-serif;max-width:600px;margin:auto;padding:20px;color:#1e293b;">
<h2 style="color:#00C896;">OPES Health Systems</h2>
<p>Dear {{ $entry->employee?->name ?? 'Team Member' }},</p>
<p>
    Your payslip for <strong>{{ $run->period_start->format('F Y') }}</strong>
    ({{ $run->period_start->format('d M') }} – {{ $run->period_end->format('d M Y') }}) is now ready.
</p>
<table style="width:100%;border-collapse:collapse;margin:20px 0;">
  <tr style="background:#f8fafc;">
    <td style="padding:10px;border:1px solid #e2e8f0;color:#64748b;">Gross Salary</td>
    <td style="padding:10px;border:1px solid #e2e8f0;font-weight:600;text-align:right;">{{ $entry->currency }} {{ number_format((float) $entry->gross_salary, 0) }}</td>
  </tr>
  <tr>
    <td style="padding:10px;border:1px solid #e2e8f0;color:#64748b;">Total Deductions</td>
    <td style="padding:10px;border:1px solid #e2e8f0;color:#ef4444;font-weight:600;text-align:right;">– {{ $entry->currency }} {{ number_format((float) $entry->total_deductions, 0) }}</td>
  </tr>
  <tr style="background:#f0fdf8;">
    <td style="padding:10px;border:1px solid #e2e8f0;font-weight:700;">Net Pay</td>
    <td style="padding:10px;border:1px solid #e2e8f0;font-weight:700;color:#00C896;text-align:right;">{{ $entry->currency }} {{ number_format((float) $entry->net_salary, 0) }}</td>
  </tr>
</table>
<p>Please log in to the OPES portal to download your full payslip PDF.</p>
<hr style="border:none;border-top:1px solid #e2e8f0;margin:24px 0;">
<p style="color:#64748b;font-size:12px;">
    OPES Health Systems HR Department &bull; This is an automated notification.
</p>
</body>
</html>

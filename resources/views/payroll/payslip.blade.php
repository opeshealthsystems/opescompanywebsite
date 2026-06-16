<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; padding: 40px; }
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; border-bottom: 2px solid #00C896; padding-bottom: 16px; }
  .brand { font-size: 22px; font-weight: 700; color: #00C896; }
  .doc-title { text-align: right; }
  .doc-title h2 { font-size: 18px; color: #1e293b; margin-bottom: 4px; }
  .doc-title p { color: #64748b; font-size: 11px; }
  .two-col { display: flex; gap: 24px; margin-bottom: 24px; }
  .two-col .box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; }
  .box h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 10px; }
  .box-row { display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #f1f5f9; }
  .box-row:last-child { border-bottom: none; }
  .box-row .label { color: #64748b; }
  .box-row .value { font-weight: 600; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  th { background: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
  td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; }
  .amount { text-align: right; }
  .total-row td { font-weight: 700; background: #f8fafc; }
  .net-row td { font-weight: 700; font-size: 14px; color: #00C896; background: #f0fdf8; }
  .footer { margin-top: 32px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="brand">OPES Health Systems</div>
    <div style="color:#64748b;font-size:11px;margin-top:4px;">Payroll Department</div>
  </div>
  <div class="doc-title">
    <h2>PAYSLIP</h2>
    <p>Reference: {{ $run->reference }}</p>
    <p>Period: {{ $run->period_start->format('d M Y') }} – {{ $run->period_end->format('d M Y') }}</p>
  </div>
</div>

<div class="two-col">
  <div class="box">
    <h3>Employee</h3>
    <div class="box-row"><span class="label">Name</span><span class="value">{{ $entry->employee?->name ?? '—' }}</span></div>
    <div class="box-row"><span class="label">Employee ID</span><span class="value">{{ $entry->employee?->employee_id ?? '—' }}</span></div>
    <div class="box-row"><span class="label">Department</span><span class="value">{{ $entry->employee?->department ?? '—' }}</span></div>
    <div class="box-row"><span class="label">Position</span><span class="value">{{ $entry->employee?->position ?? '—' }}</span></div>
  </div>
  <div class="box">
    <h3>Payroll Run</h3>
    <div class="box-row"><span class="label">Run Reference</span><span class="value">{{ $run->reference }}</span></div>
    <div class="box-row"><span class="label">Currency</span><span class="value">{{ $entry->currency }}</span></div>
    <div class="box-row"><span class="label">Status</span><span class="value" style="text-transform:capitalize;">{{ $entry->status }}</span></div>
    <div class="box-row"><span class="label">Issue Date</span><span class="value">{{ now()->format('d M Y') }}</span></div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>Description</th>
      <th class="amount">Amount ({{ $entry->currency }})</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Gross Salary</td>
      <td class="amount">{{ number_format((float) $entry->gross_salary, 0) }}</td>
    </tr>

    @if ($entry->deductions && count($entry->deductions) > 0)
      @foreach ($entry->deductions as $deduction)
        <tr>
          <td style="padding-left:20px;color:#64748b;">
            {{ $deduction['label'] ?? $deduction['name'] ?? 'Deduction' }}
          </td>
          <td class="amount" style="color:#ef4444;">– {{ number_format((float) ($deduction['amount'] ?? 0), 0) }}</td>
        </tr>
      @endforeach
    @else
      <tr><td style="color:#94a3b8;padding-left:20px;">No deductions</td><td></td></tr>
    @endif

    <tr class="total-row">
      <td>Total Deductions</td>
      <td class="amount" style="color:#ef4444;">– {{ number_format((float) $entry->total_deductions, 0) }}</td>
    </tr>
  </tbody>
  <tfoot>
    <tr class="net-row">
      <td>Net Salary</td>
      <td class="amount">{{ $entry->currency }} {{ number_format((float) $entry->net_salary, 0) }}</td>
    </tr>
  </tfoot>
</table>

<div class="footer">
  This payslip is computer-generated and does not require a signature. &bull;
  OPES Health Systems &bull; Generated {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>

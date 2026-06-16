<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; padding: 40px; }
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; border-bottom: 2px solid #00C896; padding-bottom: 16px; }
  .brand { font-size: 22px; font-weight: 700; color: #00C896; }
  .doc-title h2 { font-size: 18px; text-align: right; margin-bottom: 4px; }
  .doc-title p { color: #64748b; font-size: 11px; text-align: right; }
  .two-col { display: flex; gap: 24px; margin-bottom: 20px; }
  .two-col .box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; }
  .box h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 10px; }
  .box-row { display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #f1f5f9; }
  .box-row:last-child { border-bottom: none; }
  .box-row .label { color: #64748b; }
  .box-row .value { font-weight: 600; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  th { background: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
  td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; }
  .amount { text-align: right; }
  .totals { margin-left: auto; width: 260px; }
  .totals-row { display: flex; justify-content: space-between; padding: 4px 8px; }
  .totals-row.grand { font-weight: 700; font-size: 14px; color: #00C896; background: #f0fdf8; border-radius: 4px; }
  .footer { margin-top: 32px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="brand">OPES Health Systems</div>
    <div style="color:#64748b;font-size:11px;margin-top:4px;">Sales Department</div>
  </div>
  <div class="doc-title">
    <h2>QUOTATION</h2>
    <p>Ref: {{ $quote->reference }}</p>
    <p>Valid Until: {{ $quote->valid_until?->format('d M Y') ?? '—' }}</p>
  </div>
</div>

<div class="two-col">
  <div class="box">
    <h3>Prepared For</h3>
    @if ($quote->lead)
      <div class="box-row"><span class="label">Name</span><span class="value">{{ $quote->lead->name }}</span></div>
      <div class="box-row"><span class="label">Email</span><span class="value">{{ $quote->lead->email ?? '—' }}</span></div>
      <div class="box-row"><span class="label">Phone</span><span class="value">{{ $quote->lead->phone ?? '—' }}</span></div>
    @else
      <div class="box-row"><span class="label">Client</span><span class="value">—</span></div>
    @endif
  </div>
  <div class="box">
    <h3>Quote Info</h3>
    <div class="box-row"><span class="label">Reference</span><span class="value">{{ $quote->reference }}</span></div>
    <div class="box-row"><span class="label">Title</span><span class="value">{{ $quote->title }}</span></div>
    <div class="box-row"><span class="label">Status</span><span class="value" style="text-transform:capitalize;">{{ $quote->status }}</span></div>
    <div class="box-row"><span class="label">Currency</span><span class="value">{{ $quote->currency }}</span></div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th style="width:40%;">Product / Service</th>
      <th>Description</th>
      <th class="amount">Qty</th>
      <th class="amount">Unit Price</th>
      <th class="amount">Total</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($quote->items as $item)
      <tr>
        <td>{{ $item->product_name }}</td>
        <td style="color:#64748b;">{{ $item->description ?? '—' }}</td>
        <td class="amount">{{ number_format((float) $item->quantity, 0) }}</td>
        <td class="amount">{{ number_format((float) $item->unit_price, 0) }}</td>
        <td class="amount">{{ number_format((float) $item->total, 0) }}</td>
      </tr>
    @empty
      <tr><td colspan="5" style="text-align:center;color:#94a3b8;">No line items</td></tr>
    @endforelse
  </tbody>
</table>

<div class="totals">
  <div class="totals-row"><span>Subtotal</span><span>{{ $quote->currency }} {{ number_format((float) $quote->subtotal, 0) }}</span></div>
  <div class="totals-row"><span>Tax ({{ number_format((float) $quote->tax_rate, 1) }}%)</span><span>{{ $quote->currency }} {{ number_format((float) $quote->tax_amount, 0) }}</span></div>
  <div class="totals-row grand"><span>Total</span><span>{{ $quote->currency }} {{ number_format((float) $quote->total, 0) }}</span></div>
</div>

@if ($quote->notes)
<div style="margin-top:20px;padding:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:4px;">
  <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:6px;">Notes</div>
  {{ $quote->notes }}
</div>
@endif

<div class="footer">
  OPES Health Systems &bull; {{ $quote->reference }} &bull; Generated {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>

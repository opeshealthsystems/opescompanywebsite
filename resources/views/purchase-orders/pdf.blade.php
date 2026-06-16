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
  .sig-block { display: flex; gap: 40px; margin-top: 48px; }
  .sig-line { flex: 1; border-top: 1px solid #94a3b8; padding-top: 6px; font-size: 11px; color: #64748b; }
  .footer { margin-top: 32px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="brand">OPES Health Systems</div>
    <div style="color:#64748b;font-size:11px;margin-top:4px;">Procurement Department</div>
  </div>
  <div class="doc-title">
    <h2>PURCHASE ORDER</h2>
    <p>PO: {{ $purchaseOrder->reference }}</p>
    <p>Date: {{ now()->format('d M Y') }}</p>
  </div>
</div>

<div class="two-col">
  <div class="box">
    <h3>Vendor</h3>
    <div class="box-row"><span class="label">Name</span><span class="value">{{ $purchaseOrder->vendor?->name ?? $purchaseOrder->vendor_name ?? '—' }}</span></div>
    <div class="box-row"><span class="label">Contact</span><span class="value">{{ $purchaseOrder->vendor?->contact_name ?? '—' }}</span></div>
    <div class="box-row"><span class="label">Email</span><span class="value">{{ $purchaseOrder->vendor?->email ?? '—' }}</span></div>
    <div class="box-row"><span class="label">Tax ID</span><span class="value">{{ $purchaseOrder->vendor?->tax_id ?? '—' }}</span></div>
  </div>
  <div class="box">
    <h3>Order Info</h3>
    <div class="box-row"><span class="label">Reference</span><span class="value">{{ $purchaseOrder->reference }}</span></div>
    <div class="box-row"><span class="label">Status</span><span class="value" style="text-transform:capitalize;">{{ $purchaseOrder->status }}</span></div>
    <div class="box-row"><span class="label">Expected</span><span class="value">{{ $purchaseOrder->expected_date?->format('d M Y') ?? '—' }}</span></div>
    <div class="box-row"><span class="label">Requested By</span><span class="value">{{ $purchaseOrder->requester?->name ?? '—' }}</span></div>
    @if ($purchaseOrder->approver)
    <div class="box-row"><span class="label">Approved By</span><span class="value">{{ $purchaseOrder->approver->name }}</span></div>
    @endif
  </div>
</div>

@if ($purchaseOrder->title)
<div style="margin-bottom:16px;padding:10px 14px;background:#f8fafc;border-radius:4px;">
  <strong>{{ $purchaseOrder->title }}</strong>
  @if ($purchaseOrder->description)<br><span style="color:#64748b;">{{ $purchaseOrder->description }}</span>@endif
</div>
@endif

<table>
  <thead>
    <tr>
      <th style="width:50%;">Description</th>
      <th class="amount">Qty</th>
      <th class="amount">Unit Price ({{ $purchaseOrder->currency }})</th>
      <th class="amount">Total ({{ $purchaseOrder->currency }})</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($purchaseOrder->items as $item)
      <tr>
        <td>{{ $item->description }}</td>
        <td class="amount">{{ number_format((float) $item->quantity, 0) }}</td>
        <td class="amount">{{ number_format((float) $item->unit_price, 0) }}</td>
        <td class="amount">{{ number_format((float) $item->total, 0) }}</td>
      </tr>
    @empty
      <tr><td colspan="4" style="text-align:center;color:#94a3b8;">No line items</td></tr>
    @endforelse
  </tbody>
</table>

<div class="totals">
  <div class="totals-row"><span>Subtotal</span><span>{{ $purchaseOrder->currency }} {{ number_format((float) $purchaseOrder->subtotal, 0) }}</span></div>
  <div class="totals-row"><span>Tax</span><span>{{ $purchaseOrder->currency }} {{ number_format((float) $purchaseOrder->tax_amount, 0) }}</span></div>
  <div class="totals-row grand"><span>Total</span><span>{{ $purchaseOrder->currency }} {{ number_format((float) $purchaseOrder->total, 0) }}</span></div>
</div>

<div class="sig-block">
  <div class="sig-line">Requested By: {{ $purchaseOrder->requester?->name ?? '___________' }}</div>
  <div class="sig-line">Approved By: {{ $purchaseOrder->approver?->name ?? '___________' }}</div>
  <div class="sig-line">Date: {{ $purchaseOrder->approved_at?->format('d M Y') ?? '___________' }}</div>
</div>

<div class="footer">
  OPES Health Systems &bull; {{ $purchaseOrder->reference }} &bull; Generated {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; margin: 0; padding: 0; }
        .page { padding: 40px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left { display: table-cell; width: 50%; }
        .header-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        .company-name { font-size: 20px; font-weight: bold; color: #0f172a; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #00C896; margin-bottom: 4px; }
        .invoice-number { font-size: 14px; color: #64748b; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-draft     { background: #e2e8f0; color: #475569; }
        .status-sent      { background: #dbeafe; color: #1d4ed8; }
        .status-paid      { background: #d1fae5; color: #065f46; }
        .status-overdue   { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #e2e8f0; color: #475569; }
        .meta-table { width: 100%; margin-bottom: 30px; }
        .meta-table td { padding: 4px 8px; font-size: 11px; }
        .meta-label { color: #64748b; font-weight: bold; width: 130px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #0f172a; color: #e2e8f0; padding: 8px 10px; text-align: left; font-size: 11px; }
        .items-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 11px; }
        .items-table tr:nth-child(even) td { background: #f8fafc; }
        .text-right { text-align: right; }
        .totals { margin-left: 60%; }
        .totals table { width: 100%; }
        .totals td { padding: 4px 8px; font-size: 12px; }
        .totals .label { color: #64748b; }
        .totals .amount { text-align: right; }
        .grand-total td { font-weight: bold; font-size: 14px; border-top: 2px solid #0f172a; padding-top: 8px; }
        .notes { margin-top: 30px; padding: 12px; background: #f8fafc; border-left: 3px solid #00C896; font-size: 11px; }
        .footer { margin-top: 40px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="header-left">
            <div class="company-name">OPES Health Systems</div>
            <div style="color:#64748b;font-size:11px;margin-top:4px;">Digital Health Solutions</div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            <div style="margin-top:6px;">
                <span class="status-badge status-{{ $invoice->status }}">{{ \App\Models\Invoice::statusOptions()[$invoice->status] ?? $invoice->status }}</span>
            </div>
        </div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Bill To:</td>
            <td>{{ $invoice->customer?->name ?? 'N/A' }}<br>{{ $invoice->customer?->email ?? '' }}</td>
            <td class="meta-label">Invoice Date:</td>
            <td>{{ $invoice->created_at->format('d M Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Issued By:</td>
            <td>{{ $invoice->issuer?->name ?? 'OPES Health Systems' }}</td>
            <td class="meta-label">Due Date:</td>
            <td>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
        </tr>
        @if($invoice->paid_at)
        <tr>
            <td class="meta-label">Paid On:</td>
            <td colspan="3">{{ $invoice->paid_at->format('d M Y') }}</td>
        </tr>
        @endif
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right" style="width:60px;">Qty</th>
                <th class="text-right" style="width:120px;">Unit Price</th>
                <th class="text-right" style="width:120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price) }}</td>
                <td class="text-right">{{ number_format($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="label">Subtotal</td>
                <td class="amount">{{ number_format($invoice->subtotal) }} {{ $invoice->currency }}</td>
            </tr>
            @if($invoice->tax_rate > 0)
            <tr>
                <td class="label">Tax ({{ $invoice->tax_rate }}%)</td>
                <td class="amount">{{ number_format($invoice->taxAmount) }} {{ $invoice->currency }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Total</td>
                <td class="amount">{{ number_format($invoice->grandTotal) }} {{ $invoice->currency }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes)
    <div class="notes">
        <strong>Notes:</strong> {{ $invoice->notes }}
    </div>
    @endif

    <div class="footer">
        OPES Health Systems &mdash; {{ $invoice->invoice_number }} &mdash; Generated {{ now()->format('d M Y') }}
    </div>
</div>
</body>
</html>

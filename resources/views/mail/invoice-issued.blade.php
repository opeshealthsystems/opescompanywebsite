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
.inv-badge { display: inline-block; background: #f1f5f9; border-radius: 6px; padding: 4px 12px; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 16px; font-family: monospace; }
.detail-row { margin-bottom: 10px; }
.label { color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px; }
.value { color: #1e293b; font-size: 14px; }
.items-table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px; }
.items-table th { background: #f1f5f9; padding: 8px 12px; text-align: left; color: #64748b; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; }
.items-table td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
.items-table .total-row td { font-weight: 700; color: #0f172a; border-bottom: none; border-top: 2px solid #e2e8f0; padding-top: 12px; }
.amount { font-size: 1.4rem; font-weight: 800; color: #0f172a; }
.cta { display: inline-block; margin-top: 24px; background: #00C896; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 14px; }
.due-box { background: #fef9c3; border: 1px solid #fef08a; border-radius: 6px; padding: 12px 16px; margin: 20px 0; font-size: 13px; color: #713f12; }
.footer { padding: 16px 32px; background: #f8fafc; color: #94a3b8; font-size: 12px; text-align: center; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <p class="header-title">Invoice from OPES Health Systems</p>
    </div>
    <div class="body">
        <p style="color:#334155;font-size:15px;margin-top:0">Dear {{ $invoice->customer?->name }},</p>
        <p style="color:#334155;font-size:14px;margin-bottom:20px">Please find your invoice details below. You can download the PDF from your customer portal.</p>

        <span class="inv-badge">{{ $invoice->invoice_number }}</span>

        <div class="detail-row">
            <span class="label">Status</span>
            <span class="value" style="color:#1A6FE8;font-weight:700">Sent — Awaiting Payment</span>
        </div>

        @if($invoice->due_date)
        <div class="due-box">
            ⚠️ Payment due by <strong>{{ $invoice->due_date->format('d F Y') }}</strong>
        </div>
        @endif

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:50%">Description</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Unit Price</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">{{ number_format($item->unit_price) }}</td>
                    <td style="text-align:right">{{ number_format($item->total) }}</td>
                </tr>
                @endforeach
                @if($invoice->tax_rate > 0)
                <tr><td colspan="3" style="text-align:right;color:#64748b;padding-top:8px">Subtotal</td><td style="text-align:right;padding-top:8px">{{ number_format($invoice->subtotal) }}</td></tr>
                <tr><td colspan="3" style="text-align:right;color:#64748b">Tax ({{ $invoice->tax_rate }}%)</td><td style="text-align:right">{{ number_format($invoice->taxAmount) }}</td></tr>
                @endif
                <tr class="total-row">
                    <td colspan="3" style="text-align:right">Grand Total ({{ $invoice->currency }})</td>
                    <td style="text-align:right"><span class="amount">{{ number_format($invoice->grand_total) }}</span></td>
                </tr>
            </tbody>
        </table>

        @if($invoice->notes)
        <p style="font-size:13px;color:#64748b;background:#f8fafc;padding:12px 16px;border-radius:6px;margin-top:8px">{{ $invoice->notes }}</p>
        @endif

        <a href="{{ config('app.url') }}/en/customer/invoices/{{ $invoice->id }}" class="cta">View Invoice &amp; Download PDF</a>

        <p style="color:#94a3b8;font-size:12px;margin-top:20px">For payment enquiries contact <a href="mailto:billing@opeshealthsystems.com" style="color:#00C896">billing@opeshealthsystems.com</a></p>
    </div>
    <div class="footer">OPES Health Systems · billing@opeshealthsystems.com · Bonamousadi, Douala, Cameroon</div>
</div>
</body>
</html>

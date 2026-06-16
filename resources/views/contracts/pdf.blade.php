<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; padding: 40px; }
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; border-bottom: 2px solid #00C896; padding-bottom: 16px; }
  .brand { font-size: 22px; font-weight: 700; color: #00C896; }
  .doc-title h2 { font-size: 18px; color: #1e293b; margin-bottom: 4px; text-align: right; }
  .doc-title p { color: #64748b; font-size: 11px; text-align: right; }
  .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; }
  .badge-active { background: #dcfce7; color: #166534; }
  .badge-draft { background: #f1f5f9; color: #475569; }
  .badge-expired { background: #fee2e2; color: #991b1b; }
  .badge-terminated { background: #fef3c7; color: #92400e; }
  .section { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin-bottom: 20px; }
  .section h3 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 12px; }
  .grid { display: flex; flex-wrap: wrap; gap: 12px; }
  .field { flex: 1; min-width: 160px; }
  .field .label { font-size: 10px; color: #94a3b8; text-transform: uppercase; margin-bottom: 2px; }
  .field .value { font-weight: 600; }
  .notes-box { padding: 12px; background: #fff; border: 1px solid #e2e8f0; border-radius: 4px; margin-top: 8px; line-height: 1.5; }
  .footer { margin-top: 32px; text-align: center; color: #94a3b8; font-size: 10px; border-top: 1px solid #e2e8f0; padding-top: 12px; }
</style>
</head>
<body>

<div class="header">
  <div>
    <div class="brand">OPES Health Systems</div>
    <div style="color:#64748b;font-size:11px;margin-top:4px;">Contracts Department</div>
  </div>
  <div class="doc-title">
    <h2>CONTRACT</h2>
    <p>{{ $contract->reference }}</p>
    <p>Generated {{ now()->format('d M Y') }}</p>
  </div>
</div>

<div class="section">
  <h3>Contract Details</h3>
  <div class="grid">
    <div class="field"><div class="label">Title</div><div class="value">{{ $contract->title }}</div></div>
    <div class="field"><div class="label">Type</div><div class="value">{{ \App\Models\Contract::typeOptions()[$contract->type] ?? $contract->type }}</div></div>
    <div class="field"><div class="label">Status</div><div class="value"><span class="badge badge-{{ $contract->status }}">{{ $contract->status }}</span></div></div>
    <div class="field"><div class="label">Value</div><div class="value">{{ $contract->formatValue() }}</div></div>
    <div class="field"><div class="label">Start Date</div><div class="value">{{ $contract->start_date?->format('d M Y') ?? '—' }}</div></div>
    <div class="field"><div class="label">End Date</div><div class="value">{{ $contract->end_date?->format('d M Y') ?? '—' }}</div></div>
    <div class="field"><div class="label">Auto-Renew</div><div class="value">{{ $contract->auto_renew ? 'Yes' : 'No' }}</div></div>
    <div class="field"><div class="label">Signed At</div><div class="value">{{ $contract->signed_at?->format('d M Y') ?? 'Not signed' }}</div></div>
  </div>
</div>

@if ($contract->lead)
<div class="section">
  <h3>Associated Lead / Client</h3>
  <div class="grid">
    <div class="field"><div class="label">Name</div><div class="value">{{ $contract->lead->name }}</div></div>
    <div class="field"><div class="label">Email</div><div class="value">{{ $contract->lead->email ?? '—' }}</div></div>
    <div class="field"><div class="label">Phone</div><div class="value">{{ $contract->lead->phone ?? '—' }}</div></div>
  </div>
</div>
@endif

@if ($contract->notes)
<div class="section">
  <h3>Notes</h3>
  <div class="notes-box">{{ $contract->notes }}</div>
</div>
@endif

<div class="footer">
  OPES Health Systems &bull; {{ $contract->reference }} &bull; Generated {{ now()->format('d M Y H:i') }}
</div>

</body>
</html>

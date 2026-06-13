<x-layouts.customer title="My Documents">
    <div class="cp-page-header">
        <div>
            <h1 class="cp-page-title">My Documents</h1>
            <p class="cp-page-subtitle">Receipts, contracts, and official correspondence issued to you</p>
        </div>
    </div>

    @if($documents->isEmpty())
        <div class="cp-section-card" style="text-align:center;padding:3rem;">
            <div class="cp-empty-state">
                <i data-lucide="file-text" style="width:48px;height:48px;color:#334155"></i>
                <p>No documents issued yet.</p>
                <p style="font-size:0.8125rem">Documents from OPES Health Systems will appear here once issued.</p>
            </div>
        </div>
    @else
        <div class="cp-section-card" style="padding:0;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #334155;">
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Reference</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Title</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Type</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Status</th>
                        <th style="text-align:left;padding:0.75rem;color:#94a3b8;font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Date</th>
                        <th style="padding:0.75rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $doc)
                    <tr style="border-bottom:1px solid #1e293b;">
                        <td style="padding:0.75rem;color:#00C896;font-size:0.8125rem;font-family:monospace;">{{ $doc->reference_number }}</td>
                        <td style="padding:0.75rem;color:#e2e8f0;font-size:0.875rem;">{{ $doc->title }}</td>
                        <td style="padding:0.75rem;">
                            <span style="background:rgba(100,116,139,0.15);color:#94a3b8;font-size:0.7rem;font-weight:600;padding:0.2rem 0.5rem;border-radius:20px;text-transform:uppercase;letter-spacing:0.04em;">
                                {{ \App\Models\DocumentTemplate::typeLabel($doc->type) }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;">
                            @php
                                $statusColor = match($doc->status) {
                                    'signed'            => '#00C896',
                                    'pending_signature' => '#eab308',
                                    'voided'            => '#ef4444',
                                    default             => '#94a3b8',
                                };
                            @endphp
                            <span style="color:{{ $statusColor }};font-size:0.8125rem;font-weight:600;text-transform:capitalize;">
                                {{ str_replace('_', ' ', $doc->status) }}
                            </span>
                        </td>
                        <td style="padding:0.75rem;color:#64748b;font-size:0.8125rem;">{{ $doc->created_at->format('d M Y') }}</td>
                        <td style="padding:0.75rem;text-align:right;">
                            <a href="{{ route('customer.documents.show', ['locale' => app()->getLocale(), 'id' => $doc->id]) }}"
                               class="cp-btn-outline" style="font-size:0.75rem;padding:0.375rem 0.75rem;">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="padding:1rem 0.75rem 0;">
                {{ $documents->links() }}
            </div>
        </div>
    @endif
</x-layouts.customer>

<x-filament-panels::page>

    {{-- ── STAT CARDS ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Active Licenses</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $activeLicenses }}</p>
            @if($expiringLicenses > 0)
                <p class="mt-1 text-xs text-amber-500">{{ $expiringLicenses }} expiring in 30 days</p>
            @endif
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Revenue Collected</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($paidTotal) }}</p>
            <p class="mt-1 text-xs text-gray-400">XAF · paid invoices</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Open Tickets</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $openTickets }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $resolvedThisMonth }} resolved this month</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">New Leads (30d)</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $newLeads }}</p>
            <p class="mt-1 text-xs text-gray-400">{{ $qualifiedLeads }} qualified</p>
        </div>

    </div>

    {{-- ── SECOND ROW ──────────────────────────────────────────────── --}}
    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-2">

        {{-- Licenses by Product --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-white/10">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Active Licenses by Product</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($licensesByProduct as $row)
                    <div class="flex items-center justify-between px-5 py-3">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $row->product_name }}</span>
                        <span class="rounded-full bg-primary-50 px-2.5 py-0.5 text-xs font-semibold text-primary-700 dark:bg-primary-500/10 dark:text-primary-400">
                            {{ $row->total }}
                        </span>
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-gray-400">No active licenses.</p>
                @endforelse
            </div>
        </div>

        {{-- Overdue / Outstanding Invoices --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-white/10">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                    Outstanding Invoices
                    @if($overdueCount > 0)
                        <span class="ml-2 rounded-full bg-danger-50 px-2 py-0.5 text-xs font-semibold text-danger-600 dark:bg-danger-500/10 dark:text-danger-400">
                            {{ $overdueCount }} overdue
                        </span>
                    @endif
                </h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($recentInvoices as $invoice)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="font-mono text-xs text-gray-500">{{ $invoice->invoice_number }}</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $invoice->customer?->name ?? '—' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $invoice->formatAmount($invoice->grand_total) }}</p>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $invoice->status === 'overdue' ? 'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400' : 'bg-info-50 text-info-600 dark:bg-info-500/10 dark:text-info-400' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-4 text-sm text-gray-400">No outstanding invoices.</p>
                @endforelse
            </div>
        </div>

    </div>

</x-filament-panels::page>

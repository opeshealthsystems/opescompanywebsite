<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Total Payables Outstanding:
                <span class="text-danger-600">XAF {{ number_format($this->getTotalOutstanding(), 0) }}</span>
            </h2>
        </div>

        @foreach ($this->getAgingData() as $key => $bucket)
            @if (count($bucket['bills']) > 0)
                <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $bucket['label'] }}</h3>
                        <span class="text-sm font-medium {{ $key === 'current' ? 'text-green-600' : ($key === '1_30' ? 'text-yellow-600' : 'text-red-600') }}">
                            XAF {{ number_format($bucket['total'], 0) }}
                            ({{ count($bucket['bills']) }} bill{{ count($bucket['bills']) !== 1 ? 's' : '' }})
                        </span>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bill #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Overdue</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach ($bucket['bills'] as $bill)
                                <tr>
                                    <td class="px-6 py-3 font-mono text-sm text-gray-900 dark:text-white">{{ $bill['reference'] }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $bill['vendor'] }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-400">{{ $bill['bill_number'] }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $bill['status'])) }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $bill['due_date'] }}</td>
                                    <td class="px-6 py-3 text-sm {{ ($bill['days_overdue'] ?? 0) > 0 ? 'text-red-600 font-medium' : 'text-gray-400' }}">
                                        {{ $bill['days_overdue'] !== null && $bill['days_overdue'] > 0 ? $bill['days_overdue'].' days' : '—' }}
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                        {{ $bill['currency'] }} {{ number_format($bill['amount'], 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach

        @if (collect($this->getAgingData())->every(fn($b) => count($b['bills']) === 0))
            <div class="text-center text-gray-500 py-12">No outstanding payables.</div>
        @endif
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="space-y-6">
        @foreach ($this->getExpiryBuckets() as $key => $bucket)
            @if (count($bucket['rows']) > 0)
                <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $bucket['label'] }}</h3>
                        <span class="text-sm text-gray-500">{{ count($bucket['rows']) }} contract{{ count($bucket['rows']) !== 1 ? 's' : '' }}</span>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Left</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Auto-Renew</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach ($bucket['rows'] as $row)
                                <tr>
                                    <td class="px-6 py-3 font-mono text-sm text-gray-900 dark:text-white">{{ $row['reference'] }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $row['type'])) }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ ucfirst($row['status']) }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-500">{{ $row['end_date'] }}</td>
                                    <td class="px-6 py-3 text-sm font-medium {{ $row['days_left'] < 0 ? 'text-red-600' : ($row['days_left'] <= 30 ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ $row['days_left'] < 0 ? abs($row['days_left']).' days ago' : $row['days_left'].' days' }}
                                    </td>
                                    <td class="px-6 py-3 text-sm">
                                        @if($row['auto_renew'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Yes</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">No</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                        {{ $row['currency'] }} {{ number_format($row['value'], 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach

        @if (empty(array_filter(array_column($this->getExpiryBuckets(), 'rows'), fn($r) => count($r) > 0)))
            <div class="text-center text-gray-500 py-12">No active contracts found.</div>
        @endif
    </div>
</x-filament-panels::page>

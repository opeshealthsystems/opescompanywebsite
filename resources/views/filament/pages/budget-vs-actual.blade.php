<x-filament-panels::page>
    <div class="mb-4 flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Year:</label>
        <select
            wire:model.live="selectedYear"
            class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200"
        >
            @foreach(range(now()->year - 2, now()->year + 1) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    @php($rows = $this->getReportData()['items'])

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Category</th><th class="px-4 py-3">Department</th>
                        <th class="px-4 py-3 text-right">Budget</th><th class="px-4 py-3 text-right">Actual spent</th>
                        <th class="px-4 py-3 text-right">Variance</th><th class="px-4 py-3 text-right">Used %</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($rows as $row)
                        @php($currency = $row['currency'])
                        <tr class="text-gray-700 dark:text-gray-300">
                            <td class="px-4 py-3"><span class="rounded-md bg-gray-100 px-2 py-1 text-xs dark:bg-gray-800">{{ \App\Models\Budget::categoryOptions()[$row['category']] ?? $row['category'] }}</span></td>
                            <td class="px-4 py-3">{{ $row['department'] ?: '—' }}</td>
                            <td class="px-4 py-3 text-right font-medium">{{ $currency }} {{ number_format($row['allocated_amount'], 0) }}</td>
                            <td class="px-4 py-3 text-right">{{ $currency }} {{ number_format($row['actual_spent'], 0) }}</td>
                            <td class="px-4 py-3 text-right font-medium {{ $row['variance'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">{{ $row['variance'] >= 0 ? '+' : '' }}{{ $currency }} {{ number_format($row['variance'], 0) }}</td>
                            <td class="px-4 py-3 text-right">{{ $row['utilization'] === null ? '—' : $row['utilization'].'%' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No budgets found for {{ $selectedYear }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>

<x-filament-panels::page>
    <div class="mb-4 flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Year:</label>
        <select wire:model.live="selectedYear" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">
            @foreach(range(now()->year - 3, now()->year) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Active Staff</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $report['total_staff'] }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Payroll Runs ({{ $selectedYear }})</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ count($report['runs']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Net Paid ({{ $selectedYear }})</p>
            <p class="text-2xl font-bold text-orange-500 dark:text-orange-400 mt-1">XAF {{ number_format($report['total_payroll_net'], 0) }}</p>
        </div>
    </div>

    @if(count($report['runs']) === 0)
        <div class="text-center py-12 text-gray-400">No completed payroll runs for {{ $selectedYear }}.</div>
    @else
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Reference</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Period</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Headcount</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Gross Total</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Net Total</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Avg Net / Person</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($report['runs'] as $row)
                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 font-mono text-gray-700 dark:text-gray-300">{{ $row['reference'] }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $row['period'] }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $row['headcount'] }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $row['currency'] }} {{ number_format($row['gross'],0) }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-orange-500 dark:text-orange-400">{{ $row['currency'] }} {{ number_format($row['net'],0) }}</td>
                    <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">{{ $row['currency'] }} {{ number_format($row['avg_net'],0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</x-filament-panels::page>

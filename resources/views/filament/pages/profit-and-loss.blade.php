<x-filament-panels::page>
    <div class="mb-4 flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Year:</label>
        <select wire:model.live="selectedYear" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">
            @foreach(range(now()->year - 3, now()->year) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mb-6">
        @php
            $fmt = fn($n) => 'XAF ' . number_format($n, 0);
            $netColor = $report['net'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400';
        @endphp
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Revenue</p>
            <p class="text-xl font-bold text-green-600 dark:text-green-400 mt-1">{{ $fmt($report['total_revenue']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Expenses</p>
            <p class="text-xl font-bold text-red-500 dark:text-red-400 mt-1">{{ $fmt($report['total_expenses']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Total Payroll</p>
            <p class="text-xl font-bold text-orange-500 dark:text-orange-400 mt-1">{{ $fmt($report['total_payroll']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Net P&L</p>
            <p class="text-xl font-bold {{ $netColor }} mt-1">{{ ($report['net'] >= 0 ? '+' : '') . $fmt($report['net']) }}</p>
        </div>
    </div>

    {{-- Monthly table --}}
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Month</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Revenue</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Expenses</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Payroll</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Total Costs</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Net P&L</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($report['months'] as $row)
                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $row['month'] }}</td>
                    <td class="px-4 py-3 text-right text-green-600 dark:text-green-400">{{ $row['revenue'] > 0 ? 'XAF '.number_format($row['revenue'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-red-500 dark:text-red-400">{{ $row['expenses'] > 0 ? 'XAF '.number_format($row['expenses'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-orange-500 dark:text-orange-400">{{ $row['payroll'] > 0 ? 'XAF '.number_format($row['payroll'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $row['total_costs'] > 0 ? 'XAF '.number_format($row['total_costs'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $row['net'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ ($row['net'] >= 0 ? '+' : '') . 'XAF '.number_format($row['net'],0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 dark:bg-gray-800 border-t-2 border-gray-300 dark:border-gray-600">
                <tr>
                    <td class="px-4 py-3 font-bold text-gray-800 dark:text-gray-200">Total {{ $selectedYear }}</td>
                    <td class="px-4 py-3 text-right font-bold text-green-600 dark:text-green-400">XAF {{ number_format($report['total_revenue'],0) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-red-500 dark:text-red-400">XAF {{ number_format($report['total_expenses'],0) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-orange-500 dark:text-orange-400">XAF {{ number_format($report['total_payroll'],0) }}</td>
                    <td class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-300">XAF {{ number_format($report['total_costs'],0) }}</td>
                    <td class="px-4 py-3 text-right font-bold {{ $report['net'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ ($report['net'] >= 0 ? '+' : '') . 'XAF '.number_format($report['net'],0) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</x-filament-panels::page>

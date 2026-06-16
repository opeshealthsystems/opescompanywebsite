<x-filament-panels::page>
    <div class="mb-4 flex items-center gap-3">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Year:</label>
        <select wire:model.live="selectedYear" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200">
            @foreach(range(now()->year - 3, now()->year) as $y)
                <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Month</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Cash In</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Expenses Out</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Payroll Out</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Total Out</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Net Cash</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Cumulative</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($report['months'] as $row)
                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $row['month'] }}</td>
                    <td class="px-4 py-3 text-right text-green-600 dark:text-green-400">{{ $row['cash_in'] > 0 ? 'XAF '.number_format($row['cash_in'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-red-500 dark:text-red-400">{{ $row['cash_out_exp'] > 0 ? 'XAF '.number_format($row['cash_out_exp'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-orange-500 dark:text-orange-400">{{ $row['cash_out_pay'] > 0 ? 'XAF '.number_format($row['cash_out_pay'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">{{ $row['cash_out'] > 0 ? 'XAF '.number_format($row['cash_out'],0) : '—' }}</td>
                    <td class="px-4 py-3 text-right font-semibold {{ $row['net_cash'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ ($row['net_cash'] >= 0 ? '+' : '') . 'XAF '.number_format($row['net_cash'],0) }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold {{ $row['cumulative'] >= 0 ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                        XAF {{ number_format($row['cumulative'],0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>

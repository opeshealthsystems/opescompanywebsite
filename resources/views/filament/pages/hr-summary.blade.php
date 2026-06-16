<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Stats row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach ([
                ['Total Staff', $this->getTotalHeadcount(), 'text-blue-600'],
                ['Open Positions', $this->getOpenPositions(), 'text-amber-600'],
                ['Leave Requests (Month)', $this->getLeaveStats()['total_requests'], 'text-purple-600'],
                ['Pending Leave', $this->getLeaveStats()['pending'], 'text-orange-600'],
            ] as [$label, $value, $color])
                <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-6 text-center">
                    <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
                    <div class="text-sm text-gray-500 mt-1">{{ $label }}</div>
                </div>
            @endforeach
        </div>

        {{-- Headcount by department --}}
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Headcount by Department</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Headcount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Share</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @php $total = max($this->getTotalHeadcount(), 1); @endphp
                    @foreach ($this->getHeadcountByDepartment() as $row)
                        <tr>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                            <td class="px-6 py-3 text-sm text-right font-medium text-blue-600">{{ $row['count'] }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ round($row['count']/$total*100) }}%"></div>
                                    </div>
                                    <span class="text-xs">{{ round($row['count']/$total*100) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Leave stats --}}
        @php $leave = $this->getLeaveStats(); @endphp
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-6">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Leave Requests — {{ now()->format('F Y') }}</h3>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div><div class="text-2xl font-bold text-green-600">{{ $leave['approved'] }}</div><div class="text-xs text-gray-500 mt-1">Approved</div></div>
                <div><div class="text-2xl font-bold text-yellow-600">{{ $leave['pending'] }}</div><div class="text-xs text-gray-500 mt-1">Pending</div></div>
                <div><div class="text-2xl font-bold text-red-600">{{ $leave['rejected'] }}</div><div class="text-xs text-gray-500 mt-1">Rejected</div></div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

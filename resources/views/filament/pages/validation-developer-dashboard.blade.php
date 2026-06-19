<x-filament-panels::page>
    @php $t = $this->getThroughput(); @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([
            ['Total Tasks', $t['total'], 'text-blue-600'],
            ['Reopened Rate', $t['reopened_rate'].'%', 'text-orange-600'],
            ['Avg Days to Fix', $t['avg_days_to_fix'], 'text-amber-600'],
            ['Fixed', $t['by_status']['fixed'] ?? 0, 'text-emerald-600'],
        ] as [$label, $value, $color])
            <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-6 text-center">
                <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">By Status</h3>
            <ul class="space-y-1 text-sm">
                @foreach ($t['by_status'] as $key => $count)
                    <li class="flex justify-between"><span class="text-gray-500">{{ \App\Models\DeveloperTask::statusOptions()[$key] ?? $key }}</span><span class="font-medium">{{ $count }}</span></li>
                @endforeach
            </ul>
        </div>
        <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-3">By Assignee</h3>
            <ul class="space-y-1 text-sm">
                @forelse ($t['by_assignee'] as $row)
                    <li class="flex justify-between"><span class="text-gray-500">{{ $row['name'] }}</span><span class="font-medium">{{ $row['count'] }}</span></li>
                @empty
                    <li class="text-gray-400">No tasks yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-filament-panels::page>

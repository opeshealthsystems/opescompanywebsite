<x-filament-panels::page>
    @php $a = $this->getAnalytics(); @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach ([
            ['Total Issues', $a['total'], 'text-blue-600'],
            ['Retest Pass Rate', $a['retest_pass_rate'].'%', 'text-emerald-600'],
            ['Avg Days to Close', $a['avg_days_to_close'], 'text-amber-600'],
            ['Closed', $a['by_status']['closed'] ?? 0, 'text-gray-600'],
        ] as [$label, $value, $color])
            <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-6 text-center">
                <div class="text-3xl font-bold {{ $color }}">{{ $value }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        @foreach ([
            ['By Status', $a['by_status'], \App\Models\IssueReport::statusOptions()],
            ['By Severity', $a['by_severity'], \App\Models\IssueReport::severityOptions()],
            ['By Type', $a['by_type'], \App\Models\IssueReport::issueTypeOptions()],
        ] as [$title, $data, $labels])
            <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">{{ $title }}</h3>
                <ul class="space-y-1 text-sm">
                    @foreach ($data as $key => $count)
                        @if ($count > 0)
                            <li class="flex justify-between"><span class="text-gray-500">{{ $labels[$key] ?? $key }}</span><span class="font-medium">{{ $count }}</span></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>

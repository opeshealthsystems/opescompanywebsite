<x-filament-panels::page>
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @foreach (['Cohort', 'Status', 'Active Members', 'Sessions', 'Workflow Coverage', 'Issues'] as $h)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($this->getRows() as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ ucfirst($row['status']) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['active_members'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['sessions'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            {{ $row['covered_test_cases'] }}/{{ $row['assigned_test_cases'] }} ({{ $row['coverage_pct'] }}%)
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['issues'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No cohorts yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-xs text-gray-400 mt-2">Workflow Coverage counts assigned test cases whose workflow has been exercised in at least one session — not per-test-case execution.</p>
</x-filament-panels::page>

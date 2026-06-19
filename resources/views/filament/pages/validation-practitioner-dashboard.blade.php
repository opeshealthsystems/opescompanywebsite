<x-filament-panels::page>
    <div class="rounded-xl bg-white dark:bg-gray-900 shadow ring-1 ring-gray-200 dark:ring-gray-800 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @foreach (['Practitioner', 'Cohort', 'Sessions', 'Issues Found', 'Accepted', 'Retests'] as $h)
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($this->getLeaderboard() as $row)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row['member'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['cohort'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['sessions'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['issues_found'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['issues_accepted'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $row['retests'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-sm text-gray-400">No members yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>

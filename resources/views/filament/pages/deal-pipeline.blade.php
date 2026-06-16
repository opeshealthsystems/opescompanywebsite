<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Stage summary bar --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @foreach ($this->getStageStats() as $stage => $stats)
                <div class="rounded-lg bg-white dark:bg-gray-900 ring-1 ring-gray-200 dark:ring-gray-800 p-3 text-center">
                    <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['count'] }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ ucfirst(str_replace('_', ' ', $stage)) }}</div>
                    <div class="text-xs font-medium text-primary-600 mt-1">
                        XAF {{ number_format((float) ($stats['total_value'] ?? 0), 0) }}
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Kanban columns (horizontal scroll) --}}
        <div class="flex gap-4 overflow-x-auto pb-4">
            @php
                $stageColors = [
                    'prospecting'   => 'bg-gray-100 dark:bg-gray-800 border-gray-300',
                    'qualification' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200',
                    'proposal'      => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200',
                    'negotiation'   => 'bg-orange-50 dark:bg-orange-900/20 border-orange-200',
                    'closed_won'    => 'bg-green-50 dark:bg-green-900/20 border-green-200',
                    'closed_lost'   => 'bg-red-50 dark:bg-red-900/20 border-red-200',
                ];
                $dealsByStage = $this->getDealsByStage();
                $stageKeys    = array_keys($this->getStages());
            @endphp

            @foreach ($this->getStages() as $stageKey => $stageLabel)
                @php
                    $stageDeals = $dealsByStage[$stageKey] ?? [];
                    $colorClass = $stageColors[$stageKey] ?? 'bg-gray-100 dark:bg-gray-800 border-gray-300';
                    $currentIdx = array_search($stageKey, $stageKeys);
                    $nextStage  = ($currentIdx !== false && $currentIdx < count($stageKeys) - 1)
                                    ? $stageKeys[$currentIdx + 1]
                                    : null;
                    $nextLabel  = $nextStage ? ($this->getStages()[$nextStage] ?? '') : null;
                @endphp
                <div class="flex-shrink-0 w-72">
                    {{-- Column header --}}
                    <div class="flex items-center justify-between px-3 py-2 rounded-t-lg {{ $colorClass }} border">
                        <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $stageLabel }}</span>
                        <span class="text-xs text-gray-500 bg-white dark:bg-gray-700 rounded-full px-2 py-0.5">
                            {{ count($stageDeals) }}
                        </span>
                    </div>

                    {{-- Deal cards --}}
                    <div class="space-y-2 mt-2 min-h-20">
                        @forelse ($stageDeals as $deal)
                            <div class="rounded-lg bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <a href="{{ \App\Filament\Resources\DealResource::getUrl('view', ['record' => $deal['id']]) }}"
                                       class="font-medium text-sm text-gray-900 dark:text-white hover:text-primary-600 truncate flex-1">
                                        {{ $deal['title'] }}
                                    </a>
                                    <span class="shrink-0 text-xs text-gray-400 font-mono">{{ $deal['reference'] ?? '' }}</span>
                                </div>

                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-sm font-semibold text-primary-600">
                                        {{ $deal['currency'] ?? 'XAF' }} {{ number_format((float) ($deal['value'] ?? 0), 0) }}
                                    </span>
                                    @if (isset($deal['probability']) && $deal['probability'])
                                        <span class="text-xs text-gray-400">{{ $deal['probability'] }}%</span>
                                    @endif
                                </div>

                                @if (isset($deal['expected_close_date']) && $deal['expected_close_date'])
                                    <div class="mt-1 text-xs text-gray-400">
                                        Close: {{ \Carbon\Carbon::parse($deal['expected_close_date'])->format('d M Y') }}
                                    </div>
                                @endif

                                @if (isset($deal['owner']) && $deal['owner'])
                                    <div class="mt-1 text-xs text-gray-400 truncate">
                                        {{ $deal['owner']['name'] ?? '' }}
                                    </div>
                                @endif

                                {{-- Move forward button (skip for closed stages) --}}
                                @if ($nextStage && ! in_array($stageKey, ['closed_won', 'closed_lost']))
                                    <div class="mt-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                                        <button
                                            wire:click="advanceStage({{ $deal['id'] }})"
                                            class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                                            &rarr; Move to {{ $nextLabel }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center text-xs text-gray-400 py-6">No deals</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>

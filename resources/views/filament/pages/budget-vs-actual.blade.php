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

    {{ $this->table }}
</x-filament-panels::page>

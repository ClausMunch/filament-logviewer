<x-filament-panels::page>
    <div class="space-y-6">
        {{-- File Information Card --}}
        <x-filament::section>
            <x-slot name="heading">
                Log File Information
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">File Name</div>
                    <div class="mt-1 text-sm font-semibold">{{ $fileInfo['name'] ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">File Size</div>
                    <div class="mt-1 text-sm font-semibold">{{ $fileInfo['size'] ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Modified</div>
                    <div class="mt-1 text-sm font-semibold">{{ $fileInfo['modified'] ?? 'N/A' }}</div>
                </div>
            </div>
        </x-filament::section>

        {{-- Log Entries Table --}}
        <x-filament::section>
            <x-slot name="heading">
                Log Entries
            </x-slot>

            <x-slot name="description">
                View and filter log entries from this file. The table auto-refreshes every 30 seconds.
            </x-slot>

            {{ $this->table }}
        </x-filament::section>
    </div>
</x-filament-panels::page>

<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Market Analytics') }}</h1>
    </x-slot>

    <div class="py-6">
        @livewire('admin.market.analytics-panel', [
            'selectedLeague' => $selectedLeague ?? null,
            'currentSeason' => $currentSeason
        ])
    </div>
</x-admin-layout>
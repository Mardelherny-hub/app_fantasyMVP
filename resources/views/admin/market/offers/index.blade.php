<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Offers Management') }}</h1>
    </x-slot>

    <div class="py-8">
        @livewire('admin.market.offers-table')
    </div>
</x-admin-layout>
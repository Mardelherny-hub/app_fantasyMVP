<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Listings Management') }}</h1>
    </x-slot>

    <div class="py-8">
        @livewire('admin.market.listings-table')
    </div>
</x-admin-layout>
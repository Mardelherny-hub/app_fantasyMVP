<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Price Management') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Manage player market values') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        @livewire('admin.market.prices-manager')
    </div>
</x-admin-layout>
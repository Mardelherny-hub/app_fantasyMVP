<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Transfers History') }}</h1>
    </x-slot>

    <div class="py-8">
        @livewire('admin.market.transfers-table')
    </div>
</x-admin-layout>
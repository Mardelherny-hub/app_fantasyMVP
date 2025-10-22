<x-admin-layout>
    <x-slot name="header">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Moderation Panel') }}</h1>
            <p class="text-gray-600 mt-1">{{ __('Monitor and moderate market activity') }}</p>
        </div>
    </x-slot>

    <div class="py-6">
        @livewire('admin.market.moderation-panel')
    </div>
</x-admin-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Statistics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Breadcrumb --}}
            <div class="mb-6">
                <nav class="text-sm">
                    <a href="{{ route('manager.education.index') }}" class="text-emerald-600 hover:text-emerald-700">
                        {{ __('Education Hub') }}
                    </a>
                    <span class="text-gray-500 mx-2">/</span>
                    <span class="text-gray-600">{{ __('Statistics') }}</span>
                </nav>
            </div>

            {{-- Componente Livewire UserStats --}}
            <livewire:dashboard.education.user-stats />

        </div>
    </div>
</x-app-layout>
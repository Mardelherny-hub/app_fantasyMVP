<x-app-layout>
    <x-slot name="header">
        <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent font-black">
            {{ __('Global Ranking') }}
        </span>
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
                    <span class="text-gray-600">{{ __('Ranking') }}</span>
                </nav>
            </div>

            {{-- Componente Livewire Leaderboard --}}
            <livewire:dashboard.education.leaderboard />

        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent font-black">
            {{ __('Dashboard') }}
        </span>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <x-welcome />
            </div>
        </div>
    </div>
</x-app-layout>

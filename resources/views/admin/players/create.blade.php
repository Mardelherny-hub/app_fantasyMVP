<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Crear jugador') }}
                </h2>
            </div>
            <div>
                <a href="{{ route('admin.players.index', request()->route('locale')) }}"
                   class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    {{ __('Volver') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <form method="POST" action="{{ route('admin.players.store', request()->route('locale')) }}">
                    @csrf
                    @include('admin.players._form', ['submitLabel' => __('Crear jugador')])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

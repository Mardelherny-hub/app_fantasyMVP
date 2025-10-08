<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Crear rol') }}</h2>
            <a href="{{ route('admin.roles.index', app()->getLocale()) }}" class="text-sm text-gray-600 hover:underline">{{ __('Volver') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <form method="POST" action="{{ route('admin.roles.store', app()->getLocale()) }}">
                    @csrf
                    @include('admin.roles.partials.form', ['mode' => 'create'])
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar usuario') }}</h2>
            <a href="{{ route('admin.users.index', app()->getLocale()) }}" class="text-sm text-gray-600 hover:underline">{{ __('Volver') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <form method="POST" action="{{ route('admin.users.update', [app()->getLocale(), $user]) }}">
                    @csrf @method('PUT')
                    @include('admin.users.partials.form', ['mode' => 'edit'])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

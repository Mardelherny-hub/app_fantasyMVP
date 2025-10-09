<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Crear Temporada') }}</h2>
            <a href="{{ route('admin.seasons.index', app()->getLocale()) }}" class="text-sm text-gray-600 hover:underline">{{ __('Volver') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <form method="POST" action="{{ route('admin.seasons.store', app()->getLocale()) }}">
                    @csrf

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Nombre') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="Ej: 2025/26"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Código --}}
                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Código') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code') }}"
                               placeholder="Ej: 2025-26"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror"
                               required>
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('Código único para identificar la temporada.') }}</p>
                    </div>

                    {{-- Fecha de inicio --}}
                    <div class="mb-4">
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Fecha de inicio') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="starts_at" 
                               name="starts_at" 
                               value="{{ old('starts_at') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('starts_at') border-red-500 @enderror"
                               required>
                        @error('starts_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fecha de fin --}}
                    <div class="mb-4">
                        <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Fecha de fin') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="ends_at" 
                               name="ends_at" 
                               value="{{ old('ends_at') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('ends_at') border-red-500 @enderror"
                               required>
                        @error('ends_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado --}}
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Marcar como temporada activa') }}</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Solo puede haber una temporada activa a la vez.') }}</p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.seasons.index', app()->getLocale()) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ __('Crear Temporada') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
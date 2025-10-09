<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Equipo CPL') }}</h2>
            <a href="{{ route('admin.real-teams.index', app()->getLocale()) }}" class="text-sm text-gray-600 hover:underline">{{ __('Volver') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <form method="POST" action="{{ route('admin.real-teams.update', [app()->getLocale(), $realTeam]) }}">
                    @csrf
                    @method('PUT')

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Nombre del Equipo') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $realTeam->name) }}"
                               placeholder="Ej: Toronto FC"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre Corto --}}
                    <div class="mb-4">
                        <label for="short_name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Nombre Corto') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="short_name" 
                               name="short_name" 
                               value="{{ old('short_name', $realTeam->short_name) }}"
                               placeholder="Ej: TFC"
                               maxlength="10"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('short_name') border-red-500 @enderror"
                               required>
                        @error('short_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('Máximo 10 caracteres.') }}</p>
                    </div>

                    {{-- País --}}
                    <div class="mb-4">
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('País (ISO 3166-1 alpha-2)') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="country" 
                               name="country" 
                               value="{{ old('country', $realTeam->country) }}"
                               placeholder="CA"
                               maxlength="2"
                               pattern="[A-Z]{2}"
                               style="text-transform: uppercase;"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('country') border-red-500 @enderror"
                               required>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('Código de 2 letras en mayúsculas (ej: CA, US, MX).') }}</p>
                    </div>

                    {{-- Año de Fundación --}}
                    <div class="mb-4">
                        <label for="founded_year" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Año de Fundación') }}
                        </label>
                        <input type="number" 
                               id="founded_year" 
                               name="founded_year" 
                               value="{{ old('founded_year', $realTeam->founded_year) }}"
                               placeholder="2019"
                               min="1800"
                               max="{{ date('Y') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('founded_year') border-red-500 @enderror">
                        @error('founded_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Logo URL --}}
                    <div class="mb-6">
                        <label for="logo_url" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('URL del Logo') }}
                        </label>
                        <input type="url" 
                               id="logo_url" 
                               name="logo_url" 
                               value="{{ old('logo_url', $realTeam->logo_url) }}"
                               placeholder="https://ejemplo.com/logo.png"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('logo_url') border-red-500 @enderror">
                        @error('logo_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('URL completa del logo del equipo.') }}</p>
                        
                        @if($realTeam->logo_url)
                            <div class="mt-2">
                                <p class="text-xs text-gray-500 mb-1">{{ __('Logo actual:') }}</p>
                                <img src="{{ $realTeam->logo_url }}" alt="{{ $realTeam->name }}" class="w-20 h-20 object-contain border rounded">
                            </div>
                        @endif
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.real-teams.index', app()->getLocale()) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ __('Actualizar Equipo') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
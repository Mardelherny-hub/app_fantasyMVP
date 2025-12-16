<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Crear Jornada') }}</h2>
            <a href="{{ route('admin.fantasy.gameweeks.index', app()->getLocale()) }}" class="text-sm text-gray-600 hover:underline">{{ __('Volver') }}</a>
        </div> 
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                <form method="POST" action="{{ route('admin.fantasy.gameweeks.store', app()->getLocale()) }}">
                    @csrf

                    {{-- Temporada --}}
                    <div class="mb-4">
                        <label for="season_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Temporada') }} <span class="text-red-500">*</span>
                        </label>
                        <select id="season_id" 
                                name="season_id" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('season_id') border-red-500 @enderror"
                                required>
                            <option value="">{{ __('Seleccione una temporada') }}</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" 
                                        {{ old('season_id', $defaultSeasonId) == $season->id ? 'selected' : '' }}>
                                    {{ $season->name }} {{ $season->is_active ? '(Activa)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('season_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Número de Jornada --}}
                    <div class="mb-4">
                        <label for="number" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Número de Jornada') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="number" 
                               name="number" 
                               value="{{ old('number') }}"
                               placeholder="1"
                               min="1"
                               max="30"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('number') border-red-500 @enderror"
                               required>
                        @error('number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('Del 1 al 30 (27 regular + 3 playoffs).') }}</p>
                    </div>

                    {{-- Fecha de inicio --}}
                    <div class="mb-4">
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Fecha y hora de inicio') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
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
                            {{ __('Fecha y hora de fin') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               id="ends_at" 
                               name="ends_at" 
                               value="{{ old('ends_at') }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('ends_at') border-red-500 @enderror"
                               required>
                        @error('ends_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Es Playoff --}}
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_playoff" 
                                   value="1"
                                   {{ old('is_playoff') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   onchange="document.getElementById('playoff_round_section').classList.toggle('hidden', !this.checked)">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Es jornada de playoff') }}</span>
                        </label>
                    </div>

                    {{-- Ronda de Playoff (solo si is_playoff está marcado) --}}
                    <div id="playoff_round_section" class="mb-4 {{ old('is_playoff') ? '' : 'hidden' }}">
                        <label for="playoff_round" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Ronda de Playoff') }}
                        </label>
                        <select id="playoff_round" 
                                name="playoff_round" 
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('playoff_round') border-red-500 @enderror">
                            <option value="">{{ __('Seleccione una ronda') }}</option>
                            <option value="1" {{ old('playoff_round') == 1 ? 'selected' : '' }}>{{ __('Cuartos de Final') }}</option>
                            <option value="2" {{ old('playoff_round') == 2 ? 'selected' : '' }}>{{ __('Semifinales') }}</option>
                            <option value="3" {{ old('playoff_round') == 3 ? 'selected' : '' }}>{{ __('Final') }}</option>
                        </select>
                        @error('playoff_round')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado inicial --}}
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="is_closed" 
                                   value="1"
                                   {{ old('is_closed') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Crear jornada cerrada') }}</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500">{{ __('Las jornadas cerradas bloquean alineaciones y transferencias.') }}</p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.fantasy.gameweeks.index', app()->getLocale()) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ __('Crear Jornada') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
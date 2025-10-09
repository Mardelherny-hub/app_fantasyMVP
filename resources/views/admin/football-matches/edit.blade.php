<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Partido') }}</h2>
            <a href="{{ route('admin.football-matches.index', app()->getLocale()) }}" class="text-sm text-gray-600 hover:underline">{{ __('Volver') }}</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow">
                {{-- Preview del Partido --}}
                <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($footballMatch->homeTeam->logo_url)
                                <img src="{{ $footballMatch->homeTeam->logo_url }}" alt="{{ $footballMatch->homeTeam->name }}" class="w-10 h-10 object-contain">
                            @endif
                            <span class="font-bold text-gray-900">{{ $footballMatch->homeTeam->name }}</span>
                        </div>
                        
                        <div class="text-center px-6">
                            <div class="text-xs text-gray-600 mb-1">GW{{ $footballMatch->matchday }}</div>
                            @if($footballMatch->status >= 1)
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ $footballMatch->home_goals }} - {{ $footballMatch->away_goals }}
                                </div>
                            @else
                                <div class="text-lg text-gray-400">vs</div>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-gray-900">{{ $footballMatch->awayTeam->name }}</span>
                            @if($footballMatch->awayTeam->logo_url)
                                <img src="{{ $footballMatch->awayTeam->logo_url }}" alt="{{ $footballMatch->awayTeam->name }}" class="w-10 h-10 object-contain">
                            @endif
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.football-matches.update', [app()->getLocale(), $footballMatch]) }}">
                    @csrf
                    @method('PUT')

                    {{-- Temporada y Jornada --}}
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
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
                                            {{ old('season_id', $footballMatch->season_id) == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }} {{ $season->is_active ? '(Activa)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('season_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="matchday" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Jornada') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="matchday" 
                                   name="matchday" 
                                   value="{{ old('matchday', $footballMatch->matchday) }}"
                                   placeholder="1"
                                   min="1"
                                   max="30"
                                   class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('matchday') border-red-500 @enderror"
                                   required>
                            @error('matchday')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">{{ __('Jornada del 1 al 30') }}</p>
                        </div>
                    </div>

                    {{-- Equipos --}}
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Equipos Enfrentados') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Equipo Local --}}
                            <div>
                                <label for="home_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Equipo Local') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="home_team_id" 
                                        name="home_team_id" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('home_team_id') border-red-500 @enderror"
                                        required>
                                    <option value="">{{ __('Seleccione equipo local') }}</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('home_team_id', $footballMatch->home_team_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }} ({{ $team->short_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('home_team_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Equipo Visitante --}}
                            <div>
                                <label for="away_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Equipo Visitante') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="away_team_id" 
                                        name="away_team_id" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('away_team_id') border-red-500 @enderror"
                                        required>
                                    <option value="">{{ __('Seleccione equipo visitante') }}</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('away_team_id', $footballMatch->away_team_id) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }} ({{ $team->short_name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('away_team_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Fecha y Hora --}}
                    <div class="mb-4">
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Fecha y Hora del Partido') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" 
                               id="starts_at" 
                               name="starts_at" 
                               value="{{ old('starts_at', $footballMatch->starts_at->format('Y-m-d\TH:i')) }}"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('starts_at') border-red-500 @enderror"
                               required>
                        @error('starts_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado y Marcador --}}
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">{{ __('Estado y Resultado') }}</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Estado --}}
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Estado') }}
                                </label>
                                <select id="status" 
                                        name="status" 
                                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                        onchange="document.getElementById('goals_section').classList.toggle('hidden', this.value == '0')">
                                    <option value="0" {{ old('status', $footballMatch->status) == '0' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
                                    <option value="1" {{ old('status', $footballMatch->status) == '1' ? 'selected' : '' }}>{{ __('En Vivo') }}</option>
                                    <option value="2" {{ old('status', $footballMatch->status) == '2' ? 'selected' : '' }}>{{ __('Finalizado') }}</option>
                                    <option value="3" {{ old('status', $footballMatch->status) == '3' ? 'selected' : '' }}>{{ __('Pospuesto') }}</option>
                                </select>
                            </div>

                            {{-- Goles --}}
                            <div id="goals_section" class="col-span-2 grid grid-cols-2 gap-4 {{ old('status', $footballMatch->status) == '0' ? 'hidden' : '' }}">
                                <div>
                                    <label for="home_goals" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Goles Local') }}
                                    </label>
                                    <input type="number" 
                                           id="home_goals" 
                                           name="home_goals" 
                                           value="{{ old('home_goals', $footballMatch->home_goals) }}"
                                           min="0"
                                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="away_goals" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Goles Visitante') }}
                                    </label>
                                    <input type="number" 
                                           id="away_goals" 
                                           name="away_goals" 
                                           value="{{ old('away_goals', $footballMatch->away_goals) }}"
                                           min="0"
                                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('admin.football-matches.index', app()->getLocale()) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            {{ __('Actualizar Partido') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
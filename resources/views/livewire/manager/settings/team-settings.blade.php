<div>
    {{-- Header --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-100">
                {{ __('Configuración') }}
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Gestiona la configuración de tu equipo y preferencias') }}
            </p>
        </div>
    </div>

    {{-- Messages --}}
    @if($successMessage)
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-green-800">{{ $successMessage }}</span>
            </div>
        </div>
    @endif

    @if($errorMessage)
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-red-800">{{ $errorMessage }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        {{-- Configuración del Equipo --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    {{ __('Mi Equipo') }}
                </h2>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="saveTeamSettings">
                    {{-- Nombre del Equipo --}}
                    <div class="mb-6">
                        <label for="teamName" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Nombre del Equipo') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="teamName" 
                               wire:model="teamName" 
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="{{ __('Ej: Los Campeones') }}"
                               maxlength="50">
                        @error('teamName')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('Máximo 50 caracteres') }}</p>
                    </div>

                    {{-- Eslogan --}}
                    <div class="mb-6">
                        <label for="teamSlogan" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Eslogan / Descripción') }}
                        </label>
                        <input type="text" 
                               id="teamSlogan" 
                               wire:model="teamSlogan" 
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="{{ __('Ej: Siempre ganadores') }}"
                               maxlength="100">
                        @error('teamSlogan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ __('Opcional - Máximo 100 caracteres') }}</p>
                    </div>

                    {{-- Botón Guardar --}}
                    <div class="flex items-center justify-end gap-3">
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <span wire:loading.remove wire:target="saveTeamSettings">{{ __('Guardar Cambios') }}</span>
                            <span wire:loading wire:target="saveTeamSettings" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Guardando...') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Información de la Liga --}}
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    {{ __('Mi Liga') }}
                </h2>
            </div>
            
            <div class="p-6">
                <div class="space-y-4">
                    {{-- Nombre de la Liga --}}
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-700">{{ __('Nombre') }}</span>
                        <span class="text-sm text-gray-900 font-semibold">{{ $league->name }}</span>
                    </div>

                    {{-- Temporada --}}
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-700">{{ __('Temporada') }}</span>
                        <span class="text-sm text-gray-900">{{ $league->season->name }}</span>
                    </div>

                    {{-- Tipo de Liga --}}
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-700">{{ __('Tipo') }}</span>
                        <span class="text-sm">
                            @if($this->isPrivateLeague())
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold">
                                    🔒 {{ __('Privada') }}
                                </span>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                    🌐 {{ __('Pública') }}
                                </span>
                            @endif
                        </span>
                    </div>

                    {{-- Código de Invitación (si es privada) --}}
                    @if($this->isPrivateLeague() && $this->getLeagueCode())
                        <div class="flex justify-between items-center py-3">
                            <span class="text-sm font-medium text-gray-700">{{ __('Código de Invitación') }}</span>
                            <div class="flex items-center gap-2">
                                <code class="px-3 py-1 bg-gray-100 text-gray-900 rounded font-mono text-sm">
                                    {{ $this->getLeagueCode() }}
                                </code>
                                <button type="button"
                                        onclick="navigator.clipboard.writeText('{{ $this->getLeagueCode() }}'); alert('{{ __('Código copiado al portapapeles') }}')"
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition text-sm font-medium">
                                    {{ __('Copiar') }}
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Equipos en la Liga --}}
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-700">{{ __('Equipos') }}</span>
                        <span class="text-sm text-gray-900">
                            {{ \App\Models\FantasyTeam::where('league_id', $league->id)->count() }}
                        </span>
                    </div>

                    {{-- Mi Posición --}}
                    <div class="flex justify-between items-center py-3">
                        <span class="text-sm font-medium text-gray-700">{{ __('Mi Posición') }}</span>
                        <span class="text-lg font-bold text-blue-600">
                            {{ \App\Models\FantasyTeam::where('league_id', $league->id)->where('total_points', '>', $team->total_points)->count() + 1 }}°
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Estadísticas Rápidas --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100">
                <h2 class="text-lg font-bold text-gray-900 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('Resumen Rápido') }}
                </h2>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $team->total_points }}</div>
                        <div class="text-xs text-gray-600 mt-1">{{ __('Puntos Totales') }}</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $team->budget }}</div>
                        <div class="text-xs text-gray-600 mt-1">{{ __('Presupuesto') }}</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">
                            {{ \App\Models\FantasyRoster::where('fantasy_team_id', $team->id)->distinct('player_id')->count('player_id') }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">{{ __('Jugadores') }}</div>
                    </div>
                    
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">
                            {{ \App\Models\Fixture::where(function($q) {
                                $q->where('home_fantasy_team_id', $this->team->id)
                                  ->orWhere('away_fantasy_team_id', $this->team->id);
                            })->where('status', 1)->count() }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">{{ __('Partidos Jugados') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div>
    {{-- Header --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ __('Clasificación') }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ $league->name }}
                    </p>
                </div>

                {{-- Selector de Gameweek --}}
                @if($gameweeks->count() > 0)
                    <div class="flex items-center gap-2">
                        <select wire:model.live="selectedGameweekId" 
                                wire:change="$selectedGameweekId ? loadGameweekStandings($event.target.value) : viewCurrent()"
                                class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Clasificación Actual') }}</option>
                            @foreach($gameweeks as $gw)
                                <option value="{{ $gw->id }}">
                                    {{ __('Después de GW') }} {{ $gw->number }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Mi Posición Summary --}}
    @if($myStanding)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-6 text-white">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-center md:text-left">
                        <div class="text-sm font-medium opacity-90 mb-1">{{ __('Tu Posición') }}</div>
                        <div class="text-5xl font-bold">{{ $myStanding->position }}°</div>
                        <div class="text-sm opacity-90 mt-1">{{ __('de') }} {{ $totalTeams }} {{ __('equipos') }}</div>
                    </div>

                    <div class="flex gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $myStanding->points }}</div>
                            <div class="text-sm opacity-90">{{ __('Puntos Liga') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $myStanding->fantasy_points }}</div>
                            <div class="text-sm opacity-90">{{ __('Puntos Fantasy') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold">{{ $myStanding->played }}</div>
                            <div class="text-sm opacity-90">{{ __('Jugados') }}</div>
                        </div>
                    </div>

                    @if($inPlayoffZone)
                        <div class="text-center px-6 py-3 bg-green-500 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🏆</span>
                                <div class="text-left">
                                    <div class="font-bold">{{ __('Zona de Playoffs') }}</div>
                                    <div class="text-xs opacity-90">Top {{ $playoffSpots }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center px-6 py-3 bg-yellow-500 rounded-lg">
                            <div class="text-sm font-medium">{{ __('Fuera de Playoffs') }}</div>
                            <div class="text-xs opacity-90">
                                {{ __('Necesitas alcanzar el top') }} {{ $playoffSpots }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Tabla de Clasificación --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            {{-- Header de la tabla --}}
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">
                        @if($viewMode === 'current')
                            {{ __('Tabla de Posiciones Actual') }}
                        @else
                            {{ __('Posiciones después de GW') }} {{ $selectedGameweek->number }}
                        @endif
                    </h2>
                    
                    @if($viewMode === 'gameweek')
                        <button wire:click="viewCurrent" 
                                class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            ← {{ __('Ver tabla actual') }}
                        </button>
                    @endif
                </div>
            </div>

            {{-- Loading State --}}
            @if($loading)
                <div class="p-12 text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="mt-4 text-gray-600">{{ __('Cargando clasificación...') }}</p>
                </div>
            @else
                {{-- Leyenda de Playoffs --}}
                @if($playoffSpots > 0)
                    <div class="px-6 py-3 bg-green-50 border-b border-green-100">
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-4 h-4 bg-green-500 rounded"></div>
                            <span class="text-green-800 font-medium">
                                🏆 {{ __('Clasificados a Playoffs') }} (Top {{ $playoffSpots }})
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Tabla --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Pos') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Equipo') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('PJ') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('G') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('E') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('P') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('GF') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('GC') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('DG') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Pts Liga') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Pts Fantasy') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($standings as $standing)
                                <tr class="hover:bg-gray-50 transition {{ $this->getRowClass($standing->position, $standing->fantasy_team_id) }}">
                                    {{-- Posición --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ $standing->position }}
                                            </span>
                                            @if($standing->position <= $playoffSpots)
                                                <span class="text-green-600" title="{{ __('Clasificado a Playoffs') }}">
                                                    🏆
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Equipo --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="font-medium text-gray-900 flex items-center gap-2">
                                                    {{ $standing->fantasyTeam->name }}
                                                    @if($standing->fantasy_team_id === $team->id)
                                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded">
                                                            {{ __('TÚ') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $standing->fantasyTeam->user->name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Partidos Jugados --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $standing->played }}
                                    </td>

                                    {{-- Ganados --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="font-medium text-green-600">{{ $standing->won }}</span>
                                    </td>

                                    {{-- Empatados --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="font-medium text-yellow-600">{{ $standing->drawn }}</span>
                                    </td>

                                    {{-- Perdidos --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="font-medium text-red-600">{{ $standing->lost }}</span>
                                    </td>

                                    {{-- Goles a Favor --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $standing->goals_for }}
                                    </td>

                                    {{-- Goles en Contra --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        {{ $standing->goals_against }}
                                    </td>

                                    {{-- Diferencia de Goles --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        <span class="font-medium {{ $standing->goal_difference > 0 ? 'text-green-600' : ($standing->goal_difference < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                            {{ $standing->goal_difference > 0 ? '+' : '' }}{{ $standing->goal_difference }}
                                        </span>
                                    </td>

                                    {{-- Puntos de Liga --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-lg font-bold text-blue-600">
                                            {{ $standing->points }}
                                        </span>
                                    </td>

                                    {{-- Puntos Fantasy --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                        {{ $standing->fantasy_points }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Información adicional --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">{{ __('Sistema de Puntos:') }}</span>
                            {{ __('Victoria') }} = 3pts, {{ __('Empate') }} = 1pt, {{ __('Derrota') }} = 0pts
                        </div>
                        <div>
                            <span class="font-medium">{{ __('Criterios de Desempate:') }}</span>
                            1) {{ __('Puntos') }}, 2) {{ __('Diferencia de Goles') }}, 3) {{ __('Goles a Favor') }}, 4) {{ __('Puntos Fantasy') }}
                        </div>
                        <div>
                            <span class="font-medium">{{ __('Playoffs:') }}</span>
                            {{ __('Clasifican los primeros') }} {{ $playoffSpots }} {{ __('equipos') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Estadísticas Adicionales --}}
    @if(!$loading && $standings->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Máximo Goleador --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        ⚽ {{ __('Máximo Goleador') }}
                    </h3>
                    @php
                        $topScorer = $standings->sortByDesc('goals_for')->first();
                    @endphp
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $topScorer->goals_for }}</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $topScorer->fantasyTeam->name }}</div>
                    </div>
                </div>

                {{-- Mejor Defensa --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        🛡️ {{ __('Mejor Defensa') }}
                    </h3>
                    @php
                        $bestDefense = $standings->sortBy('goals_against')->first();
                    @endphp
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">{{ $bestDefense->goals_against }}</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $bestDefense->fantasyTeam->name }}</div>
                        <div class="text-xs text-gray-500">{{ __('goles recibidos') }}</div>
                    </div>
                </div>

                {{-- Máximos Puntos Fantasy --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        ⭐ {{ __('Máximos Pts Fantasy') }}
                    </h3>
                    @php
                        $topFantasy = $standings->sortByDesc('fantasy_points')->first();
                    @endphp
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">{{ $topFantasy->fantasy_points }}</div>
                        <div class="text-sm text-gray-600 mt-1">{{ $topFantasy->fantasyTeam->name }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
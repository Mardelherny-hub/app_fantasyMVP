<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $league->name }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Código') }}: <code class="bg-gray-100 px-2 py-0.5 rounded">{{ $league->code }}</code></p>
            </div>
            <div class="flex gap-2">
                @if($stats['available_slots'] > 0 && $league->auto_fill_bots)
                    <form method="POST" 
                          action="{{ route('admin.leagues.fill-bots', [app()->getLocale(), $league]) }}"
                          onsubmit="return confirm('{{ __('¿Crear :count equipos bot?', ['count' => $stats['available_slots']]) }}')">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Completar con bots') }} ({{ $stats['available_slots'] }})
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.leagues.edit', [app()->getLocale(), $league]) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    {{ __('Editar') }}
                </a>
                <a href="{{ route('admin.leagues.index', app()->getLocale()) }}" 
                   class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    {{ __('Volver') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Información General --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Información General') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Propietario') }}</p>
                        <p class="font-medium">{{ $league->owner->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Temporada') }}</p>
                        <p class="font-medium">{{ $league->season->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Tipo') }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $league->type == 1 ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                            {{ $league->type == 1 ? __('Privada') : __('Pública') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Estado') }}</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $league->is_locked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $league->is_locked ? __('Cerrada') : __('Abierta') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Idioma') }}</p>
                        <p class="font-medium">{{ strtoupper($league->locale) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Auto-completar con bots') }}</p>
                        <p class="font-medium">{{ $league->auto_fill_bots ? __('Sí') : __('No') }}</p>
                    </div>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow p-4">
                    <p class="text-sm text-gray-600">{{ __('Equipos Totales') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_teams'] }} / {{ $league->max_participants }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['available_slots'] }} {{ __('cupos disponibles') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4">
                    <p class="text-sm text-gray-600">{{ __('Equipos Usuario') }}</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['user_teams'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4">
                    <p class="text-sm text-gray-600">{{ __('Equipos Bot') }}</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['bot_teams'] }}</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4">
                    <p class="text-sm text-gray-600">{{ __('Partidos') }}</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_fixtures'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['finished_fixtures'] }} {{ __('finalizados') }}</p>
                </div>
            </div>

            {{-- Configuración de Playoffs --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Configuración de Playoffs') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Equipos clasifican') }}</p>
                        <p class="font-medium">{{ $league->playoff_teams }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Formato') }}</p>
                        <p class="font-medium">{{ $league->playoff_format == 1 ? 'Page Playoff' : 'Standard' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Semanas regulares') }}</p>
                        <p class="font-medium">{{ $league->regular_season_gameweeks }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Semanas totales') }}</p>
                        <p class="font-medium">{{ $league->total_gameweeks }}</p>
                    </div>
                </div>
            </div>

            {{-- Equipos Fantasy --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Equipos Fantasy') }}</h3>
                @if($league->fantasyTeams->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('No hay equipos registrados aún.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Equipo') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Usuario') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Puntos') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Presupuesto') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tipo') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($league->fantasyTeams as $team)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $team->name }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $team->user->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $team->total_points }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">${{ number_format($team->budget, 2) }}</td>
                                        <td class="px-4 py-2">
                                            @if($team->is_bot)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    BOT
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ __('Usuario') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            {{-- Miembros de la Liga --}}
            <div class="bg-white rounded-xl shadow p-6">
                

                

                {{-- Equipos de la Liga --}}
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Equipos de la Liga') }}</h3>
                    
                    {{-- Botón agregar equipo --}}
@if($availableTeams->where('league_id', null)->isNotEmpty() && $stats['available_slots'] > 0)
                        <button onclick="document.getElementById('addTeamForm').classList.toggle('hidden')" 
                                class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                            {{ __('+ Agregar Equipo') }}
                        </button>
                    @endif
                </div>

                {{-- Formulario agregar equipo (oculto por defecto) --}}
@if($availableTeams->where('league_id', null)->isNotEmpty() && $stats['available_slots'] > 0)                    <div id="addTeamForm" class="hidden mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <form method="POST" action="{{ route('admin.leagues.add-team', [app()->getLocale(), $league]) }}" class="flex gap-3 items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Seleccionar Equipo') }}</label>
                                <select name="fantasy_team_id" required class="w-full rounded-lg border-gray-300">
                                    <option value="">{{ __('Elegir equipo disponible...') }}</option>
                                    @foreach($availableTeams->whereNull('league_id') as $team)
                                        <option value="{{ $team->id }}">
                                            {{ $team->name }} - {{ $team->user->name ?? 'Sin dueño' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                {{ __('Agregar') }}
                            </button>
                            <button type="button" onclick="document.getElementById('addTeamForm').classList.add('hidden')" 
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                {{ __('Cancelar') }}
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Lista de equipos --}}
                @if($league->fantasyTeams->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('No hay equipos en esta liga aún.') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Equipo') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Manager') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Puntos') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Presupuesto') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tipo') }}</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($league->fantasyTeams as $team)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                            {{ $team->name }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-600">
                                            {{ $team->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">{{ $team->total_points }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-900">${{ number_format($team->budget, 2) }}</td>
                                        <td class="px-4 py-2">
                                            @if($team->is_bot)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                    BOT
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ __('Usuario') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            @if(!$team->is_bot)
                                                <form method="POST" action="{{ route('admin.leagues.remove-team', [app()->getLocale(), $league, $team->id]) }}" 
                                                      class="inline"
                                                      onsubmit="return confirm('{{ __('¿Remover este equipo de la liga?') }}')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs px-2 py-1 rounded bg-red-100 text-red-700 hover:bg-red-200">
                                                        {{ __('Remover') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
            </div>

        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.real-competitions.index', ['locale' => app()->getLocale()]) }}" 
                       class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $realCompetition->name }}</h1>
                        <p class="text-sm text-gray-600">{{ __('Detalle de la competición') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.real-competitions.edit', ['locale' => app()->getLocale(), 'realCompetition' => $realCompetition]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Editar') }}
                    </a>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ __('Total Fixtures') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_fixtures']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ __('Partidos Jugados') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['played_matches']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ __('Programados') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['scheduled_fixtures']) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">{{ __('Equipos') }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['teams_count']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Competition Info --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Información General') }}</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('ID Externo') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realCompetition->external_id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('País') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realCompetition->country ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Tipo') }}</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $realCompetition->type === 'league' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $realCompetition->type === 'league' ? __('Liga') : __('Copa') }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Estado') }}</dt>
                            <dd class="mt-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $realCompetition->active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $realCompetition->active ? __('Activo') : __('Inactivo') }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Fuente') }}</dt>
                            <dd class="mt-1">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $realCompetition->external_source }}</code>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Creado') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realCompetition->created_at->format('d/m/Y H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Actualizado') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $realCompetition->updated_at->format('d/m/Y H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Recent Fixtures --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">

                    {{-- Teams Section --}}
                    <div class="col-span-full bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Equipos Participantes') }}</h3>
                            <a href="{{ route('admin.real-competitions.teams.create', ['locale' => app()->getLocale(), 'realCompetition' => $realCompetition]) }}" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Agregar Equipos') }}
                            </a>
                        </div>

                        @php
                            $teamSeasons = $realCompetition->teamSeasons()->with(['team', 'season'])->orderBy('season_id', 'desc')->get()->groupBy('season_id');
                        @endphp

                        @if($teamSeasons->count() > 0)
                            @foreach($teamSeasons as $seasonId => $teams)
                                @php
                                    $season = $teams->first()->season;
                                @endphp
                                <div class="mb-6 last:mb-0">
                                    <div class="bg-gray-50 px-4 py-2 rounded-t-lg border-b border-gray-200">
                                        <h4 class="font-medium text-gray-900">{{ $season->name ?? __('Temporada') }} ({{ $teams->count() }} {{ __('equipos') }})</h4>
                                    </div>
                                    <div class="divide-y divide-gray-200">
                                        @foreach($teams as $teamSeason)
                                            <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $teamSeason->team->name }}</p>
                                                        <p class="text-sm text-gray-500">{{ $teamSeason->team->country ?? '-' }}</p>
                                                    </div>
                                                </div>
                                                <form action="{{ route('admin.real-competitions.teams.destroy', ['locale' => app()->getLocale(), 'realCompetition' => $realCompetition, 'realTeam' => $teamSeason->team]) }}" 
                                                    method="POST" 
                                                    class="inline"
                                                    onsubmit="return confirm('{{ __('¿Está seguro de quitar este equipo de la competición?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="season_id" value="{{ $seasonId }}">
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-800 p-2 rounded hover:bg-red-50 transition-colors"
                                                            title="{{ __('Quitar equipo') }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p>{{ __('No hay equipos asignados a esta competición') }}</p>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Fixtures Section --}}
                    <div class="col-span-full bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Últimos Fixtures') }}</h3>                    
                        @if($realCompetition->fixtures->count() > 0)
                            <div class="space-y-3">
                                @foreach($realCompetition->fixtures as $fixture)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 text-sm">
                                                <span class="font-medium text-gray-900">{{ $fixture->homeTeam->name ?? 'TBD' }}</span>
                                                <span class="text-gray-500">vs</span>
                                                <span class="font-medium text-gray-900">{{ $fixture->awayTeam->name ?? 'TBD' }}</span>
                                            </div>
                                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500">
                                                <span>{{ __('Ronda') }} {{ $fixture->round }}</span>
                                                @if($fixture->match_date_utc)
                                                    <span>{{ $fixture->match_date_utc->format('d/m/Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($fixture->match)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    {{ $fixture->match->home_score }} - {{ $fixture->match->away_score }}
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    {{ __('Programado') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm">{{ __('No hay fixtures registrados') }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
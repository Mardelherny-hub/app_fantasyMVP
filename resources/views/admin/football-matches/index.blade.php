<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Partidos CPL') }}</h2>
            <a href="{{ route('admin.football-matches.create', app()->getLocale()) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                {{ __('Crear Partido') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" class="mb-6 bg-white p-4 rounded-xl shadow">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Temporada') }}</label>
                        <select name="season_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>
                                    {{ $season->name }} {{ $season->is_active ? '⭐' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Jornada') }}</label>
                        <input type="number" name="matchday" value="{{ request('matchday') }}" 
                               placeholder="1-30" min="1" max="30"
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
                        <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Todos') }}</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Pendiente') }}</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('En Vivo') }}</option>
                            <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>{{ __('Finalizado') }}</option>
                            <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>{{ __('Pospuesto') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Equipo') }}</label>
                        <select name="team_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                    {{ $team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                            {{ __('Filtrar') }}
                        </button>
                    </div>
                </div>
            </form>

            {{-- Lista de Partidos --}}
            <div class="space-y-4">
                @forelse($matches as $match)
                    <div class="bg-white rounded-xl shadow hover:shadow-md transition p-4">
                        <div class="flex items-center justify-between">
                            {{-- Info del Partido --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-2">
                                    {{-- Temporada y Jornada --}}
                                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        {{ $match->season->name }} - GW{{ $match->matchday }}
                                    </span>
                                    
                                    {{-- Estado del Partido --}}
                                    @if($match->status === 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('Pendiente') }}
                                        </span>
                                    @elseif($match->status === 1)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 animate-pulse">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-1.5 animate-ping"></span>
                                            {{ __('EN VIVO') }}
                                        </span>
                                    @elseif($match->status === 2)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ __('Finalizado') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ __('Pospuesto') }}
                                        </span>
                                    @endif

                                    {{-- Fecha y hora --}}
                                    <span class="text-xs text-gray-500">
                                        {{ $match->starts_at->format('d/m/Y H:i') }}
                                    </span>
                                </div>

                                {{-- Equipos y Marcador --}}
                                <div class="flex items-center gap-4">
                                    {{-- Equipo Local --}}
                                    <div class="flex items-center gap-2 flex-1">
                                        @if($match->homeTeam->logo_url)
                                            <img src="{{ $match->homeTeam->logo_url }}" alt="{{ $match->homeTeam->name }}" class="w-8 h-8 object-contain">
                                        @endif
                                        <span class="font-semibold text-gray-900">{{ $match->homeTeam->name }}</span>
                                    </div>

                                    {{-- Marcador --}}
                                    <div class="px-6 py-2 bg-gray-50 rounded-lg min-w-[100px] text-center">
                                        @if($match->status >= 1)
                                            <span class="text-2xl font-bold text-gray-900">
                                                {{ $match->home_goals }} - {{ $match->away_goals }}
                                            </span>
                                        @else
                                            <span class="text-lg text-gray-400">vs</span>
                                        @endif
                                    </div>

                                    {{-- Equipo Visitante --}}
                                    <div class="flex items-center gap-2 flex-1 justify-end">
                                        <span class="font-semibold text-gray-900">{{ $match->awayTeam->name }}</span>
                                        @if($match->awayTeam->logo_url)
                                            <img src="{{ $match->awayTeam->logo_url }}" alt="{{ $match->awayTeam->name }}" class="w-8 h-8 object-contain">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Acciones --}}
                            <div class="flex items-center gap-2 ml-4">
                                {{-- Cambiar Estado Rápido --}}
                                @if($match->status === 0)
                                    <form method="POST" action="{{ route('admin.football-matches.update-status', [app()->getLocale(), $match]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="1">
                                        <button type="submit" title="{{ __('Marcar como En Vivo') }}"
                                                class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                            ▶ {{ __('Iniciar') }}
                                        </button>
                                    </form>
                                @elseif($match->status === 1)
                                    <form method="POST" action="{{ route('admin.football-matches.update-status', [app()->getLocale(), $match]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="2">
                                        <button type="submit" title="{{ __('Marcar como Finalizado') }}"
                                                class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition">
                                            ⏹ {{ __('Finalizar') }}
                                        </button>
                                    </form>
                                @endif

                                {{-- Editar --}}
                                <a href="{{ route('admin.football-matches.edit', [app()->getLocale(), $match]) }}" 
                                   class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                    {{ __('Editar') }}
                                </a>
                                
                                {{-- Eliminar --}}
                                <form method="POST" 
                                      action="{{ route('admin.football-matches.destroy', [app()->getLocale(), $match]) }}" 
                                      class="inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este partido?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition">
                                        {{ __('Eliminar') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">{{ __('No hay partidos creados.') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Paginación --}}
            <div class="mt-6">
                {{ $matches->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
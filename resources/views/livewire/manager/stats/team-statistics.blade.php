<div>
    {{-- Header --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-100">
                {{ __('Estadísticas') }}
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                {{ $team->name }} - {{ $league->name }}
            </p>
        </div>
    </div>

    {{-- Loading State --}}
    @if($loading)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600">{{ __('Cargando estadísticas...') }}</p>
            </div>
        </div>
    @else
        {{-- Tabs --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="bg-white rounded-lg shadow">
                <nav class="flex border-b border-gray-200">
                    <button wire:click="selectTab('overview')"
                            class="px-6 py-3 text-sm font-medium {{ $selectedTab === 'overview' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('Resumen') }}
                    </button>
                    <button wire:click="selectTab('players')"
                            class="px-6 py-3 text-sm font-medium {{ $selectedTab === 'players' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('Jugadores') }}
                    </button>
                    <button wire:click="selectTab('captains')"
                            class="px-6 py-3 text-sm font-medium {{ $selectedTab === 'captains' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('Capitanes') }}
                    </button>
                    <button wire:click="selectTab('comparison')"
                            class="px-6 py-3 text-sm font-medium {{ $selectedTab === 'comparison' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-900' }}">
                        {{ __('Comparativa') }}
                    </button>
                </nav>
            </div>
        </div>

        {{-- Tab Content --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            {{-- TAB: RESUMEN --}}
            @if($selectedTab === 'overview')
                {{-- Estadísticas Generales --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Puntos Totales') }}</div>
                        <div class="text-4xl font-bold text-blue-600">{{ $totalPoints }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Promedio por GW') }}</div>
                        <div class="text-4xl font-bold text-gray-900">{{ $averagePoints }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Mejor GW') }}</div>
                        <div class="text-4xl font-bold text-green-600">{{ $bestGameweekPoints }}</div>
                        @if($bestGameweekNumber)
                            <div class="text-xs text-gray-500 mt-1">GW {{ $bestGameweekNumber }}</div>
                        @endif
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Peor GW') }}</div>
                        <div class="text-4xl font-bold text-red-600">{{ $worstGameweekPoints }}</div>
                        @if($worstGameweekNumber)
                            <div class="text-xs text-gray-500 mt-1">GW {{ $worstGameweekNumber }}</div>
                        @endif
                    </div>
                </div>

                {{-- Estadísticas por Posición --}}
                <div class="bg-white rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Rendimiento por Posición') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach($statsByPosition as $position => $stats)
                                <div class="text-center">
                                    <div class="inline-flex px-4 py-2 rounded-lg mb-3 {{ $this->getPositionColor($position) }}">
                                        <span class="text-2xl font-bold">{{ $position }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        <div>
                                            <div class="text-2xl font-bold text-gray-900">{{ $stats['total_points'] }}</div>
                                            <div class="text-xs text-gray-600">{{ __('Puntos Totales') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-lg font-semibold text-gray-700">{{ $stats['avg_points'] }}</div>
                                            <div class="text-xs text-gray-600">{{ __('Promedio') }}</div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-gray-600">{{ $stats['appearances'] }} {{ __('apariciones') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- TAB: JUGADORES --}}
            @if($selectedTab === 'players')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Top Scorers --}}
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                ⭐ {{ __('Top Goleadores') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($topScorers as $index => $scorer)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $scorer->player->known_as }}</div>
                                                <div class="text-xs text-gray-500">{{ $scorer->appearances }} {{ __('apariciones') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-600">{{ $scorer->total_points }}</div>
                                            <div class="text-xs text-gray-500">{{ __('pts') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Jugadores Más Usados --}}
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-purple-100">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                                👥 {{ __('Más Utilizados') }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                @foreach($mostUsedPlayers as $index => $player)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $player->player->known_as }}</div>
                                                <div class="text-xs text-gray-500">{{ $player->total_points }} {{ __('pts totales') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-purple-600">{{ $player->times_starter }}</div>
                                            <div class="text-xs text-gray-500">{{ __('veces') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- TAB: CAPITANES --}}
            @if($selectedTab === 'captains')
                {{-- Estadísticas de Capitanes --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Puntos Extra (Bonus)') }}</div>
                        <div class="text-4xl font-bold text-yellow-600">{{ $captainStats['total_bonus'] }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Capitanes Efectivos') }}</div>
                        <div class="text-4xl font-bold text-green-600">{{ $captainStats['captain_appearances'] }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Vice Activado') }}</div>
                        <div class="text-4xl font-bold text-gray-600">{{ $captainStats['vice_replacements'] }}</div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-sm text-gray-600 mb-2">{{ __('Promedio Capitán') }}</div>
                        <div class="text-4xl font-bold text-blue-600">{{ $captainStats['avg_captain_points'] }}</div>
                    </div>
                </div>

                {{-- Top Capitanes --}}
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-yellow-100">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center">
                            👑 {{ __('Mejores Capitanes') }}
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($topCaptains as $index => $captain)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold text-lg">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 text-lg">{{ $captain->player->known_as }}</div>
                                            <div class="text-sm text-gray-600">
                                                {{ __('Capitán') }} {{ $captain->times_captain }} {{ __('veces') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold text-yellow-600">{{ $captain->total_base_points }}</div>
                                        <div class="text-xs text-gray-500">{{ __('pts base') }}</div>
                                        <div class="text-sm text-green-600 font-medium mt-1">
                                            +{{ $captain->total_base_points }} {{ __('bonus') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- TAB: COMPARATIVA --}}
            @if($selectedTab === 'comparison')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Mi Posición --}}
                    <div class="bg-white rounded-lg shadow p-8 text-center">
                        <div class="text-sm text-gray-600 mb-4">{{ __('Tu Posición en la Liga') }}</div>
                        <div class="text-6xl font-bold text-blue-600 mb-2">{{ $leaguePosition }}°</div>
                        <div class="text-gray-600">{{ __('de') }} {{ \App\Models\FantasyTeam::where('league_id', $league->id)->count() }} {{ __('equipos') }}</div>
                    </div>

                    {{-- Comparativa con Promedio --}}
                    <div class="bg-white rounded-lg shadow p-8">
                        <div class="text-sm text-gray-600 mb-4 text-center">{{ __('Comparativa con la Liga') }}</div>
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('Mis Puntos') }}</span>
                                <span class="text-2xl font-bold text-blue-600">{{ $totalPoints }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700">{{ __('Promedio Liga') }}</span>
                                <span class="text-2xl font-bold text-gray-600">{{ $leagueAverage }}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">{{ __('Diferencia') }}</span>
                                    <span class="text-3xl font-bold {{ $pointsAboveAverage >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $pointsAboveAverage >= 0 ? '+' : '' }}{{ $pointsAboveAverage }}
                                    </span>
                                </div>
                                <div class="text-center mt-2 text-sm text-gray-600">
                                    @if($pointsAboveAverage > 0)
                                        {{ __('Por encima del promedio') }} 📈
                                    @elseif($pointsAboveAverage < 0)
                                        {{ __('Por debajo del promedio') }} 📉
                                    @else
                                        {{ __('En el promedio') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Gráfico de Distribución --}}
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Tu Rendimiento vs Liga') }}</h3>
                    <div class="text-center py-8 text-gray-500">
                        {{ __('Gráfico de comparativa disponible próximamente') }}
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
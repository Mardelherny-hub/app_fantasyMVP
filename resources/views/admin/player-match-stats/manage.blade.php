<x-admin-layout>
    <div class="container mx-auto px-4 py-8" x-data="statsManager()">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('admin.player-match-stats.index', app()->getLocale()) }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                ← {{ __('Volver a partidos') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Cargar Estadísticas del Partido') }}</h1>
            
            {{-- Marcador --}}
            <div class="mt-3 bg-white rounded-lg shadow-sm border p-4 flex items-center justify-center gap-8">
                <div class="text-right">
                    <div class="text-lg font-bold text-gray-900">{{ $realMatch->fixture->homeTeam->name ?? 'Local' }}</div>
                </div>
                <div class="text-3xl font-black text-gray-900 bg-gray-100 px-6 py-2 rounded-lg">
                    {{ $realMatch->home_score ?? 0 }} - {{ $realMatch->away_score ?? 0 }}
                </div>
                <div class="text-left">
                    <div class="text-lg font-bold text-gray-900">{{ $realMatch->fixture->awayTeam->name ?? 'Visitante' }}</div>
                </div>
            </div>
            <div class="text-center mt-2 text-sm text-gray-500">
                {{ $realMatch->fixture->round ?? '' }} · {{ $realMatch->started_at_utc?->format('d/m/Y H:i') ?? '' }}
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        {{-- Barra de progreso --}}
        <div class="mb-6 bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium text-gray-700">{{ __('Progreso de carga') }}</span>
                <span class="text-sm font-bold text-blue-600">
                    {{ $existingStats->count() }} / {{ $homeTeamPlayers->count() + $awayTeamPlayers->count() }} {{ __('jugadores') }}
                </span>
            </div>
            @php
                $total = $homeTeamPlayers->count() + $awayTeamPlayers->count();
                $pct = $total > 0 ? round(($existingStats->count() / $total) * 100) : 0;
            @endphp
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="bg-blue-600 h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
        </div>

        {{-- Formulario Masivo --}}
        <form method="POST" action="{{ route('admin.player-match-stats.store-bulk', app()->getLocale()) }}" @submit="submitForm($event)">
            @csrf
            <input type="hidden" name="real_match_id" value="{{ $realMatch->id }}">

            {{-- Tabs Equipos --}}
            <div class="mb-4 border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button type="button" @click="activeTab = 'home'"
                        :class="activeTab === 'home' ? 'border-blue-500 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-6 py-3 text-sm font-medium border-b-2 transition-colors rounded-t-lg">
                        🏠 {{ $realMatch->fixture->homeTeam->name ?? 'Local' }}
                        <span class="ml-1 text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ $homeTeamPlayers->count() }}</span>
                        <span class="ml-1 text-xs px-2 py-0.5 rounded-full"
                            :class="homeCount >= 11 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                            x-text="'✓ ' + homeCount"></span>
                    </button>
                    <button type="button" @click="activeTab = 'away'"
                        :class="activeTab === 'away' ? 'border-red-500 text-red-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        class="px-6 py-3 text-sm font-medium border-b-2 transition-colors rounded-t-lg">
                        ✈️ {{ $realMatch->fixture->awayTeam->name ?? 'Visitante' }}
                        <span class="ml-1 text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ $awayTeamPlayers->count() }}</span>
                        <span class="ml-1 text-xs px-2 py-0.5 rounded-full"
                            :class="awayCount >= 11 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                            x-text="'✓ ' + awayCount"></span>
                    </button>
                </nav>
            </div>

            {{-- Acciones rápidas --}}
            <div class="mb-4 flex items-center gap-2 flex-wrap">
                <button type="button" @click="setAllMinutes('90')" class="px-3 py-1.5 bg-green-100 text-green-700 text-xs font-medium rounded-lg hover:bg-green-200 transition">
                    ⚡ {{ __('Todos 90 min') }}
                </button>
                <button type="button" @click="setAllMinutes('0')" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg hover:bg-gray-200 transition">
                    🔄 {{ __('Reset minutos') }}
                </button>
                <button type="button" @click="clearAll()" class="px-3 py-1.5 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200 transition">
                    🗑️ {{ __('Limpiar todo') }}
                </button>
                <span class="text-xs text-gray-400 ml-2">{{ __('Jugadores con 0 min y sin datos previos serán omitidos') }}</span>
            </div>

            {{-- Tabla Equipo Local --}}
            <div x-show="activeTab === 'home'" class="bg-white rounded-lg shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-8">#</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Jugador') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">{{ __('Pos') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">{{ __('Min') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Goles">⚽</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Asistencias">🎯</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Tarjetas Amarillas">🟨</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Tarjetas Rojas">🟥</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">{{ __('Rating') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($homeTeamPlayers as $index => $player)
                            @php $stat = $existingStats->get($player->id); @endphp
                            <tr class="{{ $stat ? 'bg-green-50' : '' }} hover:bg-gray-50 home-row">
                                <td class="px-3 py-2 text-xs text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-3 py-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $player->full_name }}</span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                        {{ $player->position === 'GK' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $player->position === 'DF' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $player->position === 'MF' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $player->position === 'FW' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ $player->position ?? '-' }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="hidden" name="stats[h{{ $index }}][real_player_id]" value="{{ $player->id }}">
                                    <input type="hidden" name="stats[h{{ $index }}][real_team_id]" value="{{ $realMatch->fixture->home_team_id }}">
                                    <input type="number" name="stats[h{{ $index }}][minutes]" min="0" max="120" 
                                           value="{{ $stat->minutes ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded home-minutes focus:ring-blue-500 focus:border-blue-500" @input="recalc()">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[h{{ $index }}][goals]" min="0" 
                                           value="{{ $stat->goals ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[h{{ $index }}][assists]" min="0" 
                                           value="{{ $stat->assists ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[h{{ $index }}][yellow_cards]" min="0" max="2" 
                                           value="{{ $stat->yellow_cards ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[h{{ $index }}][red_cards]" min="0" max="1" 
                                           value="{{ $stat->red_cards ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[h{{ $index }}][rating]" min="0" max="10" 
                                           value="{{ $stat->rating ?? '' }}" placeholder="-"
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if($stat)
                                        <span class="text-green-500" title="{{ __('Cargado') }}">✅</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tabla Equipo Visitante --}}
            <div x-show="activeTab === 'away'" class="bg-white rounded-lg shadow-sm border overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase w-8">#</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Jugador') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">{{ __('Pos') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">{{ __('Min') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Goles">⚽</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Asistencias">🎯</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Tarjetas Amarillas">🟨</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20" title="Tarjetas Rojas">🟥</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-20">{{ __('Rating') }}</th>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase w-12">{{ __('Estado') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($awayTeamPlayers as $index => $player)
                            @php $stat = $existingStats->get($player->id); @endphp
                            <tr class="{{ $stat ? 'bg-green-50' : '' }} hover:bg-gray-50 away-row">
                                <td class="px-3 py-2 text-xs text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-3 py-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $player->full_name }}</span>
                                </td>
                                <td class="px-3 py-2 text-center">
                                    <span class="text-xs font-medium px-1.5 py-0.5 rounded
                                        {{ $player->position === 'GK' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $player->position === 'DF' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $player->position === 'MF' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $player->position === 'FW' ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ $player->position ?? '-' }}</span>
                                </td>
                                <td class="px-3 py-2">
                                    <input type="hidden" name="stats[a{{ $index }}][real_player_id]" value="{{ $player->id }}">
                                    <input type="hidden" name="stats[a{{ $index }}][real_team_id]" value="{{ $realMatch->fixture->away_team_id }}">
                                    <input type="number" name="stats[a{{ $index }}][minutes]" min="0" max="120" 
                                           value="{{ $stat->minutes ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded away-minutes focus:ring-red-500 focus:border-red-500" @input="recalc()">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[a{{ $index }}][goals]" min="0" 
                                           value="{{ $stat->goals ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-red-500 focus:border-red-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[a{{ $index }}][assists]" min="0" 
                                           value="{{ $stat->assists ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-red-500 focus:border-red-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[a{{ $index }}][yellow_cards]" min="0" max="2" 
                                           value="{{ $stat->yellow_cards ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-red-500 focus:border-red-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[a{{ $index }}][red_cards]" min="0" max="1" 
                                           value="{{ $stat->red_cards ?? 0 }}" 
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-red-500 focus:border-red-500">
                                </td>
                                <td class="px-3 py-2">
                                    <input type="number" name="stats[a{{ $index }}][rating]" min="0" max="10" 
                                           value="{{ $stat->rating ?? '' }}" placeholder="-"
                                           class="w-full text-sm text-center border-gray-300 rounded focus:ring-red-500 focus:border-red-500">
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if($stat)
                                        <span class="text-green-500" title="{{ __('Cargado') }}">✅</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Botón Submit --}}
            <div class="mt-6 bg-white rounded-lg shadow-sm border p-4 flex items-center justify-between sticky bottom-0 z-10">
                <div class="text-sm text-gray-600">
                    💡 {{ __('Se guardarán solo jugadores con minutos > 0 o que ya tengan datos previos.') }}
                </div>
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-lg">
                    💾 {{ __('Guardar Todas las Estadísticas') }}
                </button>
            </div>
        </form>
         {{-- Botón Sincronizar al Fantasy (fuera del form principal) --}}
        @if($existingStats->count() > 0)
            <div class="mt-4 bg-amber-50 rounded-lg shadow-sm border border-amber-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-amber-800">🔄 {{ __('Sincronizar al Fantasy') }}</h3>
                        <p class="text-xs text-amber-600 mt-1">
                            {{ __('Crea los jugadores fantasy (si no existen) y copia las estadísticas para el sistema de puntuación.') }}
                        </p>
                        <p class="text-xs text-amber-600">
                            {{ __('Stats reales cargadas:') }} <span class="font-bold">{{ $existingStats->count() }}</span>
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.player-match-stats.sync-fantasy', [app()->getLocale(), $realMatch]) }}"
                          onsubmit="return confirm('{{ __('¿Sincronizar las estadísticas reales al sistema Fantasy?') }}')">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-amber-600 text-white font-semibold rounded-lg hover:bg-amber-700 transition shadow-lg">
                            🔄 {{ __('Sincronizar al Fantasy') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <script>
        function statsManager() {
            return {
                activeTab: 'home',
                
                get homeCount() {
                    let count = 0;
                    document.querySelectorAll('.home-minutes').forEach(input => {
                        if (parseInt(input.value) > 0) count++;
                    });
                    return count;
                },

                get awayCount() {
                    let count = 0;
                    document.querySelectorAll('.away-minutes').forEach(input => {
                        if (parseInt(input.value) > 0) count++;
                    });
                    return count;
                },

                get activeCount() {
                    return this.activeTab === 'home' ? this.homeCount : this.awayCount;
                },

                recalc() {
                    // Forzar reactividad de Alpine al cambiar inputs
                    this.$nextTick(() => {});
                },

                setAllMinutes(value) {
                    const selector = this.activeTab === 'home' ? '.home-minutes' : '.away-minutes';
                    document.querySelectorAll(selector).forEach(input => {
                        input.value = value;
                    });
                    this.recalc();
                },

                clearAll() {
                    const selector = this.activeTab === 'home' ? '.home-row' : '.away-row';
                    document.querySelectorAll(selector + ' input[type="number"]').forEach(input => {
                        if (input.classList.contains('home-minutes') || input.classList.contains('away-minutes')) {
                            input.value = '0';
                        } else {
                            input.value = input.placeholder === '-' ? '' : '0';
                        }
                    });
                    this.recalc();
                },

                submitForm(event) {
                    const homeScore = {{ $realMatch->home_score ?? 0 }};
                    const awayScore = {{ $realMatch->away_score ?? 0 }};

                    // Sumar goles cargados por equipo
                    let homeGoals = 0;
                    let awayGoals = 0;
                    let homeCount = 0;
                    let awayCount = 0;

                    document.querySelectorAll('.home-row').forEach(row => {
                        const min = parseInt(row.querySelector('.home-minutes')?.value) || 0;
                        const goals = parseInt(row.querySelector('input[name*="[goals]"]')?.value) || 0;
                        if (min > 0) homeCount++;
                        homeGoals += goals;
                    });

                    document.querySelectorAll('.away-row').forEach(row => {
                        const min = parseInt(row.querySelector('.away-minutes')?.value) || 0;
                        const goals = parseInt(row.querySelector('input[name*="[goals]"]')?.value) || 0;
                        if (min > 0) awayCount++;
                        awayGoals += goals;
                    });

                    // Validar goles
                    if (homeGoals > homeScore) {
                        alert('⚠️ Error: Los goles del equipo local (' + homeGoals + ') superan el resultado del partido (' + homeScore + ').');
                        event.preventDefault();
                        return;
                    }
                    if (awayGoals > awayScore) {
                        alert('⚠️ Error: Los goles del equipo visitante (' + awayGoals + ') superan el resultado del partido (' + awayScore + ').');
                        event.preventDefault();
                        return;
                    }

                    // Validar mínimo 11 jugadores
                    if (homeCount < 11 || awayCount < 11) {
                        if (!confirm('⚠️ Atención: Local tiene ' + homeCount + ' jugadores y Visitante tiene ' + awayCount + ' jugadores con minutos.\n\nSe requieren mínimo 11 por equipo.\n\n¿Desea guardar de todas formas?')) {
                            event.preventDefault();
                        }
                    }
                }
            }
        }
    </script>
</x-admin-layout>
<div class="min-h-screen bg-slate-900 text-white pb-12">
    {{-- Background retro gaming effects --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden opacity-30">
        <div class="absolute top-0 left-0 w-full h-full" style="background-image: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(16, 185, 129, 0.03) 2px, rgba(16, 185, 129, 0.03) 4px);"></div>
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl" style="animation: pulse 4s ease-in-out infinite;"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        {{-- Header con selector de gameweek --}}
        <div class="pt-6 pb-4">
            <div class="flex items-center justify-between mb-6">
                {{-- Título --}}
                <div>
                    <div class="inline-flex items-center space-x-2 bg-emerald-500/10 backdrop-blur-lg border border-emerald-500/30 px-4 py-2 rounded-lg mb-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Lineup Manager</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-black mb-1">
                        {{ __('Mi Alineación') }}
                    </h1>
                    <p class="text-gray-400 text-sm">{{ $team->name }}</p>
                </div>

                {{-- Selector de Gameweek con navegación --}}
                <div class="flex items-center space-x-3">
                    {{-- Botón anterior --}}
                    <button 
                        wire:click="previousGameweek"
                        @if(!$hasPreviousGW) disabled @endif
                        class="p-2 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:border-cyan-500/50 transition disabled:opacity-30 disabled:cursor-not-allowed"
                        title="{{ __('Gameweek anterior') }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>

                    {{-- Selector --}}
                    <select 
                        wire:model.live="selectedGameweek.id"
                        wire:change="selectGameweek($event.target.value)"
                        class="bg-slate-800 border-2 border-cyan-500/50 text-white px-4 py-2 rounded-lg font-bold hover:border-cyan-400 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-500/20 transition"
                    >
                        @foreach($availableGameweeks as $gw)
                            <option value="{{ $gw->id }}" @if($selectedGameweek && $selectedGameweek->id === $gw->id) selected @endif>
                                GW{{ $gw->number }}
                                @if($currentGameweek && $gw->id === $currentGameweek->id)
                                    ({{ __('Actual') }})
                                @endif
                            </option>
                        @endforeach
                    </select>

                    {{-- Botón siguiente --}}
                    <button 
                        wire:click="nextGameweek"
                        @if(!$hasNextGW) disabled @endif
                        class="p-2 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 hover:border-cyan-500/50 transition disabled:opacity-30 disabled:cursor-not-allowed"
                        title="{{ __('Gameweek siguiente') }}"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mensajes de estado --}}
            @if($successMessage)
                <div class="mb-4 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg flex items-center space-x-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-emerald-400 text-sm font-medium">{{ $successMessage }}</span>
                </div>
            @endif

            @if($errorMessage)
                <div class="mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-lg flex items-center space-x-3">
                    <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-red-400 text-sm font-medium">{{ $errorMessage }}</span>
                </div>
            @endif

            {{-- Info de estado --}}
            <div class="flex items-center justify-between p-4 bg-slate-800/50 border border-slate-700 rounded-lg">
                <div class="flex items-center space-x-6">
                    {{-- Formación --}}
                    <div class="flex items-center space-x-2">
                        <span class="text-gray-400 text-sm">{{ __('Formación:') }}</span>
                        <span class="font-bold text-cyan-400 text-lg font-mono">{{ $currentFormation ?? '---' }}</span>
                    </div>

                    {{-- Capitanes --}}
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-1.5">
                            <span class="w-5 h-5 bg-yellow-500 rounded flex items-center justify-center text-xs font-black text-slate-900">C</span>
                            <span class="text-sm text-gray-400">{{ $captain ? $captain->player->display_name : '---' }}</span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <span class="w-5 h-5 bg-gray-400 rounded flex items-center justify-center text-xs font-black text-slate-900">V</span>
                            <span class="text-sm text-gray-400">{{ $viceCaptain ? $viceCaptain->player->display_name : '---' }}</span>
                        </div>
                    </div>

                    {{-- Estado válido --}}
                    @if($isValidLineup)
                        <div class="flex items-center space-x-1.5 px-3 py-1 bg-emerald-500/20 rounded-lg">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-emerald-400 text-xs font-bold uppercase">{{ __('Válida') }}</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-1.5 px-3 py-1 bg-red-500/20 rounded-lg">
                            <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-red-400 text-xs font-bold uppercase">{{ __('Inválida') }}</span>
                        </div>
                    @endif
                </div>

                {{-- Botón guardar --}}
                @if($canEdit)
                    <button 
                        wire:click="saveLineup"
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-cyan-500 text-slate-900 font-bold rounded-lg hover:shadow-lg hover:shadow-emerald-500/30 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                    >
                        <span wire:loading.remove>{{ __('Guardar Cambios') }}</span>
                        <span wire:loading class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Guardando...') }}
                        </span>
                    </button>
                @else
                    <div class="px-6 py-2.5 bg-slate-700 text-gray-400 font-bold rounded-lg flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('Bloqueada') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Grid principal: Cancha + Stats --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
            
            {{-- CANCHA (3 columnas) --}}
            <div class="lg:col-span-3">
                {{-- Área de titulares --}}
                <div class="bg-gradient-to-b from-emerald-900/30 to-emerald-800/20 border-2 border-emerald-500/30 rounded-2xl p-6 mb-6 relative overflow-hidden">
                    {{-- Líneas de cancha retro --}}
                    <div class="absolute inset-0 pointer-events-none opacity-20">
                        <div class="absolute top-0 left-1/2 w-px h-full bg-white"></div>
                        <div class="absolute top-1/2 left-1/2 w-24 h-24 border-2 border-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                    </div>

                    <div class="relative z-10">
                        <h2 class="text-lg font-black text-emerald-400 mb-4 flex items-center space-x-2">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            <span>{{ __('TITULARES') }} (11)</span>
                        </h2>

                        {{-- Grid de titulares --}}
                        <div class="grid grid-cols-11 gap-3 min-h-[400px]">
                            @foreach($starters as $roster)
                                {{-- Placeholder para player-card component --}}
                                <div 
                                    wire:key="starter-{{ $roster->player_id }}"
                                    wire:click="openPlayerModal({{ $roster->player_id }})"
                                    class="col-span-1 cursor-pointer"
                                >
                                    {{-- Este será reemplazado por el componente player-card --}}
                                    <div class="bg-slate-800/80 border-2 border-cyan-500/50 rounded-lg p-3 hover:border-cyan-400 hover:shadow-lg hover:shadow-cyan-500/20 transition group">
                                        {{-- Posición badge --}}
                                        <div class="text-xs font-bold text-center mb-2 px-2 py-1 rounded 
                                            @if($roster->player->position === 1) bg-yellow-500/20 text-yellow-400
                                            @elseif($roster->player->position === 2) bg-blue-500/20 text-blue-400
                                            @elseif($roster->player->position === 3) bg-emerald-500/20 text-emerald-400
                                            @else bg-red-500/20 text-red-400
                                            @endif">
                                            {{ $roster->player->position_name }}
                                        </div>
                                        
                                        {{-- Nombre --}}
                                        <div class="text-xs text-center font-bold text-white truncate">
                                            {{ $roster->player->known_as ?? $roster->player->full_name }}
                                        </div>

                                        {{-- Capitanía --}}
                                        @if($roster->captaincy === 1)
                                            <div class="mt-1 w-5 h-5 bg-yellow-500 rounded-full flex items-center justify-center text-xs font-black text-slate-900 mx-auto">C</div>
                                        @elseif($roster->captaincy === 2)
                                            <div class="mt-1 w-5 h-5 bg-gray-400 rounded-full flex items-center justify-center text-xs font-black text-slate-900 mx-auto">V</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Área de banco --}}
                <div class="bg-slate-800/50 border-2 border-slate-700 rounded-2xl p-6">
                    <h2 class="text-lg font-black text-gray-400 mb-4 flex items-center space-x-2">
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        <span>{{ __('BANCO') }} (12)</span>
                    </h2>

                    {{-- Grid de suplentes --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($bench as $roster)
                            <div 
                                wire:key="bench-{{ $roster->player_id }}"
                                wire:click="openPlayerModal({{ $roster->player_id }})"
                                class="cursor-pointer"
                            >
                                {{-- Placeholder para player-card component (versión compacta) --}}
                                <div class="bg-slate-700/50 border border-slate-600 rounded-lg p-2.5 hover:border-gray-400 hover:shadow-lg hover:shadow-gray-500/10 transition">
                                    <div class="text-xs font-bold text-center mb-1.5 px-1.5 py-0.5 rounded bg-slate-600/50 text-gray-400">
                                        {{ $roster->player->position_name }}
                                    </div>
                                    <div class="text-xs text-center font-medium text-gray-300 truncate">
                                        {{ $roster->player->known_as ?? $roster->player->full_name }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- PANEL DE STATS (1 columna) --}}
            <div class="lg:col-span-1">
                <div class="bg-slate-800/50 border-2 border-slate-700 rounded-2xl p-6 sticky top-6">
                    <h3 class="text-lg font-black text-cyan-400 mb-4 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                        <span>{{ __('Estadísticas') }}</span>
                    </h3>

                    {{-- Stats items --}}
                    <div class="space-y-3">
                        {{-- Formación detallada --}}
                        <div class="p-3 bg-slate-700/50 rounded-lg">
                            <div class="text-xs text-gray-400 mb-1">{{ __('Formación') }}</div>
                            <div class="flex items-center justify-between">
                                <div class="font-mono text-lg font-bold text-white">{{ $currentFormation ?? '---' }}</div>
                            </div>
                            <div class="mt-2 grid grid-cols-4 gap-1 text-xs font-mono">
                                <div class="text-center">
                                    <div class="text-yellow-400 font-bold">{{ $formationStats['formation']['GK'] ?? 0 }}</div>
                                    <div class="text-gray-500">GK</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-blue-400 font-bold">{{ $formationStats['formation']['DF'] ?? 0 }}</div>
                                    <div class="text-gray-500">DF</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-emerald-400 font-bold">{{ $formationStats['formation']['MF'] ?? 0 }}</div>
                                    <div class="text-gray-500">MF</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-red-400 font-bold">{{ $formationStats['formation']['FW'] ?? 0 }}</div>
                                    <div class="text-gray-500">FW</div>
                                </div>
                            </div>
                        </div>

                        {{-- Jugadores totales --}}
                        <div class="p-3 bg-slate-700/50 rounded-lg">
                            <div class="text-xs text-gray-400 mb-1">{{ __('Jugadores') }}</div>
                            <div class="text-2xl font-bold text-white">{{ $formationStats['total_players'] ?? 0 }}/23</div>
                        </div>

                        {{-- Titulares/Banco --}}
                        <div class="p-3 bg-slate-700/50 rounded-lg">
                            <div class="text-xs text-gray-400 mb-2">{{ __('Distribución') }}</div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-300">{{ __('Titulares:') }}</span>
                                <span class="font-bold text-emerald-400">{{ $formationStats['starters_count'] ?? 0 }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm mt-1">
                                <span class="text-gray-300">{{ __('Banco:') }}</span>
                                <span class="font-bold text-gray-400">{{ $formationStats['bench_count'] ?? 0 }}</span>
                            </div>
                        </div>

                        {{-- Información de deadline --}}
                        @if($selectedGameweek)
                            <div class="p-3 bg-slate-700/50 rounded-lg border-l-2 
                                @if($canEdit) border-emerald-500 @else border-red-500 @endif">
                                <div class="text-xs text-gray-400 mb-1">{{ __('Deadline') }}</div>
                                <div class="text-sm font-mono text-white">
                                    {{ $selectedGameweek->starts_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="text-xs mt-1 
                                    @if($canEdit) text-emerald-400 @else text-red-400 @endif">
                                    @if($canEdit)
                                        {{ __('Puedes editar') }}
                                    @else
                                        {{ __('Bloqueada') }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Botones de acción --}}
                    <div class="mt-6 space-y-2">
                        <button 
                            wire:click="refreshLineup"
                            class="w-full py-2 bg-slate-700 text-gray-300 font-medium rounded-lg hover:bg-slate-600 transition flex items-center justify-center space-x-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span>{{ __('Actualizar') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de jugador (placeholder - se creará después) --}}
    @if($showPlayerModal && $selectedPlayerRoster)
        {{-- Aquí irá el componente player-modal --}}
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-slate-800 border-2 border-cyan-500/50 rounded-2xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">{{ $selectedPlayerRoster->player->display_name }}</h3>
                    <button wire:click="closePlayerModal" class="text-gray-400 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="text-sm text-gray-400 mb-4">
                    {{ __('Posición:') }} <span class="text-white font-bold">{{ $selectedPlayerRoster->player->position_name }}</span>
                </div>

                @if($canEdit)
                    <div class="space-y-2">
                        @if($selectedPlayerRoster->is_starter)
                            <button 
                                wire:click="handlePlayerSelected({{ $selectedPlayerRoster->player_id }}, 'to_bench')"
                                class="w-full py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition"
                            >
                                {{ __('Mover al banco') }}
                            </button>
                        @else
                            <button 
                                wire:click="handlePlayerSelected({{ $selectedPlayerRoster->player_id }}, 'to_starting')"
                                class="w-full py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-500 transition"
                            >
                                {{ __('Mover a titulares') }}
                            </button>
                        @endif

                        @if($selectedPlayerRoster->is_starter)
                            <button 
                                wire:click="handlePlayerSelected({{ $selectedPlayerRoster->player_id }}, 'set_captain')"
                                class="w-full py-2 bg-yellow-600 text-slate-900 font-bold rounded-lg hover:bg-yellow-500 transition"
                            >
                                {{ __('Asignar Capitán') }}
                            </button>
                            <button 
                                wire:click="handlePlayerSelected({{ $selectedPlayerRoster->player_id }}, 'set_vice')"
                                class="w-full py-2 bg-gray-600 text-white font-bold rounded-lg hover:bg-gray-500 transition"
                            >
                                {{ __('Asignar Vicecapitán') }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 items-center justify-center">
        <div class="bg-slate-800 border-2 border-cyan-500/50 rounded-2xl p-8 flex flex-col items-center space-y-4">
            <svg class="animate-spin h-12 w-12 text-cyan-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-cyan-400 font-bold text-lg">{{ __('Procesando...') }}</span>
        </div>
    </div>
</div>
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
                    <h1 class="text-3xl md:text-4xl font-black text-white">
                        {{ $team->name }}
                    </h1>
                </div>
            </div>

            {{-- Selector de Gameweek --}}
            @if($selectedGameweek)
                <div class="bg-slate-800/50 backdrop-blur-sm border border-slate-700 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            {{-- Botón anterior --}}
                            <button 
                                wire:click="previousGameweek"
                                @if(!$hasPreviousGW) disabled @endif
                                class="p-2 rounded-lg hover:bg-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed"
                            >
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>

                            {{-- Info gameweek --}}
                            <div>
                                <div class="text-sm text-gray-400">{{ __('Gameweek') }}</div>
                                <div class="font-bold text-white text-lg">{{ $selectedGameweek->name }}</div>
                            </div>

                            {{-- Botón siguiente --}}
                            <button 
                                wire:click="nextGameweek"
                                @if(!$hasNextGW) disabled @endif
                                class="p-2 rounded-lg hover:bg-slate-700 transition disabled:opacity-30 disabled:cursor-not-allowed"
                            >
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Estado de edición --}}
                        @if($canEdit)
                            <div class="flex items-center space-x-2 text-emerald-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                                <span>{{ __('Editable') }}</span>
                            </div>
                        @else
                            <div class="flex items-center space-x-2 text-red-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ __('Bloqueada') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Mensajes --}}
        {{-- Toast de notificaciones (Alpine.js) --}}
        <div x-data="{ 
            showSuccess: @entangle('successMessage').live,
            showError: @entangle('errorMessage').live,
            autoHide(property) {
                setTimeout(() => {
                    this[property] = null;
                    @this.set(property.replace('show', '').toLowerCase() + 'Message', null);
                }, 4000);
            }
        }" 
        x-init="
            $watch('showSuccess', value => { if(value) autoHide('showSuccess') });
            $watch('showError', value => { if(value) autoHide('showError') });
        ">
            {{-- Toast de éxito --}}
            <div x-show="showSuccess" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed top-24 right-4 z-50 max-w-md"
                style="display: none;">
                <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 text-white px-6 py-4 rounded-xl shadow-2xl border-2 border-emerald-400 flex items-start space-x-3">
                    <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-black text-lg">{{ __('¡Guardado!') }}</h4>
                        <p class="text-sm text-emerald-50" x-text="showSuccess"></p>
                    </div>
                    <button @click="showSuccess = null" class="text-emerald-100 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Toast de error --}}
            <div x-show="showError" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="$el.classList.add('animate-shake')"
                class="fixed top-24 right-4 z-50 max-w-md"
                style="display: none;">
                <div class="bg-gradient-to-r from-red-600 to-red-500 text-white px-6 py-4 rounded-xl shadow-2xl border-2 border-red-400 flex items-start space-x-3">
                    <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <h4 class="font-black text-lg">{{ __('Error') }}</h4>
                        <p class="text-sm text-red-50" x-text="showError"></p>
                    </div>
                    <button @click="showError = null" class="text-red-100 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <style>
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            .animate-shake {
                animation: shake 0.5s;
            }
        </style>

        {{-- Info de estado --}}
        <div class="mb-6 flex items-center justify-between p-4 bg-slate-800/50 border border-slate-700 rounded-lg">
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
                    @if(!$isValidLineup) disabled @endif
                    class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-bold rounded-lg hover:from-emerald-500 hover:to-emerald-400 transition shadow-lg hover:shadow-emerald-500/50 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove>{{ __('Guardar Alineación') }}</span>
                    <span wire:loading>{{ __('Guardando...') }}</span>
                </button>
            @endif
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
                        <h2 class="text-lg font-black text-emerald-400 mb-6 flex items-center space-x-2">
                            <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                            <span>{{ __('TITULARES') }} ({{ $starters->count() }}/11)</span>
                        </h2>

                        @php
                            // Agrupar titulares por posición
                            $gk = $starters->filter(fn($r) => $r->player->position === 1); // GK
                            $df = $starters->filter(fn($r) => $r->player->position === 2); // DF
                            $mf = $starters->filter(fn($r) => $r->player->position === 3); // MF
                            $fw = $starters->filter(fn($r) => $r->player->position === 4); // FW
                        @endphp

                        <div class="space-y-8">
                            {{-- FILA 4: Delanteros --}}
                            @if($fw->count() > 0)
                                <div class="flex justify-center items-center gap-4">
                                    @foreach($fw as $roster)
                                        <div class="w-24" wire:key="starter-fw-{{ $roster->player_id }}">
                                            <x-manager.lineup.player-card 
                                                :roster="$roster" 
                                                variant="starter"
                                                wire:click="openPlayerModal({{ $roster->player_id }})"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- FILA 3: Mediocampistas --}}
                            @if($mf->count() > 0)
                                <div class="flex justify-center items-center gap-4">
                                    @foreach($mf as $roster)
                                        <div class="w-24" wire:key="starter-mf-{{ $roster->player_id }}">
                                            <x-manager.lineup.player-card 
                                                :roster="$roster" 
                                                variant="starter"
                                                wire:click="openPlayerModal({{ $roster->player_id }})"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- FILA 2: Defensores --}}
                            @if($df->count() > 0)
                                <div class="flex justify-center items-center gap-4">
                                    @foreach($df as $roster)
                                        <div class="w-24" wire:key="starter-df-{{ $roster->player_id }}">
                                            <x-manager.lineup.player-card 
                                                :roster="$roster" 
                                                variant="starter"
                                                wire:click="openPlayerModal({{ $roster->player_id }})"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- FILA 1: Arquero --}}
                            @if($gk->count() > 0)
                                <div class="flex justify-center items-center">
                                    @foreach($gk as $roster)
                                        <div class="w-24" wire:key="starter-gk-{{ $roster->player_id }}">
                                            <x-manager.lineup.player-card 
                                                :roster="$roster" 
                                                variant="starter"
                                                wire:click="openPlayerModal({{ $roster->player_id }})"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- Mensaje si no hay titulares --}}
                        @if($starters->count() === 0)
                            <div class="text-center py-12 text-gray-400">
                                <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <p class="text-lg">{{ __('No hay titulares configurados') }}</p>
                                <p class="text-sm mt-2">{{ __('Agrega jugadores a tu alineación desde el banco') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Área de banco --}}
                <div class="bg-slate-800/50 border-2 border-slate-700 rounded-2xl p-6">
                    <h2 class="text-lg font-black text-gray-400 mb-4 flex items-center space-x-2">
                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                        <span>{{ __('BANCO') }} ({{ $bench->count() }}/23)</span>
                    </h2>

                    {{-- Grid de suplentes --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                        @foreach($bench as $roster)
                            <div wire:key="bench-{{ $roster->player_id }}">
                                <x-manager.lineup.player-card 
                                    :roster="$roster" 
                                    variant="bench"
                                    wire:click="openPlayerModal({{ $roster->player_id }})"
                                />
                            </div>
                        @endforeach
                    </div>

                    {{-- Mensaje si no hay suplentes --}}
                    @if($bench->count() === 0)
                        <div class="text-center py-8 text-gray-500">
                            <p>{{ __('No hay suplentes en el banco') }}</p>
                        </div>
                    @endif
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

                        {{-- Jugadores --}}
                        <div class="p-3 bg-slate-700/50 rounded-lg">
                            <div class="text-xs text-gray-400 mb-2">{{ __('Jugadores') }}</div>
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
                            <span>{{ __('Refrescar') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de jugador --}}
    @if($showPlayerModal && $selectedPlayerRoster)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" wire:click.self="closePlayerModal">
            <div class="bg-slate-800 border-2 border-slate-700 rounded-2xl max-w-md w-full shadow-2xl" @click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-slate-700">
                    <div>
                        <h3 class="text-xl font-black text-white">{{ $selectedPlayerRoster->player->display_name }}</h3>
                        <p class="text-sm text-gray-400">{{ $selectedPlayerRoster->player->team->short_name ?? '' }}</p>
                    </div>
                    <button wire:click="closePlayerModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4">
                    {{-- Info básica --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-700/50 rounded-lg p-3">
                            <div class="text-xs text-gray-400 mb-1">{{ __('Posición') }}</div>
                            <div class="text-white font-bold">{{ $selectedPlayerRoster->player->position_name }}</div>
                        </div>
                        <div class="bg-slate-700/50 rounded-lg p-3">
                            <div class="text-xs text-gray-400 mb-1">{{ __('Estado') }}</div>
                            <div class="text-white font-bold">
                                {{ $selectedPlayerRoster->is_starter ? __('Titular') : __('Suplente') }}
                            </div>
                        </div>
                    </div>

                    {{-- Acciones (solo si puede editar) --}}
                    @if($canEdit)
                        <div class="space-y-2 pt-4 border-t border-slate-700">
                            @if($selectedPlayerRoster->is_starter)
                                <button 
                                    wire:click="removeFromStarting({{ $selectedPlayerRoster->player_id }})"
                                    class="w-full py-3 bg-slate-700 text-white rounded-lg hover:bg-slate-600 transition font-medium"
                                >
                                    {{ __('Mover al banco') }}
                                </button>
                            @else
                                <button 
                                    wire:click="addToStarting({{ $selectedPlayerRoster->player_id }})"
                                    class="w-full py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-500 transition font-medium"
                                >
                                    {{ __('Mover a titulares') }}
                                </button>
                            @endif

                            @if($selectedPlayerRoster->is_starter)
                                <div class="grid grid-cols-2 gap-2">
                                    <button 
                                        wire:click="setCaptain({{ $selectedPlayerRoster->player_id }})"
                                        class="py-3 bg-yellow-600 text-slate-900 rounded-lg hover:bg-yellow-500 transition font-bold"
                                    >
                                        {{ __('Capitán') }}
                                    </button>
                                    <button 
                                        wire:click="setViceCaptain({{ $selectedPlayerRoster->player_id }})"
                                        class="py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-500 transition font-bold"
                                    >
                                        {{ __('Vice') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
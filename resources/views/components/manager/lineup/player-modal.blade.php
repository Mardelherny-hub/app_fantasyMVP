@props([
    'roster',      // FantasyRoster con player
    'canEdit' => true,  // Si se pueden mostrar botones de acción
])

@php
    $player = $roster->player;
    
    // Colores según posición
    $positionColors = [
        1 => ['bg' => 'bg-yellow-500', 'text' => 'text-yellow-400', 'border' => 'border-yellow-500', 'gradient' => 'from-yellow-500 to-yellow-600'],
        2 => ['bg' => 'bg-blue-500', 'text' => 'text-blue-400', 'border' => 'border-blue-500', 'gradient' => 'from-blue-500 to-blue-600'],
        3 => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-400', 'border' => 'border-emerald-500', 'gradient' => 'from-emerald-500 to-emerald-600'],
        4 => ['bg' => 'bg-red-500', 'text' => 'text-red-400', 'border' => 'border-red-500', 'gradient' => 'from-red-500 to-red-600'],
    ];
    
    $colors = $positionColors[$player->position] ?? $positionColors[4];
@endphp

{{-- Modal backdrop --}}
<div class="fixed inset-0 bg-black/90 backdrop-blur-md z-50 flex items-center justify-center p-4 animate-fade-in">
    
    {{-- Modal content --}}
    <div class="bg-slate-900 border-2 {{ $colors['border'] }} rounded-2xl max-w-2xl w-full shadow-2xl shadow-{{ $colors['bg'] }}/50 animate-scale-in">
        
        {{-- Header --}}
        <div class="relative bg-gradient-to-r {{ $colors['gradient'] }} p-6 rounded-t-2xl">
            {{-- Efecto retro grid --}}
            <div class="absolute inset-0 opacity-10 pointer-events-none" 
                 style="background-image: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(255,255,255,0.1) 2px, rgba(255,255,255,0.1) 4px);"></div>
            
            <div class="relative flex items-start justify-between">
                <div class="flex-1">
                    {{-- Badge de posición --}}
                    <div class="inline-flex items-center px-3 py-1 bg-black/30 backdrop-blur-sm rounded-lg mb-2 border border-white/20">
                        <span class="text-xs font-black text-white uppercase tracking-wider">
                            {{ $player->position_name }}
                        </span>
                    </div>
                    
                    {{-- Nombre --}}
                    <h2 class="text-2xl md:text-3xl font-black text-white mb-1">
                        {{ $player->known_as ?? $player->full_name }}
                    </h2>
                    
                    {{-- Info adicional --}}
                    <div class="flex items-center space-x-3 text-sm text-white/80">
                        @if($player->nationality)
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $player->nationality }}</span>
                            </span>
                        @endif
                        
                        @if($player->age)
                            <span class="flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                <span>{{ $player->age }} {{ __('años') }}</span>
                            </span>
                        @endif
                    </div>
                </div>
                
                {{-- Botón cerrar --}}
                <button 
                    wire:click="closePlayerModal"
                    class="flex-shrink-0 w-10 h-10 bg-black/30 backdrop-blur-sm rounded-lg flex items-center justify-center text-white/80 hover:text-white hover:bg-black/50 transition border border-white/20"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-6 max-h-[60vh] overflow-y-auto">
            
            {{-- Estado actual --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Ubicación en roster --}}
                <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4">
                    <div class="text-xs text-gray-400 mb-1 uppercase tracking-wide">{{ __('Estado') }}</div>
                    <div class="flex items-center space-x-2">
                        @if($roster->is_starter)
                            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-emerald-400 font-bold">{{ __('Titular') }}</span>
                        @else
                            <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                            <span class="text-gray-400 font-bold">{{ __('Suplente') }}</span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Slot {{ $roster->slot }}</div>
                </div>

                {{-- Capitanía --}}
                <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4">
                    <div class="text-xs text-gray-400 mb-1 uppercase tracking-wide">{{ __('Capitanía') }}</div>
                    <div class="flex items-center space-x-2">
                        @if($roster->captaincy === 1)
                            <div class="w-6 h-6 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center shadow-lg shadow-yellow-500/50">
                                <span class="text-xs font-black text-slate-900">C</span>
                            </div>
                            <span class="text-yellow-400 font-bold">{{ __('Capitán') }}</span>
                        @elseif($roster->captaincy === 2)
                            <div class="w-6 h-6 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center shadow-lg shadow-gray-500/50">
                                <span class="text-xs font-black text-slate-900">V</span>
                            </div>
                            <span class="text-gray-400 font-bold">{{ __('Vicecapitán') }}</span>
                        @else
                            <span class="text-gray-500">{{ __('Sin capitanía') }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Estadísticas (placeholder) --}}
            <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4">
                <h3 class="text-sm font-black text-cyan-400 mb-3 uppercase tracking-wider flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <span>{{ __('Estadísticas') }}</span>
                </h3>
                
                <div class="grid grid-cols-3 gap-3">
                    {{-- Puntos totales --}}
                    <div class="text-center p-3 bg-slate-700/50 rounded-lg">
                        <div class="text-2xl font-black text-white font-mono">--</div>
                        <div class="text-xs text-gray-400 mt-1">{{ __('Pts Total') }}</div>
                    </div>
                    
                    {{-- Última GW --}}
                    <div class="text-center p-3 bg-slate-700/50 rounded-lg">
                        <div class="text-2xl font-black text-emerald-400 font-mono">--</div>
                        <div class="text-xs text-gray-400 mt-1">{{ __('Última GW') }}</div>
                    </div>
                    
                    {{-- Promedio --}}
                    <div class="text-center p-3 bg-slate-700/50 rounded-lg">
                        <div class="text-2xl font-black text-cyan-400 font-mono">--</div>
                        <div class="text-xs text-gray-400 mt-1">{{ __('Promedio') }}</div>
                    </div>
                </div>
            </div>

            {{-- Info adicional --}}
            @if($player->height_cm || $player->weight_kg)
                <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-4">
                    <h3 class="text-sm font-black text-cyan-400 mb-3 uppercase tracking-wider">{{ __('Información') }}</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @if($player->height_cm)
                            <div>
                                <span class="text-gray-400">{{ __('Altura:') }}</span>
                                <span class="text-white font-bold ml-2">{{ $player->height_cm }} cm</span>
                            </div>
                        @endif
                        @if($player->weight_kg)
                            <div>
                                <span class="text-gray-400">{{ __('Peso:') }}</span>
                                <span class="text-white font-bold ml-2">{{ $player->weight_kg }} kg</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Acciones --}}
            @if($canEdit)
                <div class="bg-gradient-to-r from-slate-800/50 to-slate-700/50 border border-slate-600 rounded-xl p-4">
                    <h3 class="text-sm font-black text-cyan-400 mb-3 uppercase tracking-wider flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        <span>{{ __('Acciones') }}</span>
                    </h3>
                    
                    <div class="space-y-2">
                        {{-- Mover entre titular/suplente --}}
                        @if($roster->is_starter)
                            <button 
                                wire:click="handlePlayerSelected({{ $player->id }}, 'to_bench')"
                                class="w-full py-3 bg-slate-700 text-white font-bold rounded-lg hover:bg-slate-600 transition flex items-center justify-center space-x-2 border border-slate-600 hover:border-gray-500"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <span>{{ __('Mover al Banco') }}</span>
                            </button>
                        @else
                            <button 
                                wire:click="handlePlayerSelected({{ $player->id }}, 'to_starting')"
                                class="w-full py-3 bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-bold rounded-lg hover:shadow-lg hover:shadow-emerald-500/30 transition flex items-center justify-center space-x-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                                <span>{{ __('Mover a Titulares') }}</span>
                            </button>
                        @endif

                        {{-- Asignar capitanes (solo si es titular) --}}
                        @if($roster->is_starter)
                            <div class="grid grid-cols-2 gap-2">
                                <button 
                                    wire:click="handlePlayerSelected({{ $player->id }}, 'set_captain')"
                                    class="py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-slate-900 font-black rounded-lg hover:shadow-lg hover:shadow-yellow-500/30 transition flex items-center justify-center space-x-2"
                                    @if($roster->captaincy === 1) disabled @endif
                                >
                                    <span class="text-lg">C</span>
                                    <span>{{ __('Capitán') }}</span>
                                </button>
                                
                                <button 
                                    wire:click="handlePlayerSelected({{ $player->id }}, 'set_vice')"
                                    class="py-3 bg-gradient-to-r from-gray-400 to-gray-500 text-slate-900 font-black rounded-lg hover:shadow-lg hover:shadow-gray-500/30 transition flex items-center justify-center space-x-2"
                                    @if($roster->captaincy === 2) disabled @endif
                                >
                                    <span class="text-lg">V</span>
                                    <span>{{ __('Vice') }}</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="p-4 bg-slate-800/50 border-t border-slate-700 rounded-b-2xl flex items-center justify-between">
            <div class="text-xs text-gray-500">
                Player ID: <span class="font-mono">{{ $player->id }}</span>
            </div>
            
            <button 
                wire:click="closePlayerModal"
                class="px-6 py-2 bg-slate-700 text-gray-300 font-bold rounded-lg hover:bg-slate-600 hover:text-white transition border border-slate-600"
            >
                {{ __('Cerrar') }}
            </button>
        </div>
    </div>
</div>

{{-- Estilos para animaciones --}}
<style>
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes scale-in {
        from { 
            opacity: 0;
            transform: scale(0.9);
        }
        to { 
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .animate-fade-in {
        animation: fade-in 0.2s ease-out;
    }
    
    .animate-scale-in {
        animation: scale-in 0.3s ease-out;
    }
</style>
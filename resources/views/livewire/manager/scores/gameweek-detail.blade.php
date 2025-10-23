<div class="p-6">
    {{-- Resumen del Gameweek --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">
                    Gameweek {{ $gameweek->number }}
                </h3>
                <p class="text-sm text-gray-600">
                    {{ $gameweek->starts_at->format('d/m/Y') }} - {{ $gameweek->ends_at->format('d/m/Y') }}
                </p>
            </div>
            
            <div class="text-right">
                <div class="text-4xl font-bold text-blue-600">{{ $totalPoints }}</div>
                <div class="text-sm text-gray-600">{{ __('Puntos Totales') }}</div>
            </div>
        </div>

        {{-- Estado --}}
        @if($gameweek->is_closed)
            <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                ✓ {{ __('Jornada completada - Puntos calculados') }}
            </div>
        @else
            <div class="px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-700 text-sm">
                ⏳ {{ __('Jornada en curso - Puntos parciales') }}
            </div>
        @endif
    </div>

    {{-- Titulares --}}
    <div class="mb-6">
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            {{ __('Titulares') }} ({{ $starters->count() }})
        </h4>
        
        <div class="space-y-2">
            @foreach($starters as $score)
                <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                            {{-- Posición --}}
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <span class="font-bold text-blue-700 text-sm">
                                    {{ $this->getPositionLabel($score->player->position) }}
                                </span>
                            </div>

                            {{-- Info del jugador --}}
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 flex items-center gap-2">
                                    {{ $score->player->known_as }}
                                    
                                    {{-- Badge Capitán --}}
                                    @if($score->is_captain)
                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-bold rounded">
                                            (C) x2
                                        </span>
                                    @elseif($score->is_vice_captain)
                                        <span class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs font-bold rounded">
                                            (VC)
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500 truncate">
                                    {{ $score->player->full_name }}
                                </div>
                            </div>

                            {{-- Puntos --}}
                            <div class="text-right mr-4">
                                <div class="text-2xl font-bold {{ $score->final_points > 0 ? 'text-green-600' : ($score->final_points < 0 ? 'text-red-600' : 'text-gray-400') }}">
                                    {{ $score->final_points }}
                                </div>
                                <div class="text-xs text-gray-500">{{ __('puntos') }}</div>
                                @if($score->is_captain && $score->base_points > 0)
                                    <div class="text-xs text-gray-400">({{ $score->base_points }} x2)</div>
                                @endif
                            </div>

                            {{-- Botón Breakdown --}}
                            <button wire:click="showBreakdown({{ $score->player_id }})"
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition text-sm font-medium">
                                {{ __('Ver Detalle') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Total Titulares --}}
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center justify-between">
                <span class="font-semibold text-gray-900">{{ __('Total Titulares:') }}</span>
                <span class="text-2xl font-bold text-blue-600">{{ $startersPoints }} {{ __('pts') }}</span>
            </div>
        </div>
    </div>

    {{-- Suplentes --}}
    <div>
        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            {{ __('Suplentes') }} ({{ $bench->count() }})
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
            @foreach($bench as $score)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <span class="font-bold text-gray-600 text-sm">
                            {{ $this->getPositionLabel($score->player->position) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-700 truncate">
                            {{ $score->player->known_as }}
                        </div>
                        <div class="text-xs text-gray-500 truncate">
                            {{ $score->player->full_name }}
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $score->base_points }} {{ __('pts') }}
                    </div>
                    <button wire:click="showBreakdown({{ $score->player_id }})"
                            class="px-2 py-1 bg-gray-200 text-gray-700 rounded text-xs hover:bg-gray-300 transition">
                        {{ __('Ver') }}
                    </button>
                </div>
            @endforeach
        </div>
        
        <div class="mt-3 text-sm text-gray-500 italic">
            * {{ __('Los suplentes no suman puntos al equipo') }}
        </div>
    </div>

    {{-- Modal de Breakdown --}}
    @if($showBreakdownModal && $selectedPlayerScore)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 p-4"
             wire:click="closeBreakdown">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                 wire:click.stop>
                {{-- Header --}}
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                            <span class="font-bold text-white text-sm">
                                {{ $this->getPositionLabel($selectedPlayerScore->player->position) }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">
                                {{ $selectedPlayerScore->player->known_as }}
                            </h3>
                            <p class="text-sm text-blue-100">
                                {{ $selectedPlayerScore->player->full_name }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeBreakdown" 
                            class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Contenido --}}
                <div class="p-6 max-h-[calc(90vh-120px)] overflow-y-auto">
                    {{-- Puntos Totales --}}
                    <div class="text-center mb-6 p-6 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                        <div class="text-5xl font-bold text-blue-600 mb-2">
                            {{ $selectedPlayerScore->final_points }}
                        </div>
                        <div class="text-sm text-gray-700 font-medium">
                            {{ __('Puntos Finales') }}
                            @if($selectedPlayerScore->is_captain)
                                <span class="ml-2 px-2 py-0.5 bg-yellow-200 text-yellow-800 text-xs font-bold rounded">
                                    (C) x2
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Breakdown de Puntos --}}
                    <div class="space-y-3">
                        <h4 class="font-semibold text-gray-900 mb-3">{{ __('Desglose de Puntos:') }}</h4>
                        
                        @php
                            $breakdown = $this->getFormattedBreakdown($selectedPlayerScore->breakdown);
                        @endphp

                        @if(count($breakdown) > 0)
                            @foreach($breakdown as $item)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <span class="text-gray-900 font-medium">{{ $item['label'] }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold {{ $item['points'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $item['points'] > 0 ? '+' : '' }}{{ $item['points'] }}
                                        </span>
                                        <span class="text-sm text-gray-500 ml-1">{{ __('pts') }}</span>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Base Points Total --}}
                            <div class="pt-3 border-t border-gray-200">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <span class="font-semibold text-gray-900">{{ __('Puntos Base:') }}</span>
                                    <span class="text-xl font-bold text-blue-600">{{ $selectedPlayerScore->base_points }}</span>
                                </div>
                            </div>

                            {{-- Captain Bonus si aplica --}}
                            @if($selectedPlayerScore->is_captain && $selectedPlayerScore->base_points > 0)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <span class="font-semibold text-gray-900 flex items-center gap-2">
                                        {{ __('Bonus Capitán (x2):') }}
                                        <span class="px-2 py-0.5 bg-yellow-200 text-yellow-800 text-xs font-bold rounded">C</span>
                                    </span>
                                    <span class="text-xl font-bold text-yellow-600">
                                        +{{ $selectedPlayerScore->base_points }}
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>{{ __('No hay desglose de puntos disponible') }}</p>
                                <p class="text-sm mt-1">{{ __('El jugador no participó o no sumó/restó puntos') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <button wire:click="closeBreakdown" 
                            class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        {{ __('Cerrar') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
<div>
    {{-- Loading State --}}
    @if($loading)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600">{{ __('Cargando detalle...') }}</p>
            </div>
        </div>
    @else
        {{-- Header --}}
        <div class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between mb-4">
                    <button wire:click="backToCalendar" 
                            class="flex items-center text-gray-600 hover:text-gray-900">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ __('Volver al calendario') }}
                    </button>
                    
                    <div class="text-sm text-gray-500">
                        Gameweek {{ $fixture->gameweek->number }} - {{ $fixture->gameweek->starts_at->format('d/m/Y') }}
                    </div>
                </div>

                {{-- Resultado del Partido --}}
                <div class="text-center">
                    @if($fixture->status === 1)
                        <div class="inline-flex px-4 py-2 rounded-full text-sm font-semibold mb-4 {{ $this->getResultBadgeClass() }}">
                            {{ $this->getResultText() }}
                        </div>
                    @else
                        <div class="inline-flex px-4 py-2 rounded-full text-sm font-semibold mb-4 bg-yellow-100 text-yellow-800">
                            {{ __('Partido Pendiente') }}
                        </div>
                    @endif

                    <div class="flex items-center justify-center gap-8 mt-4">
                        {{-- Mi Equipo --}}
                        <div class="text-center flex-1 max-w-xs">
                            <div class="text-2xl font-bold text-blue-600 mb-2">
                                {{ $myTeam->name }}
                            </div>
                            <div class="text-sm text-gray-600">{{ __('(TÚ)') }}</div>
                            <div class="text-sm text-gray-500 mt-2">
                                {{ $myTotalPoints }} {{ __('pts') }}
                            </div>
                        </div>

                        {{-- Marcador --}}
                        <div class="text-center">
                            @if($fixture->status === 1)
                                <div class="text-6xl font-bold text-gray-900">
                                    {{ $myGoals }} - {{ $opponentGoals }}
                                </div>
                                <div class="text-sm text-gray-500 mt-2">
                                    {{ __('Goles Fantasy') }}
                                </div>
                            @else
                                <div class="text-4xl font-bold text-gray-400">
                                    VS
                                </div>
                            @endif
                        </div>

                        {{-- Equipo Rival --}}
                        <div class="text-center flex-1 max-w-xs">
                            <div class="text-2xl font-bold text-gray-900 mb-2">
                                {{ $opponentTeam->name }}
                            </div>
                            <div class="text-sm text-gray-600">{{ $opponentTeam->user->name }}</div>
                            <div class="text-sm text-gray-500 mt-2">
                                {{ $opponentTotalPoints }} {{ __('pts') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mi Alineación --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        {{ __('Mi Alineación') }}
                    </h2>
                </div>

                <div class="p-6">
                    {{-- Titulares --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ __('Titulares') }} ({{ $myStarters->count() }})
                        </h3>
                        
                        <div class="space-y-2">
                            @foreach($myStarters as $score)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center gap-4 flex-1">
                                        {{-- Posición --}}
                                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="font-bold text-blue-700">
                                                {{ $this->getPositionLabel($score->player->position) }}
                                            </span>
                                        </div>

                                        {{-- Nombre del jugador --}}
                                        <div class="flex-1">
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
                                            <div class="text-sm text-gray-500">
                                                {{ $score->player->full_name }}
                                            </div>
                                        </div>

                                        {{-- Puntos --}}
                                        <div class="text-right">
                                            <div class="text-2xl font-bold {{ $score->final_points > 0 ? 'text-green-600' : ($score->final_points < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                                {{ $score->final_points }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ __('puntos') }}
                                            </div>
                                            @if($score->is_captain && $score->base_points > 0)
                                                <div class="text-xs text-gray-400">
                                                    ({{ $score->base_points }} x2)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Total Titulares --}}
                        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-900">{{ __('Total Titulares:') }}</span>
                                <span class="text-2xl font-bold text-blue-600">{{ $myTotalPoints }} {{ __('pts') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Suplentes --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ __('Suplentes') }} ({{ $myBench->count() }})
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            @foreach($myBench as $score)
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
                                    <div class="text-sm text-gray-400">
                                        {{ $score->base_points }} {{ __('pts') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-3 text-sm text-gray-500 italic">
                            * {{ __('Los suplentes no suman puntos al equipo') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alineación del Rival --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <button wire:click="toggleOpponentLineup" 
                        class="w-full bg-gray-50 hover:bg-gray-100 px-6 py-4 flex items-center justify-between transition">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        {{ __('Alineación de') }} {{ $opponentTeam->name }}
                    </h2>
                    <svg class="w-5 h-5 text-gray-600 transform transition-transform {{ $showOpponentLineup ? 'rotate-180' : '' }}" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                @if($showOpponentLineup)
                    <div class="p-6 border-t border-gray-200">
                        {{-- Titulares Rival --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                {{ __('Titulares') }} ({{ $opponentStarters->count() }})
                            </h3>
                            
                            <div class="space-y-2">
                                @foreach($opponentStarters as $score)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="font-bold text-gray-700">
                                                    {{ $this->getPositionLabel($score->player->position) }}
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900 flex items-center gap-2">
                                                    {{ $score->player->known_as }}
                                                    @if($score->is_captain)
                                                        <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs font-bold rounded">
                                                            (C) x2
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $score->player->full_name }}
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold {{ $score->final_points > 0 ? 'text-green-600' : 'text-gray-600' }}">
                                                    {{ $score->final_points }}
                                                </div>
                                                <div class="text-xs text-gray-500">{{ __('puntos') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">{{ __('Total:') }}</span>
                                    <span class="text-2xl font-bold text-gray-700">{{ $opponentTotalPoints }} {{ __('pts') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Suplentes Rival --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                {{ __('Suplentes') }} ({{ $opponentBench->count() }})
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($opponentBench as $score)
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="font-bold text-gray-600 text-sm">
                                                {{ $this->getPositionLabel($score->player->position) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-gray-700 truncate">{{ $score->player->known_as }}</div>
                                            <div class="text-xs text-gray-500">{{ $score->base_points }} {{ __('pts') }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
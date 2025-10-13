<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.real-teams.index', app()->getLocale()) }}" 
                       class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $realTeam->name }}</h1>
                        <p class="text-sm text-gray-500">{{ __('Equipo Real') }}</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.real-teams.edit', [app()->getLocale(), $realTeam]) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        {{ __('Editar Equipo') }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Información del Equipo --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Información') }}</h3>
                        
                        @if($realTeam->logo_url)
                            <div class="flex justify-center mb-4">
                                <img src="{{ $realTeam->logo_url }}" alt="{{ $realTeam->name }}" class="w-32 h-32 object-contain">
                            </div>
                        @endif

                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Nombre completo') }}</p>
                                <p class="font-medium">{{ $realTeam->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Nombre corto') }}</p>
                                <p class="font-medium">{{ $realTeam->short_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('País') }}</p>
                                <p class="font-medium">{{ $realTeam->country }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Año de fundación') }}</p>
                                <p class="font-medium">{{ $realTeam->founded_year ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Jugadores actuales') }}</p>
                                <p class="font-medium text-blue-600">{{ $realTeam->playerHistory->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Plantilla del Equipo --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Plantilla Actual') }}</h3>
                            <a href="{{ route('admin.real-teams.players.index', ['locale' => app()->getLocale(), 'realTeam' => $realTeam->id]) }}"
                                class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 text-sm">
                                Agregar jugador
                            </a>


                        </div>

                        @if($realTeam->playerHistory->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">{{ __('No hay jugadores en este equipo') }}</p>
                                <a href="{{ route('admin.players.create', app()->getLocale()) }}?team_id={{ $realTeam->id }}" 
                                   class="mt-3 inline-block text-blue-600 hover:text-blue-700">
                                    {{ __('Agregar el primer jugador') }} →
                                </a>
                            </div>
                        @else
                            {{-- Arqueros --}}
                            @if($playersByPosition->has(1))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-yellow-100 text-yellow-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">GK</span>
                                        {{ __('Arqueros') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition[1] as $history)
                                            @php
                                                $player = $history->player;
                                                $latestValuation = $player->valuations->first();
                                            @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $history->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->known_as ?? $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    @if($latestValuation)
                                                        <span class="text-sm text-gray-600">${{ number_format($latestValuation->market_value, 0) }}</span>
                                                    @endif
                                                    <a href="{{ route('admin.players.edit', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('Editar') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Defensores --}}
                            @if($playersByPosition->has(2))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">DF</span>
                                        {{ __('Defensores') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition[2] as $history)
                                            @php
                                                $player = $history->player;
                                                $latestValuation = $player->valuations->first();
                                            @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $history->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->known_as ?? $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    @if($latestValuation)
                                                        <span class="text-sm text-gray-600">${{ number_format($latestValuation->market_value, 0) }}</span>
                                                    @endif
                                                    <a href="{{ route('admin.players.edit', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('Editar') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Mediocampistas --}}
                            @if($playersByPosition->has(3))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">MF</span>
                                        {{ __('Mediocampistas') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition[3] as $history)
                                            @php
                                                $player = $history->player;
                                                $latestValuation = $player->valuations->first();
                                            @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $history->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->known_as ?? $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    @if($latestValuation)
                                                        <span class="text-sm text-gray-600">${{ number_format($latestValuation->market_value, 0) }}</span>
                                                    @endif
                                                    <a href="{{ route('admin.players.edit', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('Editar') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Delanteros --}}
                            @if($playersByPosition->has(4))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-red-100 text-red-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">FW</span>
                                        {{ __('Delanteros') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition[4] as $history)
                                            @php
                                                $player = $history->player;
                                                $latestValuation = $player->valuations->first();
                                            @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $history->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->known_as ?? $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    @if($latestValuation)
                                                        <span class="text-sm text-gray-600">${{ number_format($latestValuation->market_value, 0) }}</span>
                                                    @endif
                                                    <a href="{{ route('admin.players.edit', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('Editar') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
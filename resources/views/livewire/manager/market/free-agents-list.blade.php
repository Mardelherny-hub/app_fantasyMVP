<div>
    {{-- Filtros --}}
    <div class="mb-6 space-y-4">
        {{-- Búsqueda --}}
        <div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search players...') }}"
                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
        </div>

        {{-- Filtros de posición y orden --}}
        <div class="flex flex-wrap gap-2">
            {{-- Posiciones --}}
            <button 
                wire:click="setPosition(null)"
                class="px-4 py-2 rounded-lg font-medium transition @if(is_null($positionFilter)) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                {{ __('All') }}
            </button>
            <button 
                wire:click="setPosition({{ \App\Models\Player::POSITION_GK }})"
                class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 1) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                GK
            </button>
            <button 
                wire:click="setPosition({{ \App\Models\Player::POSITION_DF }})"
                class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 2) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                DF
            </button>
            <button 
                wire:click="setPosition({{ \App\Models\Player::POSITION_MF }})"
                class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 3) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                MF
            </button>
            <button 
                wire:click="setPosition({{ \App\Models\Player::POSITION_FW }})"
                class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 4) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                FW
            </button>

            {{-- Separador --}}
            <div class="border-l border-slate-600 mx-2"></div>

            {{-- Ordenamiento --}}
            <button 
                wire:click="setSorting('price')"
                class="px-4 py-2 rounded-lg font-medium transition flex items-center space-x-1 @if($sortBy === 'price') bg-blue-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                <span>{{ __('Price') }}</span>
                @if($sortBy === 'price')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($sortDirection === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        @endif
                    </svg>
                @endif
            </button>
            <button 
                wire:click="setSorting('name')"
                class="px-4 py-2 rounded-lg font-medium transition flex items-center space-x-1 @if($sortBy === 'name') bg-blue-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                <span>{{ __('Name') }}</span>
                @if($sortBy === 'name')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($sortDirection === 'asc')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        @endif
                    </svg>
                @endif
            </button>
        </div>
    </div>

    {{-- Grid de jugadores --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        @forelse($players as $player)
            @php
                $marketValue = $player->marketValue($team->league->season_id) ?? 0.50;
                $commission = $marketValue * 0.05;
                $totalCost = $marketValue + $commission;
                $canAfford = $team->budget >= $totalCost;
            @endphp
            
            <div class="bg-slate-700 border border-slate-600 rounded-lg p-4 hover:border-emerald-500 transition">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="font-bold text-white truncate">{{ $player->known_as ?: $player->full_name }}</h3>
                        <p class="text-xs text-gray-400">{{ $player->position_name }}</p>
                    </div>
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-300 text-xs font-bold rounded">
                        {{ __('Free') }}
                    </span>
                </div>

                {{-- Precios --}}
                <div class="mb-4 space-y-1 text-sm">
                    <div class="flex justify-between text-gray-300">
                        <span>{{ __('Value') }}:</span>
                        <span class="font-semibold">${{ number_format($marketValue, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>{{ __('+ Fee') }} (5%):</span>
                        <span>${{ number_format($commission, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-600 pt-1 text-white">
                        <span class="font-bold">{{ __('Total') }}:</span>
                        <span class="font-bold text-lg">${{ number_format($totalCost, 2) }}</span>
                    </div>
                </div>

                {{-- Botón --}}
                <button 
                    wire:click="buyPlayer({{ $player->id }})"
                    wire:loading.attr="disabled"
                    @if(!$canAfford || !$marketOpen) disabled @endif
                    class="w-full py-2 px-4 rounded-lg font-semibold transition
                        @if($canAfford && $marketOpen)
                            bg-emerald-600 hover:bg-emerald-500 text-white
                        @else
                            bg-gray-600 text-gray-400 cursor-not-allowed
                        @endif
                    "
                >
                    <span wire:loading.remove wire:target="buyPlayer({{ $player->id }})">
                        @if(!$canAfford)
                            {{ __('Insufficient Budget') }}
                        @elseif(!$marketOpen)
                            {{ __('Market Closed') }}
                        @else
                            {{ __('Sign Player') }}
                        @endif
                    </span>
                    <span wire:loading wire:target="buyPlayer({{ $player->id }})">
                        {{ __('Processing...') }}
                    </span>
                </button>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-400 text-lg">{{ __('No players found') }}</p>
                @if($search || $positionFilter)
                    <button 
                        wire:click="$set('search', ''); $set('positionFilter', null)"
                        class="mt-4 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-gray-300 rounded-lg transition"
                    >
                        {{ __('Clear filters') }}
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Paginación --}}
    <div>
        {{ $players->links() }}
    </div>
</div>
<div>
    @if($availablePlayers->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-400 mb-2">{{ __('No players available to list') }}</h3>
            <p class="text-gray-500">{{ __('You can only sell bench players who are not already listed.') }}</p>
        </div>
    @else
        <div class="max-w-2xl mx-auto">
            <div class="bg-slate-700 border border-slate-600 rounded-lg p-6">
                <h3 class="text-xl font-bold text-white mb-6">{{ __('List Player for Sale') }}</h3>

                {{-- Selección de jugador --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Select Player') }} *</label>
                    <select 
                        wire:model.live="selectedPlayerId"
                        class="w-full px-4 py-3 bg-slate-800 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    >
                        <option value="">{{ __('Choose a player...') }}</option>
                        @foreach($availablePlayers as $player)
                            <option value="{{ $player->id }}">
                                {{ $player->display_name }} - {{ $player->position_name }}
                                ({{ __('Value') }}: ${{ number_format($player->marketValue($team->league->season_id) ?? 0.50, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('selectedPlayerId') 
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                @if($selectedPlayerId)
                    @php
                        $selectedPlayer = $availablePlayers->firstWhere('id', $selectedPlayerId);
                        $marketValue = $selectedPlayer->marketValue($team->league->season_id) ?? 0.50;
                        $minPrice = $marketValue * 0.5;
                        $maxPrice = $marketValue * 3.0;
                    @endphp

                    {{-- Info del jugador --}}
                    <div class="mb-6 bg-slate-800 rounded-lg p-4 space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">{{ __('Market Value') }}:</span>
                            <span class="text-white font-semibold">${{ number_format($marketValue, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">{{ __('Suggested Price') }} (+5%):</span>
                            <span class="text-emerald-400 font-bold">${{ number_format($suggestedPrice, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>{{ __('Price Range') }}:</span>
                            <span>${{ number_format($minPrice, 2) }} - ${{ number_format($maxPrice, 2) }}</span>
                        </div>
                    </div>

                    {{-- Input de precio --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Asking Price') }} *</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                            <input 
                                type="number" 
                                wire:model.live="price"
                                step="0.01"
                                min="{{ $minPrice }}"
                                max="{{ $maxPrice }}"
                                class="w-full pl-8 pr-4 py-3 bg-slate-800 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                        </div>
                        @error('price') 
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-400">
                            {{ __('You can set a price between 50% and 300% of market value.') }}
                        </p>
                    </div>

                    {{-- Preview de ganancias --}}
                    @if($price > 0)
                        <div class="mb-6 bg-emerald-500/10 border border-emerald-500/30 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-emerald-400 mb-3">{{ __('Expected Earnings') }}</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-gray-300">
                                    <span>{{ __('Sale Price') }}:</span>
                                    <span class="font-semibold">${{ number_format($price, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-emerald-400">
                                    <span class="font-bold">{{ __('You will receive') }}:</span>
                                    <span class="font-bold text-lg">${{ number_format($price, 2) }}</span>
                                </div>
                            </div>
                            <p class="mt-3 text-xs text-gray-400">
                                <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('The buyer will pay an additional 5% fee.') }}
                            </p>
                        </div>
                    @endif

                    {{-- Botón --}}
                    <div class="flex gap-3">
                        <button 
                            wire:click="$set('selectedPlayerId', null)"
                            class="flex-1 py-3 px-4 bg-slate-600 hover:bg-slate-500 text-white font-semibold rounded-lg transition"
                        >
                            {{ __('Cancel') }}
                        </button>
                        <button 
                            wire:click="createListing"
                            wire:loading.attr="disabled"
                            @if(!$marketOpen) disabled @endif
                            class="flex-1 py-3 px-4 font-semibold rounded-lg transition
                                @if($marketOpen)
                                    bg-emerald-600 hover:bg-emerald-500 text-white
                                @else
                                    bg-gray-600 text-gray-400 cursor-not-allowed
                                @endif
                            "
                        >
                            <span wire:loading.remove wire:target="createListing">
                                @if(!$marketOpen)
                                    {{ __('Market Closed') }}
                                @else
                                    {{ __('List for Sale') }}
                                @endif
                            </span>
                            <span wire:loading wire:target="createListing">{{ __('Processing...') }}</span>
                        </button>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                        </svg>
                        <p>{{ __('Select a player to continue') }}</p>
                    </div>
                @endif
            </div>

            {{-- Ayuda --}}
            <div class="mt-6 bg-blue-500/10 border border-blue-500/30 rounded-lg p-4">
                <h4 class="flex items-center text-sm font-semibold text-blue-400 mb-2">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('Listing Tips') }}
                </h4>
                <ul class="space-y-1 text-sm text-gray-400">
                    <li>• {{ __('Only bench players can be listed') }}</li>
                    <li>• {{ __('Players in your starting lineup cannot be sold') }}</li>
                    <li>• {{ __('Suggested price is 5% above market value') }}</li>
                    <li>• {{ __('Other managers can make offers on your listing') }}</li>
                </ul>
            </div>
        </div>
    @endif
</div>
<div>
    {{-- Filtros --}}
    <div class="mb-6 space-y-4">
        <div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="{{ __('Search players...') }}"
                class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
            >
        </div>

        <div class="flex flex-wrap gap-2">
            <button 
                wire:click="setPosition(null)"
                class="px-4 py-2 rounded-lg font-medium transition @if(is_null($positionFilter)) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif"
            >
                {{ __('All') }}
            </button>
            <button wire:click="setPosition(1)" class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 1) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif">GK</button>
            <button wire:click="setPosition(2)" class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 2) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif">DF</button>
            <button wire:click="setPosition(3)" class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 3) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif">MF</button>
            <button wire:click="setPosition(4)" class="px-4 py-2 rounded-lg font-medium transition @if($positionFilter === 4) bg-emerald-600 text-white @else bg-slate-700 text-gray-300 hover:bg-slate-600 @endif">FW</button>
        </div>
    </div>

    {{-- Grid de listings --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-6">
        @forelse($listings as $listing)
            @php
                $commission = $listing->price * 0.05;
                $totalCost = $listing->price + $commission;
                $canAfford = $team->budget >= $totalCost;
            @endphp
            
            <div class="bg-slate-700 border border-slate-600 rounded-lg p-4 hover:border-purple-500 transition">
                {{-- Header --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <h3 class="font-bold text-white truncate">{{ $listing->player->known_as ?: $listing->player->full_name }}</h3>
                        <p class="text-xs text-gray-400">{{ $listing->player->position_name }}</p>
                    </div>
                    <span class="px-2 py-1 bg-purple-500/20 text-purple-300 text-xs font-bold rounded">
                        {{ __('Listed') }}
                    </span>
                </div>

                {{-- Seller --}}
                <div class="mb-3 text-xs text-gray-400">
                    {{ __('Seller') }}: <span class="text-white font-medium">{{ $listing->fantasyTeam->name }}</span>
                </div>

                {{-- Precios --}}
                <div class="mb-4 space-y-1 text-sm">
                    <div class="flex justify-between text-gray-300">
                        <span>{{ __('Asking Price') }}:</span>
                        <span class="font-semibold">${{ number_format($listing->price, 2) }}</span>
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
                    wire:click="openOfferModal({{ $listing->id }})"
                    @if(!$marketOpen) disabled @endif
                    class="w-full py-2 px-4 rounded-lg font-semibold transition
                        @if($marketOpen)
                            bg-purple-600 hover:bg-purple-500 text-white
                        @else
                            bg-gray-600 text-gray-400 cursor-not-allowed
                        @endif
                    "
                >
                    @if(!$marketOpen)
                        {{ __('Market Closed') }}
                    @else
                        {{ __('Make Offer') }}
                    @endif
                </button>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-gray-400 text-lg">{{ __('No players listed for sale') }}</p>
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
        {{ $listings->links() }}
    </div>

    {{-- Modal de oferta --}}
    @if($showOfferModal && $selectedListingId)
        @php
            $listing = $listings->firstWhere('id', $selectedListingId);
        @endphp
        
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm" wire:click.self="closeOfferModal">
            <div class="bg-slate-800 border-2 border-slate-700 rounded-2xl max-w-md w-full shadow-2xl" @click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b border-slate-700">
                    <div>
                        <h3 class="text-xl font-black text-white">{{ __('Make an Offer') }}</h3>
                        <p class="text-sm text-gray-400 mt-1">{{ $listing->player->display_name }}</p>
                    </div>
                    <button wire:click="closeOfferModal" class="text-gray-400 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-4">
                    {{-- Info del listing --}}
                    <div class="bg-slate-700 rounded-lg p-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('Seller') }}:</span>
                            <span class="text-white font-medium">{{ $listing->fantasyTeam->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('Asking Price') }}:</span>
                            <span class="text-white font-bold">${{ number_format($listing->price, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ __('Your Budget') }}:</span>
                            <span class="text-emerald-400 font-bold">${{ number_format($team->budget, 2) }}</span>
                        </div>
                    </div>

                    {{-- Input de oferta --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">{{ __('Your Offer') }}</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                            <input 
                                type="number" 
                                wire:model.live="offerPrice"
                                step="0.01"
                                min="0.50"
                                class="w-full pl-8 pr-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
                            >
                        </div>
                        @error('offerPrice') 
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Cálculo total --}}
                    @if($offerPrice > 0)
                        @php
                            $offerCommission = $offerPrice * 0.05;
                            $offerTotal = $offerPrice + $offerCommission;
                        @endphp
                        <div class="bg-slate-700 rounded-lg p-4 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-300">
                                <span>{{ __('Offer') }}:</span>
                                <span>${{ number_format($offerPrice, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-400">
                                <span>{{ __('+ Fee') }} (5%):</span>
                                <span>${{ number_format($offerCommission, 2) }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-600 pt-2 text-white">
                                <span class="font-bold">{{ __('Total Cost') }}:</span>
                                <span class="font-bold text-lg">${{ number_format($offerTotal, 2) }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                <div class="flex gap-3 p-6 border-t border-slate-700">
                    <button 
                        wire:click="closeOfferModal"
                        class="flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition"
                    >
                        {{ __('Cancel') }}
                    </button>
                    <button 
                        wire:click="makeOffer"
                        wire:loading.attr="disabled"
                        class="flex-1 py-3 px-4 bg-purple-600 hover:bg-purple-500 text-white font-semibold rounded-lg transition disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="makeOffer">{{ __('Send Offer') }}</span>
                        <span wire:loading wire:target="makeOffer">{{ __('Sending...') }}</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
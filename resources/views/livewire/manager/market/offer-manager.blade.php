<div>
    {{-- Tabs --}}
    <div class="mb-6 border-b border-slate-700">
        <nav class="flex space-x-2">
            <button 
                wire:click="switchTab('received')"
                class="px-4 py-3 border-b-2 font-medium transition @if($tab === 'received') border-emerald-500 text-emerald-400 @else border-transparent text-gray-400 hover:text-gray-300 @endif"
            >
                {{ __('Received Offers') }}
                @if($tab !== 'received')
                    @php
                        $receivedCount = \App\Models\Offer::whereHas('listing', fn($q) => $q->where('fantasy_team_id', $team->id))
                            ->where('status', 0)
                            ->count();
                    @endphp
                    @if($receivedCount > 0)
                        <span class="ml-2 px-2 py-0.5 bg-emerald-500 text-white text-xs rounded-full">{{ $receivedCount }}</span>
                    @endif
                @endif
            </button>
            <button 
                wire:click="switchTab('sent')"
                class="px-4 py-3 border-b-2 font-medium transition @if($tab === 'sent') border-emerald-500 text-emerald-400 @else border-transparent text-gray-400 hover:text-gray-300 @endif"
            >
                {{ __('Sent Offers') }}
                @if($tab !== 'sent')
                    @php
                        $sentCount = \App\Models\Offer::where('buyer_fantasy_team_id', $team->id)
                            ->where('status', 0)
                            ->count();
                    @endphp
                    @if($sentCount > 0)
                        <span class="ml-2 px-2 py-0.5 bg-emerald-500 text-white text-xs rounded-full">{{ $sentCount }}</span>
                    @endif
                @endif
            </button>
        </nav>
    </div>

    {{-- Estado de carga --}}
    <div wire:loading.delay class="mb-4">
        <div class="bg-slate-700 border border-slate-600 rounded-lg p-4 text-center">
            <div class="flex items-center justify-center gap-3">
                <svg class="animate-spin h-5 w-5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-300">{{ __('Processing...') }}</span>
            </div>
        </div>
    </div>

    {{-- Lista vacía --}}
    @if($offers->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400 text-lg">
                @if($tab === 'received')
                    {{ __('No received offers') }}
                @else
                    {{ __('No sent offers') }}
                @endif
            </p>
            <p class="text-gray-500 text-sm mt-2">
                @if($tab === 'received')
                    {{ __('Offers from other managers will appear here.') }}
                @else
                    {{ __('Your sent offers will appear here.') }}
                @endif
            </p>
        </div>
    @else
        {{-- Lista de ofertas --}}
        <div class="space-y-4">
            @foreach($offers as $offer)
                @php
                    $statusConfig = [
                        0 => ['label' => __('Pending'), 'color' => 'yellow'],
                        1 => ['label' => __('Accepted'), 'color' => 'emerald'],
                        2 => ['label' => __('Rejected'), 'color' => 'red'],
                        3 => ['label' => __('Expired'), 'color' => 'gray'],
                    ];
                    $status = $statusConfig[$offer->status] ?? $statusConfig[0];
                    $commission = $offer->offered_price * 0.05;
                    $totalCost = $offer->offered_price + $commission;
                @endphp

                <div class="bg-slate-700 border border-slate-600 rounded-lg p-4 hover:border-slate-500 transition">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-bold text-white">{{ $offer->listing->player->display_name }}</h3>
                                <span class="px-2 py-1 bg-{{ $status['color'] }}-500/20 text-{{ $status['color'] }}-300 text-xs font-bold rounded">
                                    {{ $status['label'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400">
                                {{ $offer->listing->player->position_name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($tab === 'received')
                                    {{ __('From') }}: <span class="text-gray-400 font-medium">{{ $offer->buyerTeam->name }}</span>
                                @else
                                    {{ __('To') }}: <span class="text-gray-400 font-medium">{{ $offer->listing->fantasyTeam->name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Detalles de precio --}}
                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <span class="text-gray-400">{{ __('Asking Price') }}:</span>
                            <span class="text-white ml-2 font-medium">${{ number_format($offer->listing->price, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">{{ __('Offered') }}:</span>
                            <span class="text-emerald-400 font-bold ml-2">${{ number_format($offer->offered_price, 2) }}</span>
                        </div>
                        @if($tab === 'sent')
                            <div>
                                <span class="text-gray-400">{{ __('+ Fee') }} (5%):</span>
                                <span class="text-orange-400 ml-2">${{ number_format($commission, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400">{{ __('Total Cost') }}:</span>
                                <span class="text-white font-bold ml-2">${{ number_format($totalCost, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Fecha --}}
                    <div class="text-xs text-gray-500 mb-3">
                        {{ __('Created') }}: {{ $offer->created_at->diffForHumans() }}
                    </div>

                    {{-- Acciones --}}
                    @if($offer->status === 0)
                        @if($tab === 'received')
                            {{-- Acciones para vendedor --}}
                            <div class="flex gap-2">
                                <button 
                                    wire:click="acceptOffer({{ $offer->id }})"
                                    wire:loading.attr="disabled"
                                    class="flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="acceptOffer({{ $offer->id }})">✓ {{ __('Accept') }}</span>
                                    <span wire:loading wire:target="acceptOffer({{ $offer->id }})">{{ __('Processing...') }}</span>
                                </button>
                                <button 
                                    wire:click="rejectOffer({{ $offer->id }})"
                                    wire:loading.attr="disabled"
                                    class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="rejectOffer({{ $offer->id }})">✕ {{ __('Reject') }}</span>
                                    <span wire:loading wire:target="rejectOffer({{ $offer->id }})">{{ __('Processing...') }}</span>
                                </button>
                            </div>
                        @else
                            {{-- Acciones para comprador --}}
                            <button 
                                wire:click="cancelOffer({{ $offer->id }})"
                                wire:loading.attr="disabled"
                                class="w-full px-4 py-2 bg-gray-600 hover:bg-gray-500 text-white font-semibold rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span wire:loading.remove wire:target="cancelOffer({{ $offer->id }})">{{ __('Cancel Offer') }}</span>
                                <span wire:loading wire:target="cancelOffer({{ $offer->id }})">{{ __('Canceling...') }}</span>
                            </button>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
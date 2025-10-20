<div>
    <div class="mb-6 border-b border-slate-700">
        <nav class="flex space-x-2">
            <button 
                wire:click="switchTab('received')"
                class="px-4 py-3 border-b-2 font-medium transition @if($tab === 'received') border-emerald-500 text-emerald-400 @else border-transparent text-gray-400 hover:text-gray-300 @endif"
            >
                {{ __('Received Offers') }}
            </button>
            <button 
                wire:click="switchTab('sent')"
                class="px-4 py-3 border-b-2 font-medium transition @if($tab === 'sent') border-emerald-500 text-emerald-400 @else border-transparent text-gray-400 hover:text-gray-300 @endif"
            >
                {{ __('Sent Offers') }}
            </button>
        </nav>
    </div>

    @if($offers->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-gray-400 text-lg">{{ __('No offers') }}</p>
        </div>
    @else
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

                <div class="bg-slate-700 border border-slate-600 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-bold text-white">{{ $offer->listing->player->display_name }}</h3>
                                <span class="px-2 py-1 bg-{{ $status['color'] }}-500/20 text-{{ $status['color'] }}-300 text-xs font-bold rounded">
                                    {{ $status['label'] }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mb-2">
                                @if($tab === 'received')
                                    {{ __('From') }}: {{ $offer->buyerTeam->name }}
                                @else
                                    {{ __('To') }}: {{ $offer->listing->fantasyTeam->name }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <span class="text-gray-400">{{ __('Asking Price') }}:</span>
                            <span class="text-white ml-2">${{ number_format($offer->listing->price, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400">{{ __('Offered') }}:</span>
                            <span class="text-emerald-400 font-bold ml-2">${{ number_format($offer->offered_price, 2) }}</span>
                        </div>
                        @if($tab === 'sent')
                            <div>
                                <span class="text-gray-400">{{ __('+ Fee') }}:</span>
                                <span class="text-white ml-2">${{ number_format($commission, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-400">{{ __('Total') }}:</span>
                                <span class="text-white font-bold ml-2">${{ number_format($totalCost, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    @if($offer->status === 0 && $tab === 'received')
                        <div class="flex gap-2">
                            <button 
                                wire:click="acceptOffer({{ $offer->id }})"
                                wire:loading.attr="disabled"
                                class="flex-1 py-2 px-4 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg transition"
                            >
                                <span wire:loading.remove wire:target="acceptOffer({{ $offer->id }})">{{ __('Accept') }}</span>
                                <span wire:loading wire:target="acceptOffer({{ $offer->id }})">...</span>
                            </button>
                            <button 
                                wire:click="rejectOffer({{ $offer->id }})"
                                wire:loading.attr="disabled"
                                class="flex-1 py-2 px-4 bg-red-600 hover:bg-red-500 text-white font-semibold rounded-lg transition"
                            >
                                <span wire:loading.remove wire:target="rejectOffer({{ $offer->id }})">{{ __('Reject') }}</span>
                                <span wire:loading wire:target="rejectOffer({{ $offer->id }})">...</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
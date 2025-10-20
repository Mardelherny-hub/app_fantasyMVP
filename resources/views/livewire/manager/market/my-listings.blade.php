<div>
    @if($listings->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-400 text-lg">{{ __('No active listings') }}</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($listings as $listing)
                @php
                    $statusConfig = [
                        0 => ['label' => __('Active'), 'color' => 'emerald'],
                        1 => ['label' => __('Sold'), 'color' => 'blue'],
                        2 => ['label' => __('Withdrawn'), 'color' => 'gray'],
                        3 => ['label' => __('Expired'), 'color' => 'red'],
                    ];
                    $status = $statusConfig[$listing->status] ?? $statusConfig[0];
                    $offersCount = $listing->offers()->where('status', 0)->count();
                @endphp

                <div class="bg-slate-700 border border-slate-600 rounded-lg p-4 hover:border-slate-500 transition">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-bold text-white">{{ $listing->player->display_name }}</h3>
                                <span class="px-2 py-1 bg-{{ $status['color'] }}-500/20 text-{{ $status['color'] }}-300 text-xs font-bold rounded">
                                    {{ $status['label'] }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mb-3">{{ $listing->player->position_name }}</p>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-400">{{ __('Asking Price') }}:</span>
                                    <span class="text-white font-bold ml-2">${{ number_format($listing->price, 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-400">{{ __('Pending Offers') }}:</span>
                                    <span class="text-white font-bold ml-2">{{ $offersCount }}</span>
                                </div>
                            </div>
                        </div>

                        @if($listing->status === 0)
                            <button 
                                wire:click="withdrawListing({{ $listing->id }})"
                                wire:loading.attr="disabled"
                                class="ml-4 px-4 py-2 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold rounded-lg transition"
                            >
                                <span wire:loading.remove wire:target="withdrawListing({{ $listing->id }})">{{ __('Withdraw') }}</span>
                                <span wire:loading wire:target="withdrawListing({{ $listing->id }})">...</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
<div>
    @if($transfers->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 text-lg">{{ __('No transfer history') }}</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($transfers as $transfer)
                @php
                    $isBuy = $transfer->to_fantasy_team_id === $team->id;
                    $isSell = $transfer->from_fantasy_team_id === $team->id;
                    $isFreeAgent = is_null($transfer->from_fantasy_team_id);
                @endphp

                <div class="bg-slate-700 border border-slate-600 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-white">{{ $transfer->player->display_name }}</h3>
                            @if($isBuy)
                                <span class="px-2 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-bold rounded">
                                    {{ __('IN') }}
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-500/20 text-red-300 text-xs font-bold rounded">
                                    {{ __('OUT') }}
                                </span>
                            @endif
                        </div>
                        <span class="text-white font-bold">${{ number_format($transfer->price, 2) }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-400">
                        <div>
                            @if($isFreeAgent)
                                {{ __('From') }}: <span class="text-blue-400">{{ __('Free Agent') }}</span>
                            @else
                                {{ __('From') }}: {{ $transfer->fromTeam->name ?? __('Free Agent') }}
                                <span class="mx-2">→</span>
                                {{ __('To') }}: {{ $transfer->toTeam->name }}
                            @endif
                        </div>
                        <span>{{ $transfer->effective_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        @if($transfers->count() >= $limit)
            <div class="mt-6 text-center">
                <button 
                    wire:click="loadMore"
                    class="px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold rounded-lg transition"
                >
                    {{ __('Load More') }}
                </button>
            </div>
        @endif
    @endif
</div>
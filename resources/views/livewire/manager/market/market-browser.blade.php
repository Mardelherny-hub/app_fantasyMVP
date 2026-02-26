<div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header del mercado --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-black text-white mb-2">{{ __('Transfer Market') }}</h1>
                    <p class="text-gray-400">{{ $team->name }}</p>
                </div>
                
                <button 
                    wire:click="refreshMarket"
                    class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>{{ __('Refresh') }}</span>
                </button>
            </div>

            {{-- Estado del mercado --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Presupuesto --}}
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">{{ __('Budget') }}</p>
                            <p class="text-2xl font-black text-emerald-400">${{ number_format($availableBudget, 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Transferencias --}}
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">{{ __('Transfers') }}</p>
                            <p class="text-2xl font-black text-white">{{ $transfersRemaining }}/{{ $transfersLimit }}</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Estado del mercado --}}
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">{{ __('Market Status') }}</p>
                            <p class="text-lg font-black @if($marketOpen) text-emerald-400 @else text-red-400 @endif">
                                @if($marketOpen)
                                    {{ __('OPEN') }}
                                @else
                                    {{ __('CLOSED') }}
                                @endif
                            </p>
                        </div>
                        <div class="w-12 h-12 @if($marketOpen) bg-emerald-500/20 @else bg-red-500/20 @endif rounded-full flex items-center justify-center">
                            @if($marketOpen)
                                <svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Gameweek actual --}}
                <div class="bg-slate-800 border border-slate-700 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">{{ __('Gameweek') }}</p>
                            <p class="text-2xl font-black text-white">{{ $currentGameweek?->number ?? '-' }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Mensajes --}}
        @if($successMessage)
            <div class="mb-6 p-4 bg-emerald-500/20 border border-emerald-500/50 rounded-lg flex items-center space-x-3">
                <svg class="w-6 h-6 text-emerald-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-emerald-200">{{ $successMessage }}</p>
            </div>
        @endif

        @if($errorMessage)
            <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg flex items-center space-x-3">
                <svg class="w-6 h-6 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <p class="text-red-200">{{ $errorMessage }}</p>
            </div>
        @endif

        {{-- Sistema de tabs --}}
        <div class="mb-6">
            <div class="border-b border-slate-700">
                <nav class="-mb-px flex space-x-2 overflow-x-auto">
                    @foreach($tabs as $tabKey => $tabLabel)
                        <button 
                            wire:click="switchTab('{{ $tabKey }}')"
                            class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm transition
                                @if($activeTab === $tabKey)
                                    border-emerald-500 text-emerald-400
                                @else
                                    border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300
                                @endif
                            "
                        >
                            {{ __($tabLabel) }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Contenido del tab activo --}}
        <div class="bg-slate-800 border border-slate-700 rounded-lg p-6">
            @if(!$marketOpen && $activeTab !== 'history')
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-gray-400 mb-2">{{ __('Market is Closed') }}</h3>
                    <p class="text-gray-500">{{ __('The transfer market will open before the next gameweek starts.') }}</p>
                </div>
            @elseif(!$canTransfer && $activeTab !== 'history' && $activeTab !== 'my-listings')
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-yellow-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h3 class="text-xl font-bold text-yellow-400 mb-2">{{ __('Transfer Limit Reached') }}</h3>
                    <p class="text-gray-500">{{ __('You have used all your transfers for this gameweek (:limit).', ['limit' => $transfersLimit]) }}</p>
                </div>
            @else
                {{-- Tabs dinámicos (lazy loading) --}}
                <div wire:key="tab-{{ $activeTab }}">
                    @if($activeTab === 'free-agents')
                        <livewire:manager.market.free-agents-list 
                            :teamId="$childProps['team']->id" 
                            :gameweek="$childProps['gameweek']" 
                            :marketOpen="$childProps['marketOpen']" 
                            :key="'free-agents-' . $childProps['team']->id" 
                        />
                    @elseif($activeTab === 'market-listings')
                        <livewire:manager.market.market-listings 
                            :team="$childProps['team']" 
                            :gameweek="$childProps['gameweek']" 
                            :marketOpen="$childProps['marketOpen']" 
                            :key="'market-listings-' . $childProps['team']->id" 
                        />
                        <div class="text-center py-12 text-gray-400">
                            <p>{{ __('Market Listings component will load here') }}</p>
                            <p class="text-sm mt-2">{{ __('Component: MarketListings') }}</p>
                        </div>
                    @elseif($activeTab === 'create-listing')
                        <livewire:manager.market.create-listing 
                            :team="$childProps['team']" 
                            :gameweek="$childProps['gameweek']" 
                            :marketOpen="$childProps['marketOpen']" 
                            :key="'create-listing-' . $childProps['team']->id" 
                        />
                    @elseif($activeTab === 'my-listings')
                        <livewire:manager.market.my-listings 
                            :team="$childProps['team']" 
                            :gameweek="$childProps['gameweek']" 
                            :marketOpen="$childProps['marketOpen']" 
                            :key="'my-listings-' . $childProps['team']->id" 
                        />
                    @elseif($activeTab === 'offers')
                        <livewire:manager.market.offer-manager 
                            :team="$childProps['team']" 
                            :gameweek="$childProps['gameweek']" 
                            :marketOpen="$childProps['marketOpen']" 
                            :key="'offer-manager-' . $childProps['team']->id" 
                        />
                    @elseif($activeTab === 'history')
                        @php
                            $historyTransfers = \App\Models\Transfer::where('league_id', $childProps['team']->league_id)
                                ->where(function($q) use ($childProps) {
                                    $q->where('to_fantasy_team_id', $childProps['team']->id)
                                    ->orWhere('from_fantasy_team_id', $childProps['team']->id);
                                })
                                ->with(['player', 'fromTeam', 'toTeam'])
                                ->orderBy('effective_at', 'desc')
                                ->limit(20)
                                ->get();
                        @endphp
                        
                        <div>
                            @if($historyTransfers->isEmpty())
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-gray-400 text-lg">{{ __('No transfer history') }}</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($historyTransfers as $transfer)
                                        @php
                                            $isBuy = $transfer->to_fantasy_team_id === $childProps['team']->id;
                                            $isFreeAgent = is_null($transfer->from_fantasy_team_id);
                                        @endphp

                                        <div class="bg-slate-700 border border-slate-600 rounded-lg p-4">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-3">
                                                    <h3 class="font-bold text-white">{{ $transfer->player->display_name }}</h3>
                                                    @if($isBuy)
                                                        <span class="px-2 py-1 bg-emerald-500/20 text-emerald-300 text-xs font-bold rounded">
                                                            ↓ IN
                                                        </span>
                                                    @else
                                                        <span class="px-2 py-1 bg-red-500/20 text-red-300 text-xs font-bold rounded">
                                                            ↑ OUT
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-white font-bold">${{ number_format($transfer->price, 2) }}</span>
                                            </div>

                                            <div class="flex items-center justify-between text-sm text-gray-400">
                                                <div>
                                                    @if($isFreeAgent)
                                                        <span class="text-blue-400">Free Agent</span> → {{ $transfer->toTeam->name }}
                                                    @elseif($isBuy)
                                                        {{ $transfer->fromTeam->name ?? 'Unknown' }} → <span class="text-emerald-400">{{ $transfer->toTeam->name }}</span>
                                                    @else
                                                        <span class="text-red-400">{{ $transfer->fromTeam->name }}</span> → {{ $transfer->toTeam->name }}
                                                    @endif
                                                </div>
                                                <span class="text-xs">{{ $transfer->effective_at->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
<div>
    {{-- Period Tabs --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-2 mb-6 shadow-xl">
        <div class="flex flex-wrap gap-2">
            <button wire:click="setPeriod('all_time')"
                    class="flex-1 min-w-[100px] px-4 py-3 rounded-xl font-semibold text-sm transition-all
                        {{ $period === 'all_time' 
                            ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 shadow-lg' 
                            : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                <span class="block sm:hidden">🏆</span>
                <span class="hidden sm:block">🏆 {{ __('All Time') }}</span>
            </button>
            <button wire:click="setPeriod('monthly')"
                    class="flex-1 min-w-[100px] px-4 py-3 rounded-xl font-semibold text-sm transition-all
                        {{ $period === 'monthly' 
                            ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 shadow-lg' 
                            : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                <span class="block sm:hidden">📅</span>
                <span class="hidden sm:block">📅 {{ __('This Month') }}</span>
            </button>
            <button wire:click="setPeriod('weekly')"
                    class="flex-1 min-w-[100px] px-4 py-3 rounded-xl font-semibold text-sm transition-all
                        {{ $period === 'weekly' 
                            ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 shadow-lg' 
                            : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                <span class="block sm:hidden">📆</span>
                <span class="hidden sm:block">📆 {{ __('This Week') }}</span>
            </button>
        </div>
    </div>

    {{-- User Position Card --}}
    @if($userPosition > 0)
        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 backdrop-blur-xl border border-blue-500/30 rounded-2xl p-5 sm:p-6 mb-6 shadow-xl">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center sm:text-left">
                    <p class="text-xs sm:text-sm text-gray-400 mb-2">{{ __('Your Position') }}</p>
                    <div class="flex items-center justify-center sm:justify-start gap-3">
                        <span class="text-4xl sm:text-5xl font-black text-white">#{{ $userPosition }}</span>
                        @if($userPosition <= 3)
                            <span class="text-3xl sm:text-4xl">
                                @if($userPosition == 1) 🥇
                                @elseif($userPosition == 2) 🥈
                                @else 🥉
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Score') }}</p>
                        <p class="text-lg sm:text-xl font-bold text-emerald-400">{{ number_format($userStats['total_score'] ?? 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Quizzes') }}</p>
                        <p class="text-lg sm:text-xl font-bold text-white">{{ $userStats['total_attempts'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Accuracy') }}</p>
                        <p class="text-lg sm:text-xl font-bold text-purple-400">{{ number_format($userStats['accuracy'] ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Leaderboard Table --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl overflow-hidden">
        @if(count($leaderboard) > 0)
            {{-- Desktop Table --}}
            <div class="hidden sm:block overflow-x-auto">
                <table class="min-w-full divide-y divide-white/10">
                    <thead class="bg-white/5">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Rank') }}
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Player') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Score') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Quizzes') }}
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">
                                {{ __('Accuracy') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($leaderboard as $leader)
                            <tr class="hover:bg-white/5 transition {{ $leader['user_id'] === Auth::id() ? 'bg-blue-500/10 border-l-4 border-blue-500' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if($leader['rank'] <= 3)
                                            <span class="text-2xl">
                                                @if($leader['rank'] == 1) 🥇
                                                @elseif($leader['rank'] == 2) 🥈
                                                @else 🥉
                                                @endif
                                            </span>
                                        @endif
                                        <span class="text-xl font-bold {{ $leader['rank'] <= 3 ? 'text-amber-400' : 'text-gray-400' }}">
                                            {{ $leader['rank'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-full flex items-center justify-center border border-emerald-500/30 mr-3">
                                            <span class="text-emerald-400 font-bold text-sm">
                                                {{ substr($leader['username'], 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white">{{ $leader['username'] }}</p>
                                            @if($leader['user_id'] === Auth::id())
                                                <p class="text-xs text-blue-400">{{ __('You') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-lg font-bold text-emerald-400">{{ number_format($leader['total_score']) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-base text-white">{{ $leader['total_attempts'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-base font-semibold
                                        {{ $leader['accuracy'] >= 80 ? 'text-green-400' : '' }}
                                        {{ $leader['accuracy'] >= 60 && $leader['accuracy'] < 80 ? 'text-yellow-400' : '' }}
                                        {{ $leader['accuracy'] < 60 ? 'text-red-400' : '' }}">
                                        {{ number_format($leader['accuracy'], 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="sm:hidden space-y-3 p-4">
                @foreach($leaderboard as $leader)
                    <div class="bg-white/5 border border-white/10 rounded-xl p-4 {{ $leader['user_id'] === Auth::id() ? 'border-blue-500 bg-blue-500/10' : '' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-3">
                                @if($leader['rank'] <= 3)
                                    <span class="text-3xl">
                                        @if($leader['rank'] == 1) 🥇
                                        @elseif($leader['rank'] == 2) 🥈
                                        @else 🥉
                                        @endif
                                    </span>
                                @else
                                    <div class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center">
                                        <span class="text-gray-400 font-bold">{{ $leader['rank'] }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-bold text-white">{{ $leader['username'] }}</p>
                                    @if($leader['user_id'] === Auth::id())
                                        <p class="text-xs text-blue-400">{{ __('You') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-3 pt-3 border-t border-white/10">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 mb-1">{{ __('Score') }}</p>
                                <p class="text-base font-bold text-emerald-400">{{ number_format($leader['total_score']) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 mb-1">{{ __('Quizzes') }}</p>
                                <p class="text-base font-bold text-white">{{ $leader['total_attempts'] }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 mb-1">{{ __('Accuracy') }}</p>
                                <p class="text-base font-bold {{ $leader['accuracy'] >= 80 ? 'text-green-400' : ($leader['accuracy'] >= 60 ? 'text-yellow-400' : 'text-red-400') }}">
                                    {{ number_format($leader['accuracy'], 1) }}%
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <p class="text-gray-400 text-lg font-semibold mb-2">{{ __('No ranking data available yet.') }}</p>
                <p class="text-gray-500 text-sm">{{ __('Be the first to play and top the leaderboard!') }}</p>
            </div>
        @endif
    </div>

    {{-- Loading Overlay --}}
    <div wire:loading class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center space-x-4">
                <svg class="animate-spin h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-white font-semibold">{{ __('Updating...') }}</span>
            </div>
        </div>
    </div>
</div>
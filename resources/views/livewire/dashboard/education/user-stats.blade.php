<div>
    {{-- Tabs --}}
    <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-2 mb-6 shadow-xl">
        <div class="flex flex-wrap gap-2">
            <button wire:click="setTab('overview')"
                    class="flex-1 min-w-[100px] px-4 py-3 rounded-xl font-semibold text-sm transition-all
                        {{ $selectedTab === 'overview' 
                            ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 shadow-lg' 
                            : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                📊 {{ __('Overview') }}
            </button>
            <button wire:click="setTab('history')"
                    class="flex-1 min-w-[100px] px-4 py-3 rounded-xl font-semibold text-sm transition-all
                        {{ $selectedTab === 'history' 
                            ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 shadow-lg' 
                            : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                📜 {{ __('History') }}
            </button>
            <button wire:click="setTab('rankings')"
                    class="flex-1 min-w-[100px] px-4 py-3 rounded-xl font-semibold text-sm transition-all
                        {{ $selectedTab === 'rankings' 
                            ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 shadow-lg' 
                            : 'bg-white/5 text-gray-300 hover:bg-white/10' }}">
                🏆 {{ __('Rankings') }}
            </button>
        </div>
    </div>

    {{-- Overview Tab --}}
    @if($selectedTab === 'overview')
        <div class="space-y-6">
            {{-- General Stats Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-blue-400 mb-1">{{ $generalStats['total_attempts'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">{{ __('Total Quizzes') }}</div>
                </div>
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-emerald-400 mb-1">{{ number_format($generalStats['total_points_earned'] ?? 0) }}</div>
                    <div class="text-xs text-gray-400">{{ __('Total Points') }}</div>
                </div>
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-purple-400 mb-1">{{ number_format($generalStats['overall_accuracy'] ?? 0, 1) }}%</div>
                    <div class="text-xs text-gray-400">{{ __('Accuracy') }}</div>
                </div>
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-amber-400 mb-1">{{ number_format($generalStats['average_points_per_attempt'] ?? 0, 1) }}</div>
                    <div class="text-xs text-gray-400">{{ __('Avg Score') }}</div>
                </div>
            </div>

            {{-- Best Attempt --}}
            @if($bestAttempt)
                <div class="bg-gradient-to-r from-amber-500/10 to-yellow-500/10 backdrop-blur-xl border border-amber-500/30 rounded-2xl p-6 shadow-xl">
                    <h3 class="text-lg sm:text-xl font-bold text-white mb-4 flex items-center">
                        <span class="text-2xl mr-2">🌟</span> {{ __('Best Performance') }}
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-xs text-amber-300 mb-1">{{ __('Score') }}</p>
                            <p class="text-2xl sm:text-3xl font-black text-white">{{ $bestAttempt->score }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-amber-300 mb-1">{{ __('Correct') }}</p>
                            <p class="text-2xl sm:text-3xl font-black text-green-400">{{ $bestAttempt->correct_count }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-amber-300 mb-1">{{ __('Wrong') }}</p>
                            <p class="text-2xl sm:text-3xl font-black text-red-400">{{ $bestAttempt->wrong_count }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-amber-300 mb-1">{{ __('Date') }}</p>
                            <p class="text-sm text-white">{{ $bestAttempt->finished_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Detailed Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-5 shadow-xl">
                    <h4 class="text-base font-bold text-white mb-4">📈 {{ __('Performance') }}</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-300">{{ __('Total Correct') }}</span>
                            <span class="font-bold text-green-400">{{ $generalStats['total_correct_answers'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-300">{{ __('Total Wrong') }}</span>
                            <span class="font-bold text-red-400">{{ $generalStats['total_wrong_answers'] ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-300">{{ __('Total Questions') }}</span>
                            <span class="font-bold text-white">{{ $generalStats['total_questions_answered'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-5 shadow-xl">
                    <h4 class="text-base font-bold text-white mb-4">⚡ {{ __('Speed') }}</h4>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-300">{{ __('Avg Time/Question') }}</span>
                            <span class="font-bold text-white">{{ number_format($generalStats['average_time_per_question'] ?? 0, 1) }}s</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-300">{{ __('Fastest Quiz') }}</span>
                            <span class="font-bold text-emerald-400">{{ number_format($generalStats['fastest_quiz_time'] ?? 0, 1) }}s</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- History Tab --}}
    @if($selectedTab === 'history')
        <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-xl overflow-hidden">
            @if(count($recentAttempts) > 0)
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase">{{ __('Date') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">{{ __('Score') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">{{ __('Correct') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">{{ __('Wrong') }}</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-400 uppercase">{{ __('Accuracy') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach($recentAttempts as $attempt)
                                <tr class="hover:bg-white/5 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-300">{{ \Carbon\Carbon::parse($attempt['finished_at'])->format('M d, Y H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-base font-bold text-emerald-400">{{ $attempt['score'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-base font-bold text-green-400">{{ $attempt['correct_count'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-base font-bold text-red-400">{{ $attempt['wrong_count'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-base font-semibold text-white">
                                            {{ round(($attempt['correct_count'] / ($attempt['correct_count'] + $attempt['wrong_count'])) * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="sm:hidden space-y-3 p-4">
                    @foreach($recentAttempts as $attempt)
                        <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($attempt['finished_at'])->format('M d, Y') }}</span>
                                <span class="text-lg font-bold text-emerald-400">{{ $attempt['score'] }} pts</span>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="text-center">
                                    <p class="text-xs text-gray-400 mb-1">{{ __('Correct') }}</p>
                                    <p class="text-base font-bold text-green-400">{{ $attempt['correct_count'] }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-400 mb-1">{{ __('Wrong') }}</p>
                                    <p class="text-base font-bold text-red-400">{{ $attempt['wrong_count'] }}</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-400 mb-1">{{ __('Accuracy') }}</p>
                                    <p class="text-base font-bold text-white">
                                        {{ round(($attempt['correct_count'] / ($attempt['correct_count'] + $attempt['wrong_count'])) * 100, 1) }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-gray-400 text-lg font-semibold mb-2">{{ __('No quiz history yet.') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('Start playing to see your progress!') }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Rankings Tab --}}
    @if($selectedTab === 'rankings')
        <div class="space-y-4">
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">🏆 {{ __('All Time Ranking') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Position') }}</p>
                        <p class="text-3xl font-black text-white">#{{ $statsAllTime['position'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Score') }}</p>
                        <p class="text-3xl font-black text-emerald-400">{{ number_format($statsAllTime['total_score'] ?? 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Quizzes') }}</p>
                        <p class="text-3xl font-black text-white">{{ $statsAllTime['total_attempts'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Accuracy') }}</p>
                        <p class="text-3xl font-black text-purple-400">{{ number_format($statsAllTime['accuracy'] ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">📅 {{ __('This Month Ranking') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Position') }}</p>
                        <p class="text-3xl font-black text-white">#{{ $statsMonthly['position'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Score') }}</p>
                        <p class="text-3xl font-black text-emerald-400">{{ number_format($statsMonthly['total_score'] ?? 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Quizzes') }}</p>
                        <p class="text-3xl font-black text-white">{{ $statsMonthly['total_attempts'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Accuracy') }}</p>
                        <p class="text-3xl font-black text-purple-400">{{ number_format($statsMonthly['accuracy'] ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-6 shadow-xl">
                <h3 class="text-lg font-bold text-white mb-4">📆 {{ __('This Week Ranking') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Position') }}</p>
                        <p class="text-3xl font-black text-white">#{{ $statsWeekly['position'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Score') }}</p>
                        <p class="text-3xl font-black text-emerald-400">{{ number_format($statsWeekly['total_score'] ?? 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Quizzes') }}</p>
                        <p class="text-3xl font-black text-white">{{ $statsWeekly['total_attempts'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">{{ __('Accuracy') }}</p>
                        <p class="text-3xl font-black text-purple-400">{{ number_format($statsWeekly['accuracy'] ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading --}}
    <div wire:loading class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl">
            <div class="flex items-center space-x-4">
                <svg class="animate-spin h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-white font-semibold">{{ __('Loading...') }}</span>
            </div>
        </div>
    </div>
</div>
<x-app-layout>
    <x-slot name="header">
        <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent font-black">
            {{ __('Quiz Results') }}
        </span>
    </x-slot>

    <div class="py-6 sm:py-12 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen">
        {{-- Background Effects --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- Header Emocional --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 sm:p-8 mb-6 shadow-2xl text-center">
                @if($attemptStats['accuracy'] >= 80)
                    <div class="text-6xl sm:text-7xl mb-4 animate-bounce">🎉</div>
                    <h2 class="text-3xl sm:text-4xl font-black mb-2">
                        <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">
                            {{ __('Excellent!') }}
                        </span>
                    </h2>
                @elseif($attemptStats['accuracy'] >= 60)
                    <div class="text-6xl sm:text-7xl mb-4">👍</div>
                    <h2 class="text-3xl sm:text-4xl font-black mb-2">
                        <span class="bg-gradient-to-r from-blue-400 to-cyan-400 bg-clip-text text-transparent">
                            {{ __('Good Job!') }}
                        </span>
                    </h2>
                @else
                    <div class="text-6xl sm:text-7xl mb-4">💪</div>
                    <h2 class="text-3xl sm:text-4xl font-black mb-2">
                        <span class="bg-gradient-to-r from-gray-400 to-gray-500 bg-clip-text text-transparent">
                            {{ __('Keep Practicing!') }}
                        </span>
                    </h2>
                @endif
                <p class="text-gray-400 text-sm sm:text-base">{{ __('Quiz Completed') }}</p>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6">
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-green-400 mb-1">{{ $attemptStats['correct'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-400">{{ __('Correct') }}</div>
                </div>
                
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-red-400 mb-1">{{ $attemptStats['wrong'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-400">{{ __('Wrong') }}</div>
                </div>
                
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-emerald-400 mb-1">{{ $attemptStats['score'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-400">{{ __('Points') }}</div>
                </div>
                
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl text-center hover:scale-105 transition">
                    <div class="text-3xl sm:text-4xl font-black text-purple-400 mb-1">{{ number_format($attemptStats['accuracy'], 1) }}%</div>
                    <div class="text-xs sm:text-sm text-gray-400">{{ __('Accuracy') }}</div>
                </div>
            </div>

            {{-- Recompensas --}}
            @php
                $rewardsService = app(\App\Services\Education\QuizRewardsService::class);
                $coinsEarned = $rewardsService->calculateCoinsFromPoints($attemptStats['score']);
                $wallet = \App\Models\Wallet::where('user_id', Auth::id())->where('currency', 'CAN')->first();
                $currentBalance = $wallet ? $wallet->balance : 0;
            @endphp

            @if($attemptStats['reward_paid'] && $coinsEarned > 0)
                <div class="bg-gradient-to-r from-amber-500/20 to-yellow-500/20 backdrop-blur-xl border border-amber-500/30 rounded-2xl p-6 sm:p-8 mb-6 shadow-2xl">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-center sm:text-left">
                            <div class="flex items-center justify-center sm:justify-start gap-2 mb-2">
                                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-sm sm:text-base text-amber-300 font-medium">{{ __('You Earned') }}</span>
                            </div>
                            <div class="text-4xl sm:text-5xl font-black text-white mb-1">
                                +{{ number_format($coinsEarned, 1) }} CAN
                            </div>
                            <p class="text-xs sm:text-sm text-amber-200">
                                {{ __('From') }} {{ $attemptStats['score'] }} {{ __('points') }}
                            </p>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 sm:p-5 border border-white/20 min-w-[160px] sm:min-w-[200px]">
                            <p class="text-xs sm:text-sm text-amber-200 font-medium mb-1 text-center">{{ __('Your Balance') }}</p>
                            <p class="text-3xl sm:text-4xl font-black text-white text-center">
                                {{ number_format($currentBalance, 1) }}
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($coinsEarned > 0)
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6 shadow-xl text-center">
                    <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-emerald-500 mx-auto mb-3"></div>
                    <p class="text-white font-semibold mb-1">{{ __('Processing your reward...') }}</p>
                    <p class="text-gray-400 text-sm">+{{ number_format($coinsEarned, 1) }} CAN</p>
                </div>
            @else
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 mb-6 shadow-xl text-center">
                    <p class="text-gray-400 text-sm sm:text-base">{{ __('Complete more questions correctly to earn coins!') }}</p>
                </div>
            @endif

            {{-- Ranking Position --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 sm:p-6 mb-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1">{{ __('Your Ranking') }}</p>
                        <p class="text-3xl sm:text-4xl font-black text-white">#{{ $userPosition }}</p>
                    </div>
                    <a href="{{ route('manager.education.ranking') }}" 
                       class="text-emerald-400 hover:text-emerald-300 text-sm font-semibold transition">
                        {{ __('View Full Ranking') }} →
                    </a>
                </div>
            </div>

            {{-- Answer Details --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 sm:p-6 mb-6 shadow-xl">
                <h3 class="text-lg sm:text-xl font-bold text-white mb-4 flex items-center">
                    <span class="text-2xl mr-2">📝</span> {{ __('Answer Details') }}
                </h3>
                <div class="space-y-3">
                    @foreach($attempt->answers as $index => $answer)
                        <div class="bg-white/5 border rounded-xl p-4
                            {{ $answer->is_correct ? 'border-green-500/30' : 'border-red-500/30' }}">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div class="flex-1">
                                    <p class="font-semibold text-sm sm:text-base text-white mb-2">
                                        {{ $index + 1 }}. {{ $answer->question->getText(app()->getLocale()) }}
                                    </p>
                                    <div class="space-y-1 text-xs sm:text-sm">
                                        <p class="text-gray-300">
                                            <span class="font-semibold">{{ __('Your answer') }}:</span>
                                            @if($answer->selectedOption)
                                                <span class="{{ $answer->is_correct ? 'text-green-400' : 'text-red-400' }}">
                                                    {{ $answer->selectedOption->getText(app()->getLocale()) }}
                                                </span>
                                            @else
                                                <span class="text-gray-500 italic">{{ __('No answer') }}</span>
                                            @endif
                                        </p>
                                        @if(!$answer->is_correct && $answer->question->getCorrectOption())
                                            <p class="text-gray-300">
                                                <span class="font-semibold">{{ __('Correct answer') }}:</span>
                                                <span class="text-green-400">
                                                    {{ $answer->question->getCorrectOption()->getText(app()->getLocale()) }}
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end gap-2">
                                    @if($answer->is_correct)
                                        <span class="text-green-400 font-bold text-lg">✓ +{{ $answer->points_awarded }}</span>
                                    @else
                                        <span class="text-red-400 font-bold text-lg">✗</span>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ round($answer->time_taken_ms / 1000, 1) }}s</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                <a href="{{ route('manager.education.index') }}" 
                   class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 rounded-xl font-bold hover:shadow-lg hover:shadow-emerald-500/50 hover:scale-105 transition text-center">
                    {{ __('Play Again') }}
                </a>
                <a href="{{ route('manager.education.ranking') }}" 
                   class="w-full sm:w-auto px-8 py-4 bg-white/5 border border-white/10 text-white rounded-xl font-bold hover:bg-white/10 transition text-center">
                    {{ __('View Ranking') }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
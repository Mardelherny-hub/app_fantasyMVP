<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quiz Results') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header con feedback emocional --}}
            <div class="bg-white rounded-xl shadow-lg p-6 sm:p-8 mb-4 sm:mb-6">
                <div class="text-center">
                    @if($attemptStats['accuracy'] >= 80)
                        <div class="text-5xl sm:text-6xl mb-3 sm:mb-4">🎉</div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-emerald-600 mb-2">{{ __('Excellent!') }}</h2>
                    @elseif($attemptStats['accuracy'] >= 60)
                        <div class="text-5xl sm:text-6xl mb-3 sm:mb-4">👍</div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-blue-600 mb-2">{{ __('Good Job!') }}</h2>
                    @else
                        <div class="text-5xl sm:text-6xl mb-3 sm:mb-4">💪</div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-600 mb-2">{{ __('Keep Practicing!') }}</h2>
                    @endif
                    <p class="text-sm sm:text-base text-gray-600">{{ __('Quiz Completed') }}</p>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                {{-- Correct --}}
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-green-600">{{ $attemptStats['correct'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('Correct') }}</div>
                </div>
                
                {{-- Wrong --}}
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-red-600">{{ $attemptStats['wrong'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('Wrong') }}</div>
                </div>
                
                {{-- Points --}}
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-emerald-600">{{ $attemptStats['score'] }}</div>
                    <div class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('Points') }}</div>
                </div>
                
                {{-- Accuracy --}}
                <div class="bg-white rounded-xl shadow p-4 text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-purple-600">{{ number_format($attemptStats['accuracy'], 1) }}%</div>
                    <div class="text-xs sm:text-sm text-gray-600 mt-1">{{ __('Accuracy') }}</div>
                </div>
            </div>

            {{-- Recompensas - REDISEÑADO COHERENTE --}}
            @php
                $rewardsService = app(\App\Services\Education\QuizRewardsService::class);
                $coinsEarned = $rewardsService->calculateCoinsFromPoints($attemptStats['score']);
                $wallet = \App\Models\Wallet::where('user_id', Auth::id())
                    ->where('currency', 'CAN')
                    ->first();
                $currentBalance = $wallet ? $wallet->balance : 0;
            @endphp

            @if($attemptStats['reward_paid'] && $coinsEarned > 0)
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg p-6 sm:p-8 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    {{-- Lado izquierdo: Coins ganadas --}}
                    <div class="text-center sm:text-left">
                        <div class="flex items-center justify-center sm:justify-start gap-2 mb-2">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm sm:text-base text-emerald-50 font-medium">{{ __('You Earned') }}</span>
                        </div>
                        <div class="text-3xl sm:text-4xl font-extrabold text-white mb-1">
                            +{{ number_format($coinsEarned, 1) }} CAN
                        </div>
                        <p class="text-xs sm:text-sm text-emerald-50">
                            {{ __('From') }} {{ $attemptStats['score'] }} {{ __('points') }}
                        </p>
                    </div>

                    {{-- Lado derecho: Balance actual --}}
                    <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4 sm:p-5 border border-white/30 min-w-[160px] sm:min-w-[180px]">
                        <p class="text-xs sm:text-sm text-emerald-50 font-medium mb-1 text-center">{{ __('Your Balance') }}</p>
                        <p class="text-2xl sm:text-3xl font-bold text-white text-center">
                            {{ number_format($currentBalance, 1) }} <span class="text-lg sm:text-xl">CAN</span>
                        </p>
                    </div>
                </div>
                
                {{-- Mensaje motivacional --}}
                <div class="mt-4 pt-4 border-t border-white/20">
                    <p class="text-center text-sm text-white font-medium">
                        💡 {{ __('Keep playing to earn more coins!') }}
                    </p>
                </div>
            </div>

            @elseif($coinsEarned > 0)
            {{-- Procesando reward --}}
            <div class="bg-white rounded-xl shadow-lg p-6 mb-4 sm:mb-6">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500 mx-auto mb-3"></div>
                    <p class="text-gray-700 font-semibold mb-1">{{ __('Processing your reward...') }}</p>
                    <p class="text-gray-600 text-sm">+{{ number_format($coinsEarned, 1) }} CAN</p>
                </div>
            </div>

            @else
            {{-- Sin reward --}}
            <div class="bg-white rounded-xl shadow-lg p-6 mb-4 sm:mb-6 text-center">
                <p class="text-gray-600 text-sm sm:text-base">{{ __('Complete more questions correctly to earn coins!') }}</p>
            </div>
            @endif

            {{-- Ranking position --}}
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs sm:text-sm text-gray-600 mb-1">{{ __('Your Ranking') }}</p>
                        <p class="text-2xl sm:text-3xl font-bold text-gray-800">#{{ $userPosition }}</p>
                    </div>
                    <a href="{{ route('manager.education.ranking') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-semibold">
                        {{ __('View Full Ranking') }} →
                    </a>
                </div>
            </div>

            {{-- Detalle de respuestas --}}
            <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
                <h3 class="text-base sm:text-lg font-semibold mb-4">{{ __('Answer Details') }}</h3>
                <div class="space-y-3 sm:space-y-4">
                    @foreach($attempt->answers as $index => $answer)
                        <div class="border rounded-lg p-3 sm:p-4 {{ $answer->is_correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-4">
                                <div class="flex-1">
                                    <p class="font-medium text-sm sm:text-base text-gray-800 mb-2">
                                        {{ $index + 1 }}. {{ $answer->question->getText(app()->getLocale()) }}
                                    </p>
                                    <div class="space-y-1 text-xs sm:text-sm">
                                        <p class="text-gray-600">
                                            <span class="font-semibold">{{ __('Your answer') }}:</span>
                                            @if($answer->selectedOption)
                                                <span class="{{ $answer->is_correct ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ $answer->selectedOption->getText(app()->getLocale()) }}
                                                </span>
                                            @else
                                                <span class="text-gray-500 italic">{{ __('No answer') }}</span>
                                            @endif
                                        </p>
                                        @if(!$answer->is_correct && $answer->question->getCorrectOption())
                                            <p class="text-gray-600">
                                                <span class="font-semibold">{{ __('Correct answer') }}:</span>
                                                <span class="text-green-700">
                                                    {{ $answer->question->getCorrectOption()->getText(app()->getLocale()) }}
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-start gap-2 sm:text-right">
                                    @if($answer->is_correct)
                                        <span class="text-green-600 font-bold text-base sm:text-lg whitespace-nowrap">✓ +{{ $answer->points_awarded }}</span>
                                    @else
                                        <span class="text-red-600 font-bold text-base sm:text-lg">✗</span>
                                    @endif
                                    <p class="text-xs text-gray-500">{{ round($answer->time_taken_ms / 1000, 1) }}s</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
                <a href="{{ route('manager.education.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 bg-emerald-500 text-white rounded-xl font-semibold hover:bg-emerald-600 transition duration-200 text-center shadow-lg">
                    {{ __('Play Again') }}
                </a>
                <a href="{{ route('manager.education.ranking') }}" 
                   class="w-full sm:w-auto px-6 py-3 bg-white text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition duration-200 text-center shadow-lg border border-gray-200">
                    {{ __('View Ranking') }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
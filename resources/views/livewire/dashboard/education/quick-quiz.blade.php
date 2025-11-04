<div x-data="{ 
    selectedOption: @entangle('selectedOptionId'),
    timeRemaining: @entangle('timeRemaining'),
    quizStarted: @entangle('quizStarted'),
    quizFinished: @entangle('quizFinished')
}" class="w-full">

    {{-- ========================================
         ESTADO 1: PANTALLA INICIAL (Start Quiz)
         ======================================== --}}
    @if(!$quizStarted && !$quizFinished)
        <div class="relative">
            {{-- Background Glow Effects --}}
            <div class="absolute inset-0 -z-10 overflow-hidden rounded-2xl">
                <div class="absolute top-0 left-1/4 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-teal-500/15 rounded-full blur-3xl" style="animation: pulse 4s ease-in-out infinite;"></div>
            </div>

            {{-- Card Principal --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 sm:p-8 shadow-2xl">
                {{-- Header con Icono --}}
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 mb-4">
                        <svg class="w-8 h-8 sm:w-10 sm:h-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl sm:text-3xl font-black mb-2">
                        <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">
                            {{ __('Quick Quiz') }}
                        </span>
                    </h2>
                    <p class="text-gray-400 text-sm sm:text-base">{{ __('Test your soccer knowledge!') }}</p>
                </div>

                {{-- Info Cards Grid --}}
                <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-6">
                    {{-- Preguntas --}}
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl mb-1">📝</div>
                        <p class="text-lg sm:text-xl font-bold text-white mb-1">10</p>
                        <p class="text-xs text-gray-400">{{ __('Questions') }}</p>
                    </div>

                    {{-- Tiempo --}}
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl mb-1">⏱️</div>
                        <p class="text-lg sm:text-xl font-bold text-white mb-1">30s</p>
                        <p class="text-xs text-gray-400">{{ __('Per question') }}</p>
                    </div>

                    {{-- Recompensas --}}
                    <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl p-3 sm:p-4 text-center">
                        <div class="text-2xl sm:text-3xl mb-1">💰</div>
                        <p class="text-lg sm:text-xl font-bold text-amber-400 mb-1">CAN</p>
                        <p class="text-xs text-gray-400">{{ __('Coins') }}</p>
                    </div>
                </div>

                {{-- Características --}}
                <div class="space-y-2 mb-8">
                    <div class="flex items-center text-gray-300 text-sm sm:text-base">
                        <svg class="w-5 h-5 text-emerald-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('Points based on difficulty & speed') }}</span>
                    </div>
                    <div class="flex items-center text-gray-300 text-sm sm:text-base">
                        <svg class="w-5 h-5 text-emerald-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('Streak bonuses for correct answers') }}</span>
                    </div>
                    <div class="flex items-center text-gray-300 text-sm sm:text-base">
                        <svg class="w-5 h-5 text-emerald-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ __('Convert points to coins automatically') }}</span>
                    </div>
                </div>

                {{-- Start Button --}}
                <button wire:click="startQuiz" 
                        wire:loading.attr="disabled"
                        class="w-full py-4 rounded-xl font-bold text-base sm:text-lg bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 hover:shadow-lg hover:shadow-emerald-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="startQuiz" class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('Start Quiz') }}
                    </span>
                    <span wire:loading wire:target="startQuiz" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ __('Loading...') }}
                    </span>
                </button>
            </div>
        </div>
    @endif

    {{-- ========================================
         ESTADO 2: QUIZ EN PROGRESO
         ======================================== --}}
    @if($quizStarted && !$quizFinished && count($questions) > 0)
        @php
            $currentQuestion = $questions[$currentQuestionIndex];
            $progress = (($currentQuestionIndex + 1) / count($questions)) * 100;
            $currentStreak = $this->calculateCurrentStreak();
        @endphp

        <div class="space-y-4">
            {{-- Header: Stats Compactas --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    {{-- Cronómetro --}}
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 transform -rotate-90" viewBox="0 0 36 36">
                                <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-white/10" stroke-width="2"></circle>
                                <circle cx="18" cy="18" r="16" fill="none" 
                                        :class="{
                                            'text-emerald-500': timeRemaining > 10,
                                            'text-yellow-500': timeRemaining <= 10 && timeRemaining > 5,
                                            'text-red-500': timeRemaining <= 5
                                        }"
                                        class="stroke-current transition-all duration-300"
                                        stroke-width="2.5"
                                        stroke-linecap="round"
                                        :stroke-dasharray="100"
                                        :stroke-dashoffset="100 - (timeRemaining / 30 * 100)">
                                </circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-base sm:text-lg font-bold"
                                      :class="{
                                          'text-white': timeRemaining > 10,
                                          'text-yellow-400': timeRemaining <= 10 && timeRemaining > 5,
                                          'text-red-400 animate-pulse': timeRemaining <= 5
                                      }"
                                      x-text="timeRemaining">30</span>
                            </div>
                        </div>
                    </div>

                    {{-- Stats Grid --}}
                    <div class="flex items-center space-x-2 sm:space-x-4">
                        {{-- Score --}}
                        <div class="text-center">
                            <p class="text-xs text-gray-400">{{ __('Score') }}</p>
                            <p class="text-lg sm:text-xl font-bold text-amber-400">{{ $totalScore }}</p>
                        </div>

                        {{-- Correctas/Incorrectas --}}
                        <div class="flex items-center space-x-1 sm:space-x-2">
                            <div class="flex items-center bg-green-500/20 border border-green-500/30 rounded-lg px-2 py-1">
                                <span class="text-green-400 font-bold text-sm sm:text-base">✓{{ $correctCount }}</span>
                            </div>
                            <div class="flex items-center bg-red-500/20 border border-red-500/30 rounded-lg px-2 py-1">
                                <span class="text-red-400 font-bold text-sm sm:text-base">✗{{ $wrongCount }}</span>
                            </div>
                        </div>

                        {{-- Streak --}}
                        @if($currentStreak > 0)
                            <div class="flex items-center bg-gradient-to-r from-orange-500/20 to-amber-500/20 border border-orange-500/30 rounded-lg px-2 sm:px-3 py-1">
                                <span class="text-lg mr-1">🔥</span>
                                <span class="text-orange-400 font-bold text-sm sm:text-base">{{ $currentStreak }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs text-gray-400">
                        <span>{{ __('Question') }} {{ $currentQuestionIndex + 1 }}/{{ count($questions) }}</span>
                        <span>{{ round($progress) }}%</span>
                    </div>
                    <div class="w-full bg-white/10 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 h-full rounded-full transition-all duration-500 shadow-lg shadow-emerald-500/50"
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Question Card --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 sm:p-6 shadow-xl">
                {{-- Difficulty Badge --}}
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                        {{ $currentQuestion['difficulty'] == 1 ? 'bg-green-500/20 text-green-400 border border-green-500/30' : '' }}
                        {{ $currentQuestion['difficulty'] == 2 ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : '' }}
                        {{ $currentQuestion['difficulty'] == 3 ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}">
                        @if($currentQuestion['difficulty'] == 1)
                            ⭐ {{ __('Easy') }}
                        @elseif($currentQuestion['difficulty'] == 2)
                            ⭐⭐ {{ __('Medium') }}
                        @else
                            ⭐⭐⭐ {{ __('Hard') }}
                        @endif
                    </span>

                    {{-- Skip Button --}}
                    <button wire:click="skipQuestion"
                            wire:loading.attr="disabled"
                            class="text-xs sm:text-sm text-gray-400 hover:text-white font-medium transition">
                        {{ __('Skip') }} →
                    </button>
                </div>

                {{-- Question Text --}}
                <h3 class="text-lg sm:text-xl font-bold text-white mb-6 leading-relaxed">
                    {{ $currentQuestion['text'] }}
                </h3>

                {{-- Options --}}
                <div class="space-y-3">
                    @foreach($currentQuestion['options'] as $option)
                        <button wire:click="selectOption({{ $option['id'] }})"
                                type="button"
                                class="w-full text-left p-4 rounded-xl border-2 transition-all duration-200
                                    {{ $selectedOptionId === $option['id'] 
                                        ? 'bg-gradient-to-r from-emerald-500/20 to-teal-500/20 border-emerald-500 shadow-lg shadow-emerald-500/30 scale-[1.02]' 
                                        : 'bg-white/5 border-white/10 hover:border-emerald-500/50 hover:bg-white/10 hover:scale-[1.01]' }}">
                            <div class="flex items-center">
                                {{-- Radio Circle --}}
                                <div class="flex-shrink-0 w-6 h-6 rounded-full border-2 mr-3 flex items-center justify-center transition-all
                                    {{ $selectedOptionId === $option['id'] 
                                        ? 'border-emerald-500 bg-emerald-500' 
                                        : 'border-white/30' }}">
                                    @if($selectedOptionId === $option['id'])
                                        <svg class="w-4 h-4 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>

                                {{-- Option Text --}}
                                <span class="text-sm sm:text-base font-medium
                                    {{ $selectedOptionId === $option['id'] ? 'text-white' : 'text-gray-300' }}">
                                    {{ $option['text'] }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Submit Button --}}
                <div class="mt-6">
                    <button wire:click="submitAnswer"
                            wire:loading.attr="disabled"
                            :disabled="selectedOption === null"
                            class="w-full py-4 rounded-xl font-bold text-base sm:text-lg bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 hover:shadow-lg hover:shadow-emerald-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                        <span wire:loading.remove wire:target="submitAnswer">
                            @if($currentQuestionIndex < count($questions) - 1)
                                {{ __('Next Question') }} →
                            @else
                                {{ __('Finish Quiz') }} ✓
                            @endif
                        </span>
                        <span wire:loading wire:target="submitAnswer" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('Processing...') }}
                        </span>
                    </button>
                </div>

                {{-- Error Message --}}
                @if($errorMessage)
                    <div class="mt-4 bg-red-500/10 border border-red-500/30 rounded-lg p-3 text-red-400 text-sm">
                        {{ $errorMessage }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Timer Script --}}
        <script>
            document.addEventListener('alpine:init', () => {
                let timerInterval = null;

                Alpine.effect(() => {
                    const started = @json($quizStarted);
                    const finished = @json($quizFinished);

                    if (started && !finished && !timerInterval) {
                        timerInterval = setInterval(() => {
                            @this.call('updateTimer', @this.timeRemaining - 1);
                        }, 1000);
                    }

                    if (finished && timerInterval) {
                        clearInterval(timerInterval);
                        timerInterval = null;
                    }
                });
            });
        </script>
    @endif

    {{-- ========================================
         ESTADO 3: QUIZ FINALIZADO
         ======================================== --}}
    @if($quizFinished)
        <div class="relative">
            {{-- Background Celebration Effect --}}
            <div class="absolute inset-0 -z-10 overflow-hidden rounded-2xl">
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
            </div>

            {{-- Success Card --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 sm:p-12 text-center shadow-2xl">
                {{-- Success Icon --}}
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-emerald-500/30 to-teal-500/30 border-2 border-emerald-500/50 animate-bounce">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                {{-- Title --}}
                <h2 class="text-2xl sm:text-3xl font-black mb-3">
                    <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">
                        {{ __('Quiz Completed!') }}
                    </span>
                </h2>
                
                <p class="text-gray-400 text-sm sm:text-base mb-6">
                    {{ __('Calculating your rewards...') }}
                </p>

                {{-- Loading Spinner --}}
                <div class="flex justify-center mb-6">
                    <svg class="animate-spin h-12 w-12 text-emerald-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>

                {{-- Quick Stats Preview --}}
                <div class="grid grid-cols-3 gap-4 max-w-md mx-auto">
                    <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                        <p class="text-2xl font-bold text-emerald-400">{{ $correctCount }}</p>
                        <p class="text-xs text-gray-400">{{ __('Correct') }}</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                        <p class="text-2xl font-bold text-amber-400">{{ $totalScore }}</p>
                        <p class="text-xs text-gray-400">{{ __('Points') }}</p>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-xl p-3">
                        <p class="text-2xl font-bold text-teal-400">{{ $this->calculateCurrentStreak() }}</p>
                        <p class="text-xs text-gray-400">{{ __('Max Streak') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Auto-redirect Script --}}
        <script>
            setTimeout(() => {
                const locale = '{{ app()->getLocale() }}';
                const attemptId = {{ $attemptId }};
                window.location.href = `/${locale}/manager/education/results/${attemptId}`;
            }, 2000);
        </script>
    @endif
</div>
<div class="max-w-4xl mx-auto">
    {{-- Mensaje de error --}}
    @if($errorMessage)
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
            <p class="font-semibold">{{ __('Error') }}</p>
            <p>{{ $errorMessage }}</p>
        </div>
    @endif

    {{-- Estado inicial: Botón para iniciar quiz --}}
    @if(!$quizStarted && !$quizFinished)
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <svg class="w-24 h-24 mx-auto text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">{{ __('Quick Quiz') }}</h2>
            <p class="text-gray-600 mb-6">
                {{ __('Test your soccer knowledge with 10 random questions!') }}
            </p>
            <div class="mb-6 text-sm text-gray-500 space-y-2">
                <p>⏱️ {{ __('30 seconds per question') }}</p>
                <p>🎯 {{ __('Earn points based on difficulty and speed') }}</p>
                <p>💰 {{ __('Convert points to coins') }}</p>
            </div>
            <button wire:click="startQuiz" 
                    class="px-8 py-3 bg-emerald-500 text-white font-semibold rounded-lg hover:bg-emerald-600 transition duration-200">
                {{ __('Start Quiz') }}
            </button>
        </div>
    @endif

    {{-- Quiz en progreso --}}
    @if($quizStarted && !$quizFinished && count($questions) > 0)
        <div class="bg-white rounded-lg shadow-lg p-6">
            {{-- Header: Progreso y cronómetro --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-sm text-gray-600">{{ __('Question') }}</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $currentQuestionIndex + 1 }} / {{ count($questions) }}
                    </p>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">{{ __('Score') }}</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $totalScore }}</p>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">{{ __('Time') }}</p>
                    <div class="relative">
                        <div class="text-3xl font-bold" 
                             x-data="{ time: @entangle('timeRemaining') }"
                             x-init="
                                 let interval = setInterval(() => {
                                     if (time > 0) {
                                         time--;
                                         $wire.updateTimer(time);
                                     } else {
                                         clearInterval(interval);
                                     }
                                 }, 1000);
                             "
                             :class="time <= 5 ? 'text-red-600 animate-pulse' : 'text-gray-800'">
                            <span x-text="time"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra de progreso --}}
            <div class="mb-6">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-300"
                         style="width: {{ (($currentQuestionIndex + 1) / count($questions)) * 100 }}%"></div>
                </div>
            </div>

            @php
                $currentQuestion = $questions[$currentQuestionIndex];
            @endphp

            {{-- Pregunta actual --}}
            <div class="mb-8">
                <div class="flex items-start mb-4">
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mr-3
                        {{ $currentQuestion['difficulty'] == 1 ? 'bg-green-100 text-green-800' : '' }}
                        {{ $currentQuestion['difficulty'] == 2 ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $currentQuestion['difficulty'] == 3 ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $currentQuestion['difficulty'] == 1 ? __('Easy') : '' }}
                        {{ $currentQuestion['difficulty'] == 2 ? __('Medium') : '' }}
                        {{ $currentQuestion['difficulty'] == 3 ? __('Hard') : '' }}
                    </span>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-6">
                    {{ $currentQuestion['text'] }}
                </h3>

                {{-- Opciones de respuesta --}}
                <div class="space-y-3">
                    @foreach($currentQuestion['options'] as $option)
                        <button wire:click="selectOption({{ $option['id'] }})"
                                class="w-full text-left p-4 rounded-lg border-2 transition duration-200
                                    {{ $selectedOptionId === $option['id'] 
                                        ? 'border-emerald-500 bg-emerald-50' 
                                        : 'border-gray-200 hover:border-emerald-300 hover:bg-gray-50' }}">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full border-2 mr-3 flex items-center justify-center
                                    {{ $selectedOptionId === $option['id'] 
                                        ? 'border-emerald-500 bg-emerald-500' 
                                        : 'border-gray-300' }}">
                                    @if($selectedOptionId === $option['id'])
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                                <span class="text-gray-800">{{ $option['text'] }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Botones de acción --}}
            <div class="flex items-center justify-between">
                <button wire:click="skipQuestion"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                    {{ __('Skip') }}
                </button>

                <button 
                    wire:click="submitAnswer" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    :disabled="selectedOption === null"
                    x-ref="submitButton"
                    @click="$refs.submitButton.disabled = true"
                    class="w-full px-6 py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-emerald-500/50 transition disabled:opacity-50 disabled:cursor-not-allowed">                    @if($currentQuestionIndex < count($questions) - 1)
                        {{ __('Next Question') }}
                    @else
                        {{ __('Finish Quiz') }}
                    @endif
                </button>
            </div>

            {{-- Estadísticas rápidas --}}
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Correct') }}</p>
                        <p class="text-lg font-bold text-green-600">{{ $correctCount }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Wrong') }}</p>
                        <p class="text-lg font-bold text-red-600">{{ $wrongCount }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">{{ __('Streak') }}</p>
                        <p class="text-lg font-bold text-blue-600">{{ $this->calculateCurrentStreak() }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Quiz finalizado --}}
    @if($quizFinished)
    <div class="bg-white rounded-lg shadow-lg p-8 text-center"
         x-data="{ 
             attemptId: @entangle('attemptId'),
             locale: '{{ app()->getLocale() }}'
         }"
         x-init="
             console.log('Attempt ID:', attemptId);
             console.log('Locale:', locale);
             let url = '/' + locale + '/manager/education/results/' + attemptId;
             console.log('Redirecting to:', url);
             setTimeout(() => { 
                 window.location.href = url;
             }, 2000);
         ">
        <div class="mb-6">
            <svg class="w-24 h-24 mx-auto text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-800 mb-4">{{ __('Quiz Completed!') }}</h2>
        <p class="text-gray-600 mb-6">{{ __('Redirecting to results...') }}</p>
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500 mx-auto"></div>
    </div>
@endif
</div>
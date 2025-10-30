<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Quiz Results') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Resumen de resultados --}}
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <div class="text-center mb-6">
                    @if($attemptStats['accuracy'] >= 80)
                        <div class="text-6xl mb-4">🎉</div>
                        <h2 class="text-3xl font-bold text-emerald-600 mb-2">{{ __('Excellent!') }}</h2>
                    @elseif($attemptStats['accuracy'] >= 60)
                        <div class="text-6xl mb-4">👍</div>
                        <h2 class="text-3xl font-bold text-blue-600 mb-2">{{ __('Good Job!') }}</h2>
                    @else
                        <div class="text-6xl mb-4">💪</div>
                        <h2 class="text-3xl font-bold text-gray-600 mb-2">{{ __('Keep Practicing!') }}</h2>
                    @endif
                    <p class="text-gray-600">{{ __('Quiz Completed') }}</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600">{{ $attemptStats['correct'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Correct') }}</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-3xl font-bold text-red-600">{{ $attemptStats['wrong'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Wrong') }}</div>
                    </div>
                    <div class="text-center p-4 bg-emerald-50 rounded-lg">
                        <div class="text-3xl font-bold text-emerald-600">{{ $attemptStats['score'] }}</div>
                        <div class="text-sm text-gray-600">{{ __('Points') }}</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600">{{ number_format($attemptStats['accuracy'], 1) }}%</div>
                        <div class="text-sm text-gray-600">{{ __('Accuracy') }}</div>
                    </div>
                </div>

                {{-- Recompensas --}}
                @if($attemptStats['reward_paid'])
                    <div class="bg-emerald-50 border-2 border-emerald-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-center space-x-3">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <div class="text-lg font-semibold text-emerald-700">{{ __('Coins Earned!') }}</div>
                                <div class="text-2xl font-bold text-emerald-600">
                                    {{ number_format($attemptStats['score'] * 0.1, 2) }} CAN
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Ranking position --}}
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">{{ __('Your Ranking Position') }}</p>
                    <p class="text-2xl font-bold text-gray-800">#{{ $userPosition }}</p>
                </div>
            </div>

            {{-- Detalle de respuestas --}}
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">{{ __('Answer Details') }}</h3>
                <div class="space-y-4">
                    @foreach($attempt->answers as $index => $answer)
                        <div class="border rounded-lg p-4 {{ $answer->is_correct ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-800 mb-2">
                                        {{ $index + 1 }}. {{ $answer->question->getText(app()->getLocale()) }}
                                    </p>
                                    <div class="space-y-1">
                                        <p class="text-sm text-gray-600">
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
                                            <p class="text-sm text-gray-600">
                                                <span class="font-semibold">{{ __('Correct answer') }}:</span>
                                                <span class="text-green-700">
                                                    {{ $answer->question->getCorrectOption()->getText(app()->getLocale()) }}
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    @if($answer->is_correct)
                                        <span class="text-green-600 font-bold text-lg">✓ +{{ $answer->points_awarded }} pts</span>
                                    @else
                                        <span class="text-red-600 font-bold text-lg">✗</span>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">{{ round($answer->time_taken_ms / 1000, 1) }}s</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center justify-center space-x-4">
                <a href="{{ route('manager.education.index') }}" 
                   class="px-6 py-3 bg-emerald-500 text-white rounded-lg font-semibold hover:bg-emerald-600 transition duration-200">
                    {{ __('Play Again') }}
                </a>
                <a href="{{ route('manager.education.ranking') }}" 
                   class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition duration-200">
                    {{ __('View Ranking') }}
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
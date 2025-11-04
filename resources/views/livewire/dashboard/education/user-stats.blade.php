<div>
    {{-- Header con tabs --}}
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">📊 {{ __('Your Statistics') }}</h2>
            <button wire:click="refresh" 
                    class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ __('Refresh') }}
            </button>
        </div>

        {{-- Tabs --}}
        <div class="flex space-x-2 border-b border-gray-200">
            <button wire:click="setTab('overview')"
                    class="px-4 py-2 font-semibold transition duration-200 border-b-2
                        {{ $selectedTab === 'overview' 
                            ? 'border-emerald-500 text-emerald-600' 
                            : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                {{ __('Overview') }}
            </button>
            <button wire:click="setTab('history')"
                    class="px-4 py-2 font-semibold transition duration-200 border-b-2
                        {{ $selectedTab === 'history' 
                            ? 'border-emerald-500 text-emerald-600' 
                            : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                {{ __('History') }}
            </button>
            <button wire:click="setTab('rankings')"
                    class="px-4 py-2 font-semibold transition duration-200 border-b-2
                        {{ $selectedTab === 'rankings' 
                            ? 'border-emerald-500 text-emerald-600' 
                            : 'border-transparent text-gray-600 hover:text-gray-800' }}">
                {{ __('Rankings') }}
            </button>
        </div>
    </div>

    {{-- Tab: Overview --}}
    @if($selectedTab === 'overview')
        <div class="space-y-6">
            {{-- Estadísticas generales --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">{{ __('Total Quizzes') }}</p>
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ $generalStats['total_attempts'] ?? 0 }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">{{ __('Total Score') }}</p>
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($generalStats['total_points_earned'] ?? 0) }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">{{ __('Average Score') }}</p>
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($generalStats['average_points_per_attempt'] ?? 0, 1) }}</p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">{{ __('Accuracy Rate') }}</p>
                        <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </div>
                    <p class="text-3xl font-bold text-gray-800">{{ number_format($generalStats['overall_accuracy'] ?? 0, 1) }}%</p>
                </div>
            </div>

            {{-- Mejor intento --}}
            @if($bestAttempt)
                <div class="bg-gradient-to-r from-yellow-400 to-orange-500 rounded-lg shadow-lg p-6 text-white">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        {{ __('Best Quiz Performance') }}
                    </h3>
                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <p class="text-sm text-yellow-100">{{ __('Score') }}</p>
                            <p class="text-2xl font-bold">{{ $bestAttempt->score }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-yellow-100">{{ __('Correct') }}</p>
                            <p class="text-2xl font-bold">{{ $bestAttempt->correct_count }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-yellow-100">{{ __('Accuracy') }}</p>
                            <p class="text-2xl font-bold">
                                {{ round(($bestAttempt->correct_count / ($bestAttempt->correct_count + $bestAttempt->wrong_count)) * 100, 1) }}%
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-yellow-100">{{ __('Date') }}</p>
                            <p class="text-sm font-semibold">{{ $bestAttempt->finished_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Desglose de respuestas --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('Answer Breakdown') }}</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">{{ __('Correct Answers') }}</p>
                        <p class="text-4xl font-bold text-green-600">{{ number_format($generalStats['total_correct_answers'] ?? 0) }}</p>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">{{ __('Wrong Answers') }}</p>
                        <p class="text-4xl font-bold text-green-600">{{ number_format($generalStats['total_incorrect_answers'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Tab: History --}}
    @if($selectedTab === 'history')
        <div class="bg-white rounded-lg shadow">
            @if(count($recentAttempts) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Score') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Correct') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Wrong') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Accuracy') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentAttempts as $attempt)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($attempt['finished_at'])->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-lg font-bold text-emerald-600">{{ $attempt['score'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-green-600 font-semibold">{{ $attempt['correct_count'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-red-600 font-semibold">{{ $attempt['wrong_count'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="font-semibold">
                                            {{ round(($attempt['correct_count'] / ($attempt['correct_count'] + $attempt['wrong_count'])) * 100, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center">
                    <p class="text-gray-500">{{ __('No quiz history yet. Start playing to see your progress!') }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Tab: Rankings --}}
    @if($selectedTab === 'rankings')
        <div class="space-y-4">
            {{-- All Time --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">🏆 {{ __('All Time Ranking') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Position') }}</p>
                        <p class="text-2xl font-bold text-gray-800">#{{ $statsAllTime['position'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Score') }}</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format($statsAllTime['total_score'] ?? 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Quizzes') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $statsAllTime['total_attempts'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Accuracy') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($statsAllTime['accuracy'] ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>

            {{-- Monthly --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📅 {{ __('This Month Ranking') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Position') }}</p>
                        <p class="text-2xl font-bold text-gray-800">#{{ $statsMonthly['position'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Score') }}</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format($statsMonthly['total_score'] ?? 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Quizzes') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $statsMonthly['total_attempts'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Accuracy') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($statsMonthly['accuracy'] ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>

            {{-- Weekly --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📆 {{ __('This Week Ranking') }}</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Position') }}</p>
                        <p class="text-2xl font-bold text-gray-800">#{{ $statsWeekly['position'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Score') }}</p>
                        <p class="text-2xl font-bold text-emerald-600">{{ number_format($statsWeekly['total_score'] ?? 0) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Quizzes') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $statsWeekly['total_attempts'] ?? 0 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600">{{ __('Accuracy') }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($statsWeekly['accuracy'] ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading indicator --}}
    <div wire:loading class="fixed top-4 right-4 bg-white rounded-lg shadow-lg p-4 z-50">
        <div class="flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-emerald-500" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm text-gray-600">{{ __('Updating...') }}</span>
        </div>
    </div>
</div>
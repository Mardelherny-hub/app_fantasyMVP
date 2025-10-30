<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Education Hub') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Bienvenida y estadísticas rápidas --}}
            <div class="mb-8">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-lg shadow-lg p-8 text-white">
                    <h1 class="text-3xl font-bold mb-2">{{ __('Welcome to the Education Hub!') }}</h1>
                    <p class="text-emerald-100">{{ __('Test your soccer knowledge and earn rewards') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {{-- Estadística: Total Attempts --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Total Quizzes') }}</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $userStats['total_attempts'] }}</p>
                        </div>
                    </div>
                </div>

                {{-- Estadística: Total Score --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Total Points') }}</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($userStats['total_score']) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Estadística: Accuracy Rate --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">{{ __('Accuracy Rate') }}</p>
                            <p class="text-2xl font-bold text-gray-800">{{ number_format($userStats['accuracy_rate'], 1) }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Quiz Component --}}
            <div class="mb-8">
                <livewire:dashboard.education.quick-quiz />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Ranking Preview --}}
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800">🏆 {{ __('Top Players') }}</h3>
                        <a href="{{ route('manager.education.ranking') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-semibold">
                            {{ __('View All') }} →
                        </a>
                    </div>

                    @if($topUsers->count() > 0)
                        <div class="space-y-3">
                            @foreach($topUsers as $leader)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <span class="text-2xl mr-3">
                                            @if($leader['rank'] == 1) 🥇
                                            @elseif($leader['rank'] == 2) 🥈
                                            @elseif($leader['rank'] == 3) 🥉
                                            @else {{ $leader['rank'] }}
                                            @endif
                                        </span>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $leader['username'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $leader['total_attempts'] }} {{ __('quizzes') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-emerald-600">{{ number_format($leader['total_score']) }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($leader['accuracy'], 1) }}% {{ __('accuracy') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- User Position --}}
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Your Position') }}</p>
                                    <p class="text-xl font-bold text-gray-800">#{{ $userPosition }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">{{ __('Your Score') }}</p>
                                    <p class="text-xl font-bold text-emerald-600">{{ number_format($userStats['total_score']) }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">{{ __('No ranking data yet. Be the first!') }}</p>
                    @endif
                </div>

                {{-- Recent Activity --}}
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800">📊 {{ __('Your Stats') }}</h3>
                        <a href="{{ route('manager.education.stats') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-semibold">
                            {{ __('View Details') }} →
                        </a>
                    </div>

                    @if($lastAttempt)
                        <div class="mb-6">
                            <p class="text-sm text-gray-600 mb-2">{{ __('Last Quiz') }}</p>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600">{{ $lastAttempt->finished_at->diffForHumans() }}</span>
                                    <span class="text-lg font-bold text-emerald-600">{{ $lastAttempt->score }} {{ __('pts') }}</span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm">
                                    <span class="text-green-600">✓ {{ $lastAttempt->correct_count }}</span>
                                    <span class="text-red-600">✗ {{ $lastAttempt->wrong_count }}</span>
                                    <span class="text-gray-600">
                                        {{ round(($lastAttempt->correct_count / ($lastAttempt->correct_count + $lastAttempt->wrong_count)) * 100, 1) }}% {{ __('accuracy') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Average Score') }}</span>
                            <span class="font-semibold text-gray-800">{{ number_format($userStats['average_score'], 1) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Total Correct') }}</span>
                            <span class="font-semibold text-green-600">{{ number_format($userStats['correct_answers']) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('Total Questions') }}</span>
                            <span class="font-semibold text-gray-800">{{ number_format($userStats['total_answers']) }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('manager.education.stats') }}" 
                           class="block w-full text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                            {{ __('View Full Statistics') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
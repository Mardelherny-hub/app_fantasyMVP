<x-app-layout>
    <x-slot name="header">
        <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent font-black">
            {{ __('Education Hub') }}
        </span>
    </x-slot>

    <div class="py-6 sm:py-12 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 min-h-screen">
        {{-- Background Effects --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 right-1/4 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-3xl" style="animation: pulse 6s ease-in-out infinite;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- Hero Section --}}
            <div class="mb-6 sm:mb-8">
                <div class="bg-gradient-to-r from-emerald-500/10 to-teal-500/10 backdrop-blur-xl border border-white/10 rounded-2xl p-6 sm:p-8 shadow-2xl">
                    <div class="flex items-center space-x-4 mb-3">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 rounded-full flex items-center justify-center border border-emerald-500/30">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-white">{{ __('Welcome to the Education Hub!') }}</h1>
                            <p class="text-emerald-300 text-sm sm:text-base">{{ __('Test your soccer knowledge and earn rewards') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
                {{-- Total Quizzes --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl hover:scale-105 transition-transform">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-3 border border-blue-500/30">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1">{{ __('Total Quizzes') }}</p>
                        <p class="text-xl sm:text-2xl font-bold text-white">{{ $userStats['total_attempts'] }}</p>
                    </div>
                </div>

                {{-- Total Score --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl hover:scale-105 transition-transform">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center mb-3 border border-emerald-500/30">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1">{{ __('Total Score') }}</p>
                        <p class="text-xl sm:text-2xl font-bold text-emerald-400">{{ number_format($userStats['total_score']) }}</p>
                    </div>
                </div>

                {{-- Accuracy --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl hover:scale-105 transition-transform">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-3 border border-purple-500/30">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1">{{ __('Accuracy') }}</p>
                        <p class="text-xl sm:text-2xl font-bold text-purple-400">{{ number_format($userStats['accuracy_rate'], 1) }}%</p>
                    </div>
                </div>

                {{-- Ranking Position --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-xl p-4 sm:p-5 shadow-xl hover:scale-105 transition-transform">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-500/20 rounded-lg flex items-center justify-center mb-3 border border-amber-500/30">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-400 mb-1">{{ __('Your Position') }}</p>
                        <p class="text-xl sm:text-2xl font-bold text-amber-400">#{{ $userPosition }}</p>
                    </div>
                </div>
            </div>

            {{-- Quick Quiz Component --}}
            <div class="mb-6 sm:mb-8">
                <livewire:dashboard.education.quick-quiz />
            </div>

            {{-- Two Columns Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                
                {{-- Ranking Preview --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 sm:p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg sm:text-xl font-bold text-white flex items-center">
                            <span class="text-2xl mr-2">🏆</span> {{ __('Top Players') }}
                        </h3>
                        <a href="{{ route('manager.education.ranking') }}" 
                           class="text-xs sm:text-sm text-emerald-400 hover:text-emerald-300 font-semibold transition">
                            {{ __('View All') }} →
                        </a>
                    </div>

                    @if($topUsers->count() > 0)
                        <div class="space-y-3 mb-4">
                            @foreach($topUsers as $leader)
                                <div class="bg-white/5 border border-white/10 rounded-xl p-3 hover:bg-white/10 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <span class="text-xl sm:text-2xl">
                                                @if($leader['rank'] == 1) 🥇
                                                @elseif($leader['rank'] == 2) 🥈
                                                @elseif($leader['rank'] == 3) 🥉
                                                @else <span class="text-gray-400 font-bold text-sm">{{ $leader['rank'] }}</span>
                                                @endif
                                            </span>
                                            <div>
                                                <p class="font-semibold text-white text-sm sm:text-base">{{ $leader['username'] }}</p>
                                                <p class="text-xs text-gray-400">{{ $leader['total_attempts'] }} {{ __('quizzes') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-emerald-400 text-sm sm:text-base">{{ number_format($leader['total_score']) }}</p>
                                            <p class="text-xs text-gray-400">{{ number_format($leader['accuracy'], 1) }}%</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- User Position Card --}}
                        <div class="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/30 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs sm:text-sm text-gray-400 mb-1">{{ __('Your Position') }}</p>
                                    <p class="text-xl sm:text-2xl font-bold text-white">#{{ $userPosition }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs sm:text-sm text-gray-400 mb-1">{{ __('Your Score') }}</p>
                                    <p class="text-xl sm:text-2xl font-bold text-emerald-400">{{ number_format($userStats['total_score']) }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-400 text-sm">{{ __('No ranking data yet. Be the first!') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Your Stats --}}
                <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 sm:p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg sm:text-xl font-bold text-white flex items-center">
                            <span class="text-2xl mr-2">📊</span> {{ __('Your Stats') }}
                        </h3>
                        <a href="{{ route('manager.education.stats') }}" 
                           class="text-xs sm:text-sm text-emerald-400 hover:text-emerald-300 font-semibold transition">
                            {{ __('View Details') }} →
                        </a>
                    </div>

                    @if($lastAttempt)
                        <div class="mb-5">
                            <p class="text-xs sm:text-sm text-gray-400 mb-2">{{ __('Last Quiz') }}</p>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs sm:text-sm text-gray-400">{{ $lastAttempt->finished_at->diffForHumans() }}</span>
                                    <span class="text-base sm:text-lg font-bold text-emerald-400">{{ $lastAttempt->score }} {{ __('pts') }}</span>
                                </div>
                                <div class="flex items-center space-x-4 text-xs sm:text-sm">
                                    <span class="text-green-400">✓ {{ $lastAttempt->correct_count }}</span>
                                    <span class="text-red-400">✗ {{ $lastAttempt->wrong_count }}</span>
                                    <span class="text-gray-400">
                                        {{ round(($lastAttempt->correct_count / ($lastAttempt->correct_count + $lastAttempt->wrong_count)) * 100, 1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-lg p-3">
                            <span class="text-gray-300 text-sm">{{ __('Average Score') }}</span>
                            <span class="font-bold text-white">{{ number_format($userStats['average_score'], 1) }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-lg p-3">
                            <span class="text-gray-300 text-sm">{{ __('Total Correct') }}</span>
                            <span class="font-bold text-green-400">{{ number_format($userStats['correct_answers']) }}</span>
                        </div>
                        <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-lg p-3">
                            <span class="text-gray-300 text-sm">{{ __('Total Questions') }}</span>
                            <span class="font-bold text-white">{{ number_format($userStats['total_answers']) }}</span>
                        </div>
                    </div>

                    <div class="mt-5">
                        <a href="{{ route('manager.education.stats') }}" 
                           class="block w-full text-center px-4 py-3 bg-gradient-to-r from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 text-white rounded-xl hover:from-emerald-500/30 hover:to-teal-500/30 transition font-semibold">
                            {{ __('View Full Statistics') }}
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
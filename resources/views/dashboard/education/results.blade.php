<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    {{ __('Quiz Completed!') }}
                </h1>
                <p class="text-gray-600">{{ __('Here are your results') }}</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <!-- Score -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg p-6 text-white text-center">
                    <div class="text-4xl font-bold">{{ $attemptStats['score'] }}</div>
                    <div class="text-sm opacity-90">{{ __('Points') }}</div>
                </div>

                <!-- Correct -->
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $attemptStats['correct'] }}</div>
                    <div class="text-sm text-gray-600">{{ __('Correct') }}</div>
                </div>

                <!-- Wrong -->
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $attemptStats['wrong'] }}</div>
                    <div class="text-sm text-gray-600">{{ __('Wrong') }}</div>
                </div>

                <!-- Accuracy -->
                <div class="bg-white rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ $attemptStats['accuracy'] }}%</div>
                    <div class="text-sm text-gray-600">{{ __('Accuracy') }}</div>
                </div>
            </div>

           <!-- Recompensa ganada - MEJORADO -->
@php
    $rewardsService = app(\App\Services\Education\QuizRewardsService::class);
    $coinsEarned = $rewardsService->calculateCoinsFromPoints($attemptStats['score']);
    $wallet = Auth::user()->wallets()->where('currency', 'CAN')->first();
    $currentBalance = $wallet ? $wallet->balance : 0;
@endphp

@if($coinsEarned > 0)
<div class="bg-gradient-to-r from-yellow-400 via-yellow-500 to-amber-500 rounded-lg shadow-xl p-8 mb-6">
    <div class="text-center">
        <div class="mb-4">
            <svg class="w-20 h-20 mx-auto text-white animate-bounce" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
            </svg>
        </div>
        
        <h3 class="text-3xl font-bold text-white mb-2">
            🎉 {{ __('You earned') }} {{ number_format($coinsEarned, 1) }} {{ __('CAN Coins!') }}
        </h3>
        
        <p class="text-yellow-100 text-lg mb-4">
            {{ __('Based on your') }} {{ $attemptStats['score'] }} {{ __('points') }}
        </p>
        
        <div class="bg-white/20 backdrop-blur-sm rounded-lg p-4 inline-block">
            <p class="text-white text-sm mb-1">{{ __('Your new balance') }}</p>
            <p class="text-3xl font-bold text-white">
                💰 {{ number_format($currentBalance, 1) }} CAN
            </p>
        </div>
        
        <div class="mt-4 text-yellow-100 text-sm">
            <p>{{ __('Keep playing to earn more coins and climb the ranking!') }}</p>
        </div>
    </div>
</div>
@else
<div class="bg-gray-100 rounded-lg p-6 mb-6 text-center">
    <p class="text-gray-600">{{ __('Complete more questions correctly to earn coins!') }}</p>
</div>
@endif

            <!-- Actions -->
            <div class="flex gap-4 justify-center">
                <a href="{{ route('dashboard.education.index') }}" 
                   class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    {{ __('Play Again') }}
                </a>
                <a href="{{ route('dashboard.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                    {{ __('Back to Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
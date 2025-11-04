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

           <!-- Recompensa ganada -->
            @php
                $coinsEarned = floor($attemptStats['score'] * 0.1);
                $wallet = Auth::user()->wallets()->where('currency', 'CAN')->first();
                $currentBalance = $wallet ? $wallet->balance : 0;
            @endphp

            <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg shadow-lg p-8 mb-6 text-center">
                <div class="text-white">
                    <div class="text-6xl mb-2">💰</div>
                    <h3 class="text-2xl font-bold mb-2">¡Recompensa Ganada!</h3>
                    <div class="text-5xl font-black mb-3">{{ number_format($coinsEarned, 2) }} CAN</div>
                    <p class="text-lg opacity-90">Balance actual: {{ number_format($currentBalance, 2) }} CAN</p>
                </div>
            </div>

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
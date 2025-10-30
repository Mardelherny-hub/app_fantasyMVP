<div>
    {{-- Filtros de período --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">🏆 {{ __('Leaderboard') }}</h2>
            <button wire:click="refresh" 
                    class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ __('Refresh') }}
            </button>
        </div>

        <div class="mt-4 flex space-x-2">
            <button wire:click="setPeriod('all_time')"
                    class="px-4 py-2 rounded-lg font-semibold transition duration-200
                        {{ $period === 'all_time' 
                            ? 'bg-emerald-500 text-white' 
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ __('All Time') }}
            </button>
            <button wire:click="setPeriod('monthly')"
                    class="px-4 py-2 rounded-lg font-semibold transition duration-200
                        {{ $period === 'monthly' 
                            ? 'bg-emerald-500 text-white' 
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ __('This Month') }}
            </button>
            <button wire:click="setPeriod('weekly')"
                    class="px-4 py-2 rounded-lg font-semibold transition duration-200
                        {{ $period === 'weekly' 
                            ? 'bg-emerald-500 text-white' 
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                {{ __('This Week') }}
            </button>
        </div>
    </div>

    {{-- Estadísticas del usuario --}}
    @if($userStats)
        <div class="mb-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-sm text-blue-100 mb-1">{{ __('Your Position') }}</p>
                    <p class="text-3xl font-bold">#{{ $userPosition }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-blue-100 mb-1">{{ __('Total Score') }}</p>
                    <p class="text-3xl font-bold">{{ number_format($userStats['total_score']) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-blue-100 mb-1">{{ __('Quizzes') }}</p>
                    <p class="text-3xl font-bold">{{ $userStats['total_attempts'] }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-blue-100 mb-1">{{ __('Accuracy') }}</p>
                    <p class="text-3xl font-bold">{{ number_format($userStats['accuracy'], 1) }}%</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Tabla de ranking --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        @if(count($leaderboard) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Rank') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Player') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Score') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Quizzes') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('Accuracy') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($leaderboard as $leader)
                            <tr class="{{ $leader['user_id'] === Auth::id() ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-2xl font-bold
                                            {{ $leader['rank'] === 1 ? 'text-yellow-500' : '' }}
                                            {{ $leader['rank'] === 2 ? 'text-gray-400' : '' }}
                                            {{ $leader['rank'] === 3 ? 'text-orange-600' : '' }}
                                            {{ $leader['rank'] > 3 ? 'text-gray-600' : '' }}">
                                            @if($leader['rank'] === 1) 🥇
                                            @elseif($leader['rank'] === 2) 🥈
                                            @elseif($leader['rank'] === 3) 🥉
                                            @else {{ $leader['rank'] }}
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $leader['username'] }}
                                                @if($leader['user_id'] === Auth::id())
                                                    <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                        {{ __('You') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-lg font-bold text-emerald-600">
                                        {{ number_format($leader['total_score']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm text-gray-600">
                                        {{ $leader['total_attempts'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center">
                                        <span class="text-sm font-semibold
                                            {{ $leader['accuracy'] >= 80 ? 'text-green-600' : '' }}
                                            {{ $leader['accuracy'] >= 60 && $leader['accuracy'] < 80 ? 'text-yellow-600' : '' }}
                                            {{ $leader['accuracy'] < 60 ? 'text-red-600' : '' }}">
                                            {{ number_format($leader['accuracy'], 1) }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 text-lg">{{ __('No ranking data available yet.') }}</p>
                <p class="text-gray-400 text-sm mt-2">{{ __('Be the first to play and top the leaderboard!') }}</p>
            </div>
        @endif
    </div>

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
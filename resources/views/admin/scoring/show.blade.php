{{-- resources/views/admin/scoring/show.blade.php --}}
<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Gameweek') }} {{ $gameweek->number }} - {{ __('Scoring') }}</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $gameweek->season->name }}</p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('admin.scoring.index', ['locale' => app()->getLocale()]) }}" 
                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        {{ __('Back') }}
                    </a>
                    
                    <form action="{{ route('admin.scoring.process', ['locale' => app()->getLocale(), 'gameweek' => $gameweek->id]) }}" 
                        method="POST" class="inline-block">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('{{ __('Process scoring for this gameweek?') }}')"
                                class="px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/50 transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('Process Scoring') }}
                            </span>
                        </button>
                    </form>
                    
                    <form action="{{ route('admin.scoring.recalculate', ['locale' => app()->getLocale(), 'gameweek' => $gameweek->id]) }}" 
                        method="POST" class="inline-block">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('{{ __('This will delete and recalculate all points. Continue?') }}')"
                                class="px-4 py-2 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900/50 transition">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                {{ __('Recalculate') }}
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-400 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-400 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Status') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $gameweek->is_closed ? __('Closed') : __('Open') }}
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Fixtures') }}</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $gameweek->fixtures->count() }}</div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Processed') }}</div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ $gameweek->fixtures->where('status', 1)->count() }}
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Pending') }}</div>
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ $gameweek->fixtures->where('status', 0)->count() }}
                    </div>
                </div>
            </div>

            {{-- Fixtures Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Fixtures') }}</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                {{ __('Home') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                {{ __('Away') }}
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                {{ __('Result') }}
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                {{ __('Status') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($gameweek->fixtures as $fixture)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $fixture->homeTeam->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $fixture->awayTeam->name }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($fixture->status == 1)
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $fixture->home_goals }} - {{ $fixture->away_goals }}
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($fixture->status == 1)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                                        {{ __('Finished') }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                                        {{ __('Pending') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                {{ __('No fixtures') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
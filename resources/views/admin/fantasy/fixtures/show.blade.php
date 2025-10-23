// resources/views/admin/fantasy/fixtures/show.blade.php
<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Fixture Details') }}</h1>
                <p class="text-gray-600 mt-1">{{ $fixture->league->name }} - GW{{ $fixture->gameweek->number }}</p>
            </div>
            @if($fixture->status === 0)
                <form method="POST" action="{{ route('admin.fantasy.fixtures.finish', ['locale' => $locale, 'fixture' => $fixture]) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        {{ __('Mark as Finished') }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <!-- Match Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <!-- Home Team -->
                <div class="text-center flex-1">
                    <div class="mb-2">
                        @if($fixture->homeTeam->emblem_url)
                            <img src="{{ $fixture->homeTeam->emblem_url }}" alt="{{ $fixture->homeTeam->name }}" class="w-20 h-20 mx-auto">
                        @else
                            <div class="w-20 h-20 mx-auto bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-500">{{ substr($fixture->homeTeam->name, 0, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-xl font-semibold">{{ $fixture->homeTeam->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $fixture->homeTeam->user->name }}</p>
                </div>

                <!-- Score -->
                <div class="text-center px-8">
                    <div class="text-5xl font-bold text-gray-900">
                        {{ $fixture->home_goals }} - {{ $fixture->away_goals }}
                    </div>
                    <div class="mt-2">
                        @if($fixture->status === 0)
                            <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full">{{ __('Pending') }}</span>
                        @else
                            <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">{{ __('Finished') }}</span>
                        @endif
                    </div>
                    @if($fixture->is_playoff)
                        <div class="mt-2">
                            <span class="px-3 py-1 text-sm bg-purple-100 text-purple-800 rounded-full">
                                @switch($fixture->playoff_round)
                                    @case(1) {{ __('Quarter Final') }} @break
                                    @case(2) {{ __('Semi Final') }} @break
                                    @case(3) {{ __('Final') }} @break
                                @endswitch
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Away Team -->
                <div class="text-center flex-1">
                    <div class="mb-2">
                        @if($fixture->awayTeam->emblem_url)
                            <img src="{{ $fixture->awayTeam->emblem_url }}" alt="{{ $fixture->awayTeam->name }}" class="w-20 h-20 mx-auto">
                        @else
                            <div class="w-20 h-20 mx-auto bg-gray-200 rounded-full flex items-center justify-center">
                                <span class="text-2xl font-bold text-gray-500">{{ substr($fixture->awayTeam->name, 0, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    <h3 class="text-xl font-semibold">{{ $fixture->awayTeam->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $fixture->awayTeam->user->name }}</p>
                </div>
            </div>
        </div>

        <!-- Gameweek Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">{{ __('Gameweek Information') }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">{{ __('Number') }}</p>
                    <p class="font-semibold">{{ $fixture->gameweek->number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('Period') }}</p>
                    <p class="font-semibold">
                        {{ $fixture->gameweek->starts_at->format('d/m/Y') }} - 
                        {{ $fixture->gameweek->ends_at->format('d/m/Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('League') }}</p>
                    <p class="font-semibold">{{ $fixture->league->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">{{ __('Status') }}</p>
                    <p class="font-semibold">
                        @if($fixture->gameweek->is_closed)
                            <span class="text-red-600">{{ __('Closed') }}</span>
                        @else
                            <span class="text-green-600">{{ __('Open') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Stats -->
        <div class="grid grid-cols-2 gap-6">
            <!-- Home Team Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">{{ $fixture->homeTeam->name }}</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Total Points') }}</span>
                        <span class="font-semibold">{{ $fixture->homeTeam->total_points }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Budget') }}</span>
                        <span class="font-semibold">${{ number_format($fixture->homeTeam->budget, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Away Team Stats -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">{{ $fixture->awayTeam->name }}</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Total Points') }}</span>
                        <span class="font-semibold">{{ $fixture->awayTeam->total_points }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">{{ __('Budget') }}</span>
                        <span class="font-semibold">${{ number_format($fixture->awayTeam->budget, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.fantasy.fixtures.index', $locale) }}" 
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                {{ __('Back to List') }}
            </a>
        </div>
    </div>
</x-admin-layout>
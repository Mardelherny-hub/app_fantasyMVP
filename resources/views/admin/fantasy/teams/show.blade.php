<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $team->name }}
            </h2>
            <a href="{{ route('admin.fantasy.teams.edit', [app()->getLocale(), $team->id]) }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                {{ __('Edit Team') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Información General --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                
                {{-- Tarjeta Principal --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <div class="flex items-center mb-4">
                        @if($team->emblem_url)
                            <img src="{{ $team->emblem_url }}" 
                                 alt="{{ $team->name }}" 
                                 class="h-16 w-16 rounded-full mr-4">
                        @else
                            <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                                <span class="text-2xl text-gray-500 font-bold">
                                    {{ substr($team->name, 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $team->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $team->slug }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('Manager') }}:</span>
                            @if($team->is_bot)
                                <span class="text-sm font-medium text-purple-600">🤖 {{ __('Bot') }}</span>
                            @elseif($team->user)
                                <span class="text-sm font-medium text-gray-900">{{ $team->user->name }}</span>
                            @else
                                <span class="text-sm text-gray-400">{{ __('No owner') }}</span>
                            @endif
                        </div>

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('League') }}:</span>
                            @if($team->league)
                                <span class="text-sm font-medium text-gray-900">{{ $team->league->name }}</span>
                            @else
                                <span class="text-sm text-gray-400">{{ __('No league') }}</span>
                            @endif
                        </div>

                        @if($team->league && $team->league->season)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('Season') }}:</span>
                            <span class="text-sm font-medium text-gray-900">{{ $team->league->season->name }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">{{ __('Squad Status') }}:</span>
                            @if($team->is_squad_complete)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ {{ __('Complete') }}
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    ⚠ {{ __('Incomplete') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Estadísticas --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Statistics') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">{{ __('Total Points') }}</div>
                            <div class="text-3xl font-bold text-indigo-600">
                                {{ number_format($stats['total_points']) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">{{ __('Budget') }}</div>
                            <div class="text-2xl font-bold text-green-600">
                                ${{ number_format($stats['budget'], 2) }}
                            </div>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ __('Fixtures Played') }}:</span>
                            <span class="font-medium">{{ $stats['fixtures_played'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ __('Roster Size') }}:</span>
                            <span class="font-medium">{{ $stats['roster_size'] }} {{ __('players') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Información Adicional --}}
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Details') }}</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Created') }}:</span>
                            <span class="font-medium">{{ $team->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('Last Update') }}:</span>
                            <span class="font-medium">{{ $team->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if($team->user)
                        <div class="flex justify-between">
                            <span class="text-gray-600">{{ __('User Email') }}:</span>
                            <span class="font-medium">{{ $team->user->email }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Roster Actual --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Current Lineup') }}</h3>
                    
                    @if($starters->count() > 0 || $bench->count() > 0)
                        {{-- Titulares --}}
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-3">{{ __('Starters') }} ({{ $starters->count() }})</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                                @foreach($starters as $roster)
                                    <div class="border border-gray-200 rounded-lg p-3 hover:border-indigo-300 transition">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $roster->player->full_name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $roster->player->position_label }} • Slot {{ $roster->slot }}
                                                </div>
                                            </div>
                                            @if($roster->captaincy == 1)
                                                <span class="text-xs font-bold text-yellow-600">C</span>
                                            @elseif($roster->captaincy == 2)
                                                <span class="text-xs font-bold text-gray-600">VC</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Suplentes --}}
                        @if($bench->count() > 0)
                        <div>
                            <h4 class="text-md font-semibold text-gray-700 mb-3">{{ __('Bench') }} ({{ $bench->count() }})</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                                @foreach($bench as $roster)
                                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                        <div class="text-sm font-medium text-gray-700">
                                            {{ $roster->player->full_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $roster->player->position_label }} • Slot {{ $roster->slot }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @else
                        <p class="text-gray-500 text-center py-8">
                            {{ __('No lineup set for this team yet.') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Recent Fixtures --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Recent Fixtures') }}</h3>
                    
                    @php
                        $recentFixtures = $team->homeFixtures()
                            ->union($team->awayFixtures())
                            ->with(['homeTeam', 'awayTeam', 'gameweek'])
                            ->orderBy('gameweek_id', 'desc')
                            ->limit(10)
                            ->get();
                    @endphp

                    @if($recentFixtures->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentFixtures as $fixture)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 flex-1">
                                            <span class="text-xs font-medium text-gray-500">
                                                GW{{ $fixture->gameweek->number }}
                                            </span>
                                            <div class="flex items-center space-x-2 flex-1">
                                                <span class="font-medium {{ $fixture->home_fantasy_team_id == $team->id ? 'text-indigo-600' : '' }}">
                                                    {{ $fixture->homeTeam->name }}
                                                </span>
                                                <span class="text-gray-500">vs</span>
                                                <span class="font-medium {{ $fixture->away_fantasy_team_id == $team->id ? 'text-indigo-600' : '' }}">
                                                    {{ $fixture->awayTeam->name }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if($fixture->status == 1)
                                                <div class="text-lg font-bold text-gray-900">
                                                    {{ $fixture->home_goals }} - {{ $fixture->away_goals }}
                                                </div>
                                                <span class="text-xs text-green-600">{{ __('Finished') }}</span>
                                            @else
                                                <span class="text-sm text-gray-500">{{ __('Pending') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">
                            {{ __('No fixtures found for this team.') }}
                        </p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
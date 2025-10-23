<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Match Events') }}: {{ $match->fixture->homeTeam->name }} vs {{ $match->fixture->awayTeam->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Match Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="text-center">
                        <div class="flex justify-center items-center gap-8 mb-4">
                            <div class="text-2xl font-bold">{{ $match->fixture->homeTeam->name }}</div>
                            <div class="text-4xl font-bold">{{ $match->home_score ?? 0 }} - {{ $match->away_score ?? 0 }}</div>
                            <div class="text-2xl font-bold">{{ $match->fixture->awayTeam->name }}</div>
                        </div>
                        <span class="px-4 py-2 text-sm rounded-full 
                            {{ $match->status == 'finished' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $match->status == 'live' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ strtoupper($match->status) }}
                            @if($match->status == 'live' && $match->minute)
                                - {{ $match->minute }}'
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Add Event Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Add Event') }}</h3>
                    <form method="POST" action="{{ route('admin.cpl.matches.events.store', [app()->getLocale(), $match]) }}" class="grid grid-cols-5 gap-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-bold mb-2">{{ __('Type') }}</label>
                            <select name="type" required class="w-full rounded border-gray-300">
                                <option value="goal">⚽ Goal</option>
                                <option value="assist">🅰️ Assist</option>
                                <option value="yellow">🟨 Yellow</option>
                                <option value="red">🟥 Red</option>
                                <option value="own_goal">🥅 Own Goal</option>
                                <option value="penalty_scored">⚽🎯 Penalty Scored</option>
                                <option value="penalty_missed">❌ Penalty Missed</option>
                                <option value="sub_in">🔼 Sub In</option>
                                <option value="sub_out">🔽 Sub Out</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">{{ __('Team') }}</label>
                            <select name="real_team_id" id="team_select" required class="w-full rounded border-gray-300">
                                <option value="{{ $match->fixture->home_team_id }}">{{ $match->fixture->homeTeam->name }}</option>
                                <option value="{{ $match->fixture->away_team_id }}">{{ $match->fixture->awayTeam->name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">{{ __('Player') }}</label>
                            <select name="real_player_id" id="player_select" required class="w-full rounded border-gray-300">
                                <optgroup label="{{ $match->fixture->homeTeam->name }}" data-team="{{ $match->fixture->home_team_id }}">
                                    @foreach($homePlayers as $player)
                                        <option value="{{ $player->id }}" data-team="{{ $match->fixture->home_team_id }}">
                                            {{ $player->full_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="{{ $match->fixture->awayTeam->name }}" data-team="{{ $match->fixture->away_team_id }}">
                                    @foreach($awayPlayers as $player)
                                        <option value="{{ $player->id }}" data-team="{{ $match->fixture->away_team_id }}">
                                            {{ $player->full_name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">{{ __('Minute') }}</label>
                            <input type="number" name="minute" required min="0" max="120" class="w-full rounded border-gray-300">
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Add Event') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Events Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Events') }} ({{ $match->events->count() }})</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Minute') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Player') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Team') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($match->events->sortBy('minute') as $event)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">{{ $event->minute }}'</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $event->icon }} {{ $event->type_name }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $event->player->full_name }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $event->team->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <form action="{{ route('admin.cpl.matches.events.destroy', [app()->getLocale(), $event]) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('{{ __('Are you sure?') }}')">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        {{ __('No events yet') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Process Stats Button -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.cpl.matches.events.process', [app()->getLocale(), $match]) }}">
                        @csrf
                        <button type="submit" 
                                class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded text-lg"
                                onclick="return confirm('{{ __('Process events and generate PlayerMatchStats?') }}')">
                            {{ __('Process Stats') }} ✅
                        </button>
                    </form>
                    <p class="text-sm text-gray-600 mt-2 text-center">
                        {{ __('This will process all events and generate PlayerMatchStats for scoring calculation') }}
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('admin.cpl.matches.index', app()->getLocale()) }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Back to Matches') }}
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
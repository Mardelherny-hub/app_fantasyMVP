<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Match Details') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-matches.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Matches') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Details') }}</span>
                        </nav>
                    </div>
                    <a href="{{ route('admin.real-matches.index', app()->getLocale()) }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        {{ __('Back') }}
                    </a>
                </div>
            </div>

            {{-- Resultado del Partido --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-6">
                <div class="flex items-center justify-center mb-6">
                    <div class="text-center flex-1">
                        <div class="text-3xl font-bold text-gray-900">{{ $realMatch->fixture->homeTeam->name ?? 'TBD' }}</div>
                        <div class="text-sm text-gray-500 mt-2">{{ $realMatch->fixture->homeTeam->country ?? '' }}</div>
                    </div>
                    
                    <div class="px-12">
                        <div class="text-6xl font-bold text-gray-900">
                            {{ $realMatch->home_score }} - {{ $realMatch->away_score }}
                        </div>
                        <div class="text-center mt-3">
                            @php
                                $statusColors = [
                                    'live' => 'bg-green-100 text-green-800',
                                    'ht' => 'bg-yellow-100 text-yellow-800',
                                    'ft' => 'bg-blue-100 text-blue-800',
                                    'finished' => 'bg-gray-100 text-gray-800',
                                    'postponed' => 'bg-yellow-100 text-yellow-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $color = $statusColors[$realMatch->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-4 py-2 text-sm font-medium rounded-full {{ $color }}">
                                {{ __(strtoupper($realMatch->status)) }}
                                @if($realMatch->status === 'live' && $realMatch->minute)
                                    - {{ $realMatch->minute }}'
                                @endif
                            </span>
                        </div>
                    </div>
                    
                    <div class="text-center flex-1">
                        <div class="text-3xl font-bold text-gray-900">{{ $realMatch->fixture->awayTeam->name ?? 'TBD' }}</div>
                        <div class="text-sm text-gray-500 mt-2">{{ $realMatch->fixture->awayTeam->country ?? '' }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-6 border-t text-center">
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Competition') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">{{ $realMatch->fixture->competition->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Date') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">
                            {{ $realMatch->started_at_utc ? $realMatch->started_at_utc->format('d/m/Y H:i') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Round') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">{{ $realMatch->fixture->round ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Venue') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">{{ $realMatch->fixture->venue ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Eventos del Partido --}}
            @if($realMatch->events->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Match Events') }}</h2>
                    
                    <div class="space-y-3">
                        @foreach($realMatch->events->sortBy('minute') as $event)
                            <div class="flex items-center space-x-4 py-2 border-b last:border-0">
                                <div class="w-12 text-center font-bold text-gray-700">
                                    {{ $event->minute }}'
                                </div>
                                
                                <div class="w-8 text-center">
                                    @if($event->type === 'goal')
                                        <span class="text-2xl">⚽</span>
                                    @elseif($event->type === 'yellow')
                                        <span class="text-2xl">🟨</span>
                                    @elseif($event->type === 'red')
                                        <span class="text-2xl">🟥</span>
                                    @elseif(in_array($event->type, ['sub_in', 'sub_out']))
                                        <span class="text-2xl">🔄</span>
                                    @else
                                        <span class="text-gray-400">•</span>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">{{ $event->player->full_name ?? 'Unknown' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $event->team->name ?? '' }} - {{ __(ucfirst(str_replace('_', ' ', $event->type))) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Alineaciones --}}
            @if($realMatch->lineups->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    
                    {{-- Equipo Local --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ $realMatch->fixture->homeTeam->name ?? 'Home' }}
                        </h3>
                        
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Starters') }}</h4>
                            <div class="space-y-2">
                                @foreach($realMatch->lineups->where('team_id', $realMatch->fixture->home_team_id)->where('starter', true) as $lineup)
                                    <div class="flex items-center space-x-3 text-sm">
                                        <span class="w-8 text-center font-medium text-gray-700">{{ $lineup->shirt_number }}</span>
                                        <span class="flex-1">{{ $lineup->player->full_name ?? 'Unknown' }}</span>
                                        <span class="text-gray-500">{{ $lineup->position }}</span>
                                        @if($lineup->minutes)
                                            <span class="text-xs text-gray-400">{{ $lineup->minutes }}'</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Substitutes') }}</h4>
                            <div class="space-y-2">
                                @foreach($realMatch->lineups->where('team_id', $realMatch->fixture->home_team_id)->where('starter', false) as $lineup)
                                    <div class="flex items-center space-x-3 text-sm">
                                        <span class="w-8 text-center font-medium text-gray-700">{{ $lineup->shirt_number }}</span>
                                        <span class="flex-1">{{ $lineup->player->full_name ?? 'Unknown' }}</span>
                                        <span class="text-gray-500">{{ $lineup->position }}</span>
                                        @if($lineup->minutes)
                                            <span class="text-xs text-gray-400">{{ $lineup->minutes }}'</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Equipo Visitante --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            {{ $realMatch->fixture->awayTeam->name ?? 'Away' }}
                        </h3>
                        
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Starters') }}</h4>
                            <div class="space-y-2">
                                @foreach($realMatch->lineups->where('team_id', $realMatch->fixture->away_team_id)->where('starter', true) as $lineup)
                                    <div class="flex items-center space-x-3 text-sm">
                                        <span class="w-8 text-center font-medium text-gray-700">{{ $lineup->shirt_number }}</span>
                                        <span class="flex-1">{{ $lineup->player->full_name ?? 'Unknown' }}</span>
                                        <span class="text-gray-500">{{ $lineup->position }}</span>
                                        @if($lineup->minutes)
                                            <span class="text-xs text-gray-400">{{ $lineup->minutes }}'</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Substitutes') }}</h4>
                            <div class="space-y-2">
                                @foreach($realMatch->lineups->where('team_id', $realMatch->fixture->away_team_id)->where('starter', false) as $lineup)
                                    <div class="flex items-center space-x-3 text-sm">
                                        <span class="w-8 text-center font-medium text-gray-700">{{ $lineup->shirt_number }}</span>
                                        <span class="flex-1">{{ $lineup->player->full_name ?? 'Unknown' }}</span>
                                        <span class="text-gray-500">{{ $lineup->position }}</span>
                                        @if($lineup->minutes)
                                            <span class="text-xs text-gray-400">{{ $lineup->minutes }}'</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            @endif

            {{-- Estadísticas de Jugadores --}}
            @if($realMatch->stats->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Player Statistics') }}</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Player') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Team') }}</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Min') }}</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">⚽</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">🎯</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">🟨</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">🟥</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Rating') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($realMatch->stats->sortByDesc('rating') as $stat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                            {{ $stat->player->full_name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500">
                                            {{ $stat->team->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-900">
                                            {{ $stat->minutes ?? 0 }}
                                        </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-900">
                                            {{ $stat->goals ?? 0 }}
                                        </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-900">
                                            {{ $stat->assists ?? 0 }}
                                        </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-900">
                                            {{ $stat->yellow_cards ?? 0 }}
                                        </td>
                                        <td class="px-4 py-2 text-center text-sm text-gray-900">
                                            {{ $stat->red_cards ?? 0 }}
                                        </td>
                                        <td class="px-4 py-2 text-center text-sm">
                                            @if($stat->rating)
                                                <span class="font-semibold text-gray-900">{{ number_format($stat->rating / 100, 1) }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Player Details') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Real Players') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Details') }}</span>
                        </nav>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.real-players.edit', [app()->getLocale(), $realPlayer]) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Perfil del jugador --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start space-x-6">
                    @if($realPlayer->photo_url)
                        <img src="{{ $realPlayer->photo_url }}" alt="{{ $realPlayer->full_name }}" class="w-32 h-32 rounded-lg object-cover">
                    @else
                        <div class="w-32 h-32 rounded-lg bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500 font-medium text-3xl">{{ substr($realPlayer->full_name, 0, 2) }}</span>
                        </div>
                    @endif
                    
                    <div class="flex-1">
                        <h2 class="text-3xl font-bold text-gray-900">{{ $realPlayer->full_name }}</h2>
                        
                        <div class="flex items-center space-x-4 mt-3">
                            <span class="px-3 py-1 text-sm font-medium rounded-full 
                                {{ $realPlayer->position === 'GK' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $realPlayer->position === 'DF' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $realPlayer->position === 'MF' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $realPlayer->position === 'FW' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $realPlayer->position }}
                            </span>
                            @if($realPlayer->nationality)
                                <span class="text-sm text-gray-600">{{ $realPlayer->nationality }}</span>
                            @endif
                            @if($realPlayer->birthdate)
                                <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($realPlayer->birthdate)->age }} {{ __('years') }}</span>
                            @endif
                        </div>

                        @php
                            $currentMembership = $realPlayer->memberships->firstWhere('to_date', null);
                        @endphp

                        @if($currentMembership)
                            <div class="mt-4 flex items-center space-x-2">
                                <span class="text-sm text-gray-500">{{ __('Current Team') }}:</span>
                                <a href="{{ route('admin.real-teams.show', [app()->getLocale(), $currentMembership->team]) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    {{ $currentMembership->team->name }}
                                    @if($currentMembership->shirt_number)
                                        (#{{ $currentMembership->shirt_number }})
                                    @endif
                                </a>
                            </div>
                        @else
                            <div class="mt-4">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{{ __('Free Agent') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Datos técnicos --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Technical Data') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">{{ __('External ID') }}</span>
                        <div class="text-sm font-medium text-gray-900 mt-1">{{ $realPlayer->external_id ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">{{ __('Created At') }}</span>
                        <div class="text-sm font-medium text-gray-900 mt-1">{{ $realPlayer->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">{{ __('Updated At') }}</span>
                        <div class="text-sm font-medium text-gray-900 mt-1">{{ $realPlayer->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Historial de equipos --}}
            @if($realPlayer->memberships->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Team History') }}</h3>
                    
                    <div class="space-y-3">
                        @foreach($realPlayer->memberships->sortByDesc('from_date') as $membership)
                            <div class="flex items-center justify-between py-3 border-b last:border-0">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 text-center">
                                        @if($membership->shirt_number)
                                            <span class="text-2xl font-bold text-gray-700">#{{ $membership->shirt_number }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $membership->team->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $membership->season->name }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-900">
                                        {{ $membership->from_date->format('d/m/Y') }}
                                        @if($membership->to_date)
                                            → {{ $membership->to_date->format('d/m/Y') }}
                                        @else
                                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">{{ __('Current') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Estadísticas de partidos --}}
            @if($realPlayer->stats->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Career Statistics') }}</h3>
                    
                    @php
                        $totalMatches = $realPlayer->stats->count();
                        $totalGoals = $realPlayer->stats->sum('goals');
                        $totalAssists = $realPlayer->stats->sum('assists');
                        $totalYellowCards = $realPlayer->stats->sum('yellow_cards');
                        $totalRedCards = $realPlayer->stats->sum('red_cards');
                        $totalMinutes = $realPlayer->stats->sum('minutes');
                        $avgRating = $realPlayer->stats->where('rating', '>', 0)->avg('rating');
                    @endphp

                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gray-900">{{ $totalMatches }}</div>
                            <div class="text-sm text-gray-500">{{ __('Matches') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $totalGoals }}</div>
                            <div class="text-sm text-gray-500">{{ __('Goals') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $totalAssists }}</div>
                            <div class="text-sm text-gray-500">{{ __('Assists') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-gray-900">{{ number_format($totalMinutes / 90, 1) }}</div>
                            <div class="text-sm text-gray-500">{{ __('Full Games') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ $totalYellowCards }}</div>
                            <div class="text-sm text-gray-500">🟨</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $totalRedCards }}</div>
                            <div class="text-sm text-gray-500">🟥</div>
                        </div>
                        @if($avgRating)
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600">{{ number_format($avgRating / 100, 1) }}</div>
                                <div class="text-sm text-gray-500">{{ __('Avg Rating') }}</div>
                            </div>
                        @endif
                    </div>

                    <h4 class="font-medium text-gray-900 mb-3">{{ __('Recent Matches') }}</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ __('Date') }}</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ __('Match') }}</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">{{ __('Min') }}</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">⚽</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">🎯</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">🟨</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">🟥</th>
                                    <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">{{ __('Rating') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($realPlayer->stats->sortByDesc('match.started_at_utc')->take(10) as $stat)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $stat->match->started_at_utc ? $stat->match->started_at_utc->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            <a href="{{ route('admin.real-matches.show', [app()->getLocale(), $stat->match]) }}" 
                                               class="text-blue-600 hover:text-blue-800">
                                                {{ $stat->match->fixture->homeTeam->name ?? 'TBD' }} vs {{ $stat->match->fixture->awayTeam->name ?? 'TBD' }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-2 text-center text-sm">{{ $stat->minutes ?? 0 }}</td>
                                        <td class="px-4 py-2 text-center text-sm">{{ $stat->goals ?? 0 }}</td>
                                        <td class="px-4 py-2 text-center text-sm">{{ $stat->assists ?? 0 }}</td>
                                        <td class="px-4 py-2 text-center text-sm">{{ $stat->yellow_cards ?? 0 }}</td>
                                        <td class="px-4 py-2 text-center text-sm">{{ $stat->red_cards ?? 0 }}</td>
                                        <td class="px-4 py-2 text-center text-sm">
                                            @if($stat->rating)
                                                <span class="font-semibold">{{ number_format($stat->rating / 100, 1) }}</span>
                                            @else
                                                -
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
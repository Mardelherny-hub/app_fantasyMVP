<div>
    {{-- Header --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">
                        {{ __('Calendario de Jornadas') }}
                    </h1>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ $league->name }} - {{ $league->season->name }}
                    </p>
                </div>

                {{-- Toggle View Mode --}}
                <button wire:click="toggleViewMode" 
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition flex items-center gap-2">
                    @if($viewMode === 'grid')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        {{ __('Vista Lista') }}
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                        </svg>
                        {{ __('Vista Grid') }}
                    @endif
                </button>
            </div>
        </div>
    </div>

    {{-- Gameweek Actual Destacada --}}
    @if($currentGameweek)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium opacity-90 mb-1">{{ __('Jornada Actual') }}</div>
                        <div class="text-3xl font-bold mb-2">
                            Gameweek {{ $currentGameweek->number }}
                            @if($currentGameweek->is_playoff)
                                - {{ $currentGameweek->playoff_round_name }}
                            @endif
                        </div>
                        <div class="text-sm opacity-90">
                            {{ $currentGameweek->starts_at->format('d M') }} - {{ $currentGameweek->ends_at->format('d M Y') }}
                        </div>
                    </div>

                    <div class="flex gap-3">
                        @php
                            $currentData = collect($gameweeksData)->firstWhere('is_current', true);
                        @endphp
                        
                        @if($currentData)
                            <button wire:click="viewFixtures({{ $currentGameweek->id }})"
                                    class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg transition">
                                {{ __('Ver Partidos') }}
                            </button>
                            
                            <button wire:click="manageLineup"
                                    class="px-4 py-2 bg-white text-green-600 hover:bg-gray-100 rounded-lg transition font-medium">
                                {{ __('Gestionar Alineación') }} →
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Próxima Gameweek --}}
    @if($nextGameweek && !$currentGameweek)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="text-sm font-medium opacity-90 mb-1">{{ __('Próxima Jornada') }}</div>
                        <div class="text-3xl font-bold mb-2">
                            Gameweek {{ $nextGameweek->number }}
                        </div>
                        @php
                            $nextData = collect($gameweeksData)->firstWhere('is_next', true);
                        @endphp
                        @if($nextData)
                            <div class="text-sm opacity-90">
                                {{ $nextData['time_info']['label'] }}
                            </div>
                        @endif
                    </div>

                    <button wire:click="manageLineup"
                            class="px-4 py-2 bg-white text-blue-600 hover:bg-gray-100 rounded-lg transition font-medium">
                        {{ __('Preparar Alineación') }} →
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Lista/Grid de Gameweeks --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        @if($viewMode === 'grid')
            {{-- Vista Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($gameweeksData as $data)
                    @php
                        $gw = $data['gameweek'];
                        $statusClass = $this->getStatusBadgeClass($data['status']);
                        $statusText = $this->getStatusBadgeText($data['status']);
                    @endphp
                    
                    <div class="bg-white rounded-lg shadow hover:shadow-md transition overflow-hidden border-2 {{ $data['is_current'] ? 'border-green-500' : ($data['is_next'] ? 'border-blue-500' : 'border-gray-200') }}">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r {{ $data['is_current'] ? 'from-green-50 to-green-100' : ($data['is_next'] ? 'from-blue-50 to-blue-100' : 'from-gray-50 to-gray-100') }} px-4 py-3 border-b">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-lg font-bold text-gray-900">GW {{ $gw->number }}</div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full border {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                            @if($gw->is_playoff)
                                <div class="text-xs font-medium text-gray-700">
                                    🏆 {{ $gw->playoff_round_name }}
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="p-4">
                            {{-- Fechas --}}
                            <div class="text-sm text-gray-600 mb-3">
                                <div>{{ $gw->starts_at->format('d M') }} - {{ $gw->ends_at->format('d M Y') }}</div>
                            </div>

                            {{-- Mi Fixture --}}
                            @if($data['my_fixture'])
                                <div class="mb-3 p-2 bg-gray-50 rounded text-xs">
                                    @php
                                        $isHome = $data['my_fixture']->home_fantasy_team_id === $team->id;
                                    @endphp
                                    <div class="flex items-center justify-between">
                                        <span class="{{ $isHome ? 'font-bold text-blue-600' : '' }}">
                                            {{ $data['my_fixture']->homeTeam->name }}
                                        </span>
                                        <span class="font-bold text-gray-400">vs</span>
                                        <span class="{{ !$isHome ? 'font-bold text-blue-600' : '' }}">
                                            {{ $data['my_fixture']->awayTeam->name }}
                                        </span>
                                    </div>
                                    @if($data['my_fixture']->status === 1)
                                        <div class="text-center mt-1 font-bold text-gray-900">
                                            {{ $data['my_fixture']->home_goals }} - {{ $data['my_fixture']->away_goals }}
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Puntos --}}
                            @if($gw->is_closed)
                                <div class="text-center mb-3">
                                    <div class="text-2xl font-bold text-green-600">{{ $data['my_points'] }}</div>
                                    <div class="text-xs text-gray-600">{{ __('puntos') }}</div>
                                </div>
                            @endif

                            {{-- Time Info --}}
                            <div class="text-xs {{ $data['time_info']['class'] }} text-center mb-3">
                                {{ $data['time_info']['label'] }}
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2">
                                @if($data['fixtures_count'] > 0)
                                    <button wire:click="viewFixtures({{ $gw->id }})"
                                            class="flex-1 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded text-xs font-medium transition">
                                        {{ __('Partidos') }}
                                    </button>
                                @endif
                                
                                @if($gw->is_closed)
                                    <button wire:click="viewScores({{ $gw->id }})"
                                            class="flex-1 px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded text-xs font-medium transition">
                                        {{ __('Puntos') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Vista Lista --}}
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GW</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Periodo') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Estado') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Mi Partido') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Puntos') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($gameweeksData as $data)
                                @php
                                    $gw = $data['gameweek'];
                                    $statusClass = $this->getStatusBadgeClass($data['status']);
                                    $statusText = $this->getStatusBadgeText($data['status']);
                                @endphp
                                
                                <tr class="hover:bg-gray-50 {{ $data['is_current'] ? 'bg-green-50' : ($data['is_next'] ? 'bg-blue-50' : '') }}">
                                    {{-- GW Number --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold text-gray-900">{{ $gw->number }}</span>
                                            @if($gw->is_playoff)
                                                <span class="text-xs">🏆</span>
                                            @endif
                                        </div>
                                        @if($gw->is_playoff)
                                            <div class="text-xs text-gray-600">{{ $gw->playoff_round_name }}</div>
                                        @endif
                                    </td>

                                    {{-- Periodo --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        <div>{{ $gw->starts_at->format('d M') }} - {{ $gw->ends_at->format('d M Y') }}</div>
                                        <div class="text-xs {{ $data['time_info']['class'] }}">
                                            {{ $data['time_info']['label'] }}
                                        </div>
                                    </td>

                                    {{-- Estado --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>

                                    {{-- Mi Fixture --}}
                                    <td class="px-6 py-4 text-sm">
                                        @if($data['my_fixture'])
                                            @php
                                                $isHome = $data['my_fixture']->home_fantasy_team_id === $team->id;
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <span class="{{ $isHome ? 'font-bold' : '' }}">
                                                    {{ $data['my_fixture']->homeTeam->name }}
                                                </span>
                                                @if($data['my_fixture']->status === 1)
                                                    <span class="font-bold">{{ $data['my_fixture']->home_goals }}-{{ $data['my_fixture']->away_goals }}</span>
                                                @else
                                                    <span class="text-gray-400">vs</span>
                                                @endif
                                                <span class="{{ !$isHome ? 'font-bold' : '' }}">
                                                    {{ $data['my_fixture']->awayTeam->name }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Puntos --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($gw->is_closed)
                                            <span class="text-xl font-bold text-green-600">{{ $data['my_points'] }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <div class="flex gap-2 justify-end">
                                            @if($data['fixtures_count'] > 0)
                                                <button wire:click="viewFixtures({{ $gw->id }})"
                                                        class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded font-medium transition">
                                                    {{ __('Partidos') }}
                                                </button>
                                            @endif
                                            
                                            @if($gw->is_closed)
                                                <button wire:click="viewScores({{ $gw->id }})"
                                                        class="px-3 py-1 bg-green-50 text-green-600 hover:bg-green-100 rounded font-medium transition">
                                                    {{ __('Puntos') }}
                                                </button>
                                            @endif
                                        </div>
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
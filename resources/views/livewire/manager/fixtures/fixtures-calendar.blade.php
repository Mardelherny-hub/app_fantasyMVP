<div>
    {{-- Header --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ __('Mis Partidos') }}
                </h1>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex flex-wrap gap-4 items-center">
                {{-- Filtro por Gameweek --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Gameweek') }}
                    </label>
                    <select wire:model.live="selectedGameweekId" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Todas las jornadas') }}</option>
                        @foreach($gameweeks as $gw)
                            <option value="{{ $gw->id }}">
                                GW {{ $gw->number }} - {{ $gw->starts_at->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filtro por Estado --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ __('Estado') }}
                    </label>
                    <select wire:model.live="statusFilter" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="all">{{ __('Todos') }}</option>
                        <option value="pending">{{ __('Pendientes') }}</option>
                        <option value="finished">{{ __('Finalizados') }}</option>
                    </select>
                </div>

                {{-- Botón limpiar filtros --}}
                @if($selectedGameweekId || $statusFilter !== 'all')
                    <div class="flex items-end">
                        <button wire:click="clearFilters" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                            {{ __('Limpiar Filtros') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Loading State --}}
    @if($loading)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-4 text-gray-600">{{ __('Cargando partidos...') }}</p>
            </div>
        </div>
    @else
        {{-- Próximos Partidos --}}
        @if($upcomingFixtures->count() > 0)
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    {{ __('Próximos Partidos') }}
                </h2>
                
                <div class="space-y-4">
                    @foreach($upcomingFixtures as $fixture)
                        <div class="bg-white rounded-lg shadow hover:shadow-md transition cursor-pointer"
                             wire:click="viewFixture({{ $fixture->id }})">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    {{-- Gameweek Badge --}}
                                    <div class="text-sm font-medium text-blue-600 mb-2">
                                        GW {{ $fixture->gameweek->number }}
                                    </div>
                                    
                                    {{-- Fecha --}}
                                    <div class="text-sm text-gray-500">
                                        {{ $fixture->gameweek->starts_at->format('d/m/Y') }}
                                    </div>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    {{-- Equipo Local --}}
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900 {{ $this->isHomeTeam($fixture) ? 'text-blue-600' : '' }}">
                                            {{ $fixture->homeTeam->name }}
                                        </div>
                                        @if($this->isHomeTeam($fixture))
                                            <div class="text-xs text-blue-600 font-medium mt-1">{{ __('(TÚ)') }}</div>
                                        @endif
                                    </div>

                                    {{-- VS --}}
                                    <div class="px-6">
                                        <span class="text-2xl font-bold text-gray-400">VS</span>
                                    </div>

                                    {{-- Equipo Visitante --}}
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900 {{ !$this->isHomeTeam($fixture) ? 'text-blue-600' : '' }}">
                                            {{ $fixture->awayTeam->name }}
                                        </div>
                                        @if(!$this->isHomeTeam($fixture))
                                            <div class="text-xs text-blue-600 font-medium mt-1">{{ __('(TÚ)') }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones --}}
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        {{ __('Ver Alineación') }} →
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Resultados Recientes --}}
        @if($recentFixtures->count() > 0)
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ __('Resultados Recientes') }}
                </h2>
                
                <div class="space-y-4">
                    @foreach($recentFixtures as $fixture)
                        @php
                            $outcome = $this->getMatchOutcome($fixture);
                            $outcomeClass = match($outcome) {
                                'W' => 'bg-green-50 border-green-200',
                                'D' => 'bg-yellow-50 border-yellow-200',
                                'L' => 'bg-red-50 border-red-200',
                                default => 'bg-white border-gray-200'
                            };
                        @endphp
                        
                        <div class="rounded-lg shadow hover:shadow-md transition cursor-pointer border-2 {{ $outcomeClass }}"
                             wire:click="viewFixture({{ $fixture->id }})">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    {{-- Gameweek Badge --}}
                                    <div class="text-sm font-medium text-gray-600 mb-2">
                                        GW {{ $fixture->gameweek->number }} - {{ __('Finalizado') }}
                                    </div>
                                    
                                    {{-- Resultado Badge --}}
                                    @if($outcome)
                                        <div class="px-3 py-1 rounded-full text-sm font-semibold
                                            {{ $outcome === 'W' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $outcome === 'D' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                            {{ $outcome === 'L' ? 'bg-red-100 text-red-700' : '' }}">
                                            @if($outcome === 'W') ✓ {{ __('Victoria') }}
                                            @elseif($outcome === 'D') = {{ __('Empate') }}
                                            @else ✗ {{ __('Derrota') }}
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    {{-- Equipo Local --}}
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900 {{ $this->isHomeTeam($fixture) ? 'text-blue-600' : '' }}">
                                            {{ $fixture->homeTeam->name }}
                                        </div>
                                        @if($this->isHomeTeam($fixture))
                                            <div class="text-xs text-blue-600 font-medium mt-1">{{ __('(TÚ)') }}</div>
                                        @endif
                                    </div>

                                    {{-- Marcador --}}
                                    <div class="px-6">
                                        <div class="text-3xl font-bold text-gray-900">
                                            {{ $fixture->home_goals }} - {{ $fixture->away_goals }}
                                        </div>
                                    </div>

                                    {{-- Equipo Visitante --}}
                                    <div class="flex-1 text-center">
                                        <div class="font-semibold text-gray-900 {{ !$this->isHomeTeam($fixture) ? 'text-blue-600' : '' }}">
                                            {{ $fixture->awayTeam->name }}
                                        </div>
                                        @if(!$this->isHomeTeam($fixture))
                                            <div class="text-xs text-blue-600 font-medium mt-1">{{ __('(TÚ)') }}</div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Acciones --}}
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        {{ __('Ver Detalle Completo') }} →
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Todos los Fixtures (con paginación) --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ __('Todos los Partidos') }}
            </h2>

            @if($allFixtures->count() > 0)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GW</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Local') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Resultado') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Visitante') }}</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Estado') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acción') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($allFixtures as $fixture)
                                <tr class="hover:bg-gray-50 cursor-pointer" wire:click="viewFixture({{ $fixture->id }})">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $fixture->gameweek->number }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ $this->isHomeTeam($fixture) ? 'font-bold text-blue-600' : 'text-gray-900' }}">
                                        {{ $fixture->homeTeam->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($fixture->status === 0)
                                            <span class="text-gray-400 font-medium">VS</span>
                                        @else
                                            <span class="text-lg font-bold text-gray-900">
                                                {{ $fixture->home_goals }} - {{ $fixture->away_goals }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm {{ !$this->isHomeTeam($fixture) ? 'font-bold text-blue-600' : 'text-gray-900' }}">
                                        {{ $fixture->awayTeam->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($fixture->status === 0)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                {{ __('Pendiente') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ __('Finalizado') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <button class="text-blue-600 hover:text-blue-900 font-medium">
                                            {{ __('Ver') }} →
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $allFixtures->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-600 text-lg">{{ __('No hay partidos con los filtros seleccionados') }}</p>
                    @if($selectedGameweekId || $statusFilter !== 'all')
                        <button wire:click="clearFilters" 
                                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            {{ __('Limpiar Filtros') }}
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- Modal de Detalle del Fixture --}}
    @if($selectedFixtureId)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50" 
             wire:click="$set('selectedFixtureId', null)">
            <div class="relative top-20 mx-auto p-5 w-full max-w-6xl" 
                 wire:click.stop>
                <div class="bg-white rounded-lg shadow-xl">
                    {{-- Header del Modal --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ __('Detalle del Partido') }}
                        </h3>
                        <button wire:click="$set('selectedFixtureId', null)" 
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Contenido del Modal --}}
                    <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                        @livewire('manager.fixtures.fixture-detail', ['fixtureId' => $selectedFixtureId], key('fixture-detail-'.$selectedFixtureId))
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
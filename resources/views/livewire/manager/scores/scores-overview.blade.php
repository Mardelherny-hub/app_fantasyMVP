<div>
    {{-- Header --}}
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ __('Mis Puntos') }}
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Rendimiento y estadísticas por jornada') }}
            </p>
        </div>
    </div>

    {{-- Resumen General --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    {{ __('Resumen de Temporada') }}
                </h2>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    {{-- Puntos Totales --}}
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600">{{ $totalPoints }}</div>
                        <div class="text-sm text-gray-600 mt-2">{{ __('Puntos Totales') }}</div>
                    </div>

                    {{-- Promedio --}}
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900">{{ $averagePoints }}</div>
                        <div class="text-sm text-gray-600 mt-2">{{ __('Promedio por GW') }}</div>
                    </div>

                    {{-- Mejor GW --}}
                    <div class="text-center">
                        <div class="text-4xl font-bold text-green-600">{{ $bestGameweekPoints ?? '-' }}</div>
                        <div class="text-sm text-gray-600 mt-2">
                            {{ __('Mejor GW') }}
                            @if($bestGameweekNumber)
                                <span class="font-semibold">(GW{{ $bestGameweekNumber }})</span>
                            @endif
                        </div>
                    </div>

                    {{-- Peor GW --}}
                    <div class="text-center">
                        <div class="text-4xl font-bold text-red-600">{{ $worstGameweekPoints ?? '-' }}</div>
                        <div class="text-sm text-gray-600 mt-2">
                            {{ __('Peor GW') }}
                            @if($worstGameweekNumber)
                                <span class="font-semibold">(GW{{ $worstGameweekNumber }})</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Gráfico de Evolución --}}
    @if(count($chartData['data']) > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ __('Evolución de Puntos por Jornada') }}
                </h3>
                
                <div class="h-64">
                    <canvas id="pointsChart"></canvas>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('pointsChart');
                        if (ctx) {
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: @json($chartData['labels']),
                                    datasets: [{
                                        label: '{{ __("Puntos") }}',
                                        data: @json($chartData['data']),
                                        borderColor: 'rgb(59, 130, 246)',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        tension: 0.4,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }
                    });
                </script>
            </div>
        </div>
    @endif

    {{-- Lista de Gameweeks --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">
                    {{ __('Puntos por Jornada') }}
                </h2>
            </div>

            <div class="divide-y divide-gray-200">
                @foreach($gameweekScores as $score)
                    <div class="p-6 hover:bg-gray-50 transition cursor-pointer"
                         wire:click="viewGameweekDetail({{ $score['gameweek']->id }})">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                {{-- GW Badge --}}
                                <div class="w-16 h-16 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <div class="text-center">
                                        <div class="text-xs font-semibold text-blue-600">GW</div>
                                        <div class="text-xl font-bold text-blue-700">{{ $score['gameweek']->number }}</div>
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div>
                                    <div class="font-semibold text-gray-900">
                                        Gameweek {{ $score['gameweek']->number }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        {{ $score['gameweek']->starts_at->format('d/m/Y') }} - 
                                        {{ $score['gameweek']->ends_at->format('d/m/Y') }}
                                    </div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $score['players_played'] }} {{ __('jugadores jugaron') }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-6">
                                {{-- Puntos --}}
                                <div class="text-right">
                                    <div class="text-3xl font-bold {{ $score['total_points'] > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ $score['total_points'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ __('puntos') }}</div>
                                </div>

                                {{-- Estado --}}
                                <div>
                                    @if($score['is_closed'])
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            {{ __('Completado') }}
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            {{ __('En Curso') }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Arrow --}}
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($gameweekScores->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-600 text-lg">{{ __('No hay jornadas disponibles aún') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Detalle del Gameweek --}}
    @if($showDetail && $selectedGameweekId)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto h-full w-full z-50" 
             wire:click="closeDetail">
            <div class="relative top-20 mx-auto p-5 w-full max-w-6xl" 
                 wire:click.stop>
                <div class="bg-white rounded-lg shadow-xl">
                    {{-- Header del Modal --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900">
                            {{ __('Detalle de Puntos - Gameweek') }} {{ $selectedGameweek->number }}
                        </h3>
                        <button wire:click="closeDetail" 
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Contenido del Modal --}}
                    <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                        @livewire('manager.scores.gameweek-detail', ['gameweekId' => $selectedGameweekId, 'teamId' => $team->id], key('gameweek-detail-'.$selectedGameweekId))
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Cargar Chart.js desde CDN --}}
    @once
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endonce
</div>
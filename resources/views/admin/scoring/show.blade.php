<x-admin-layout>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.scoring.index', ['locale' => app()->getLocale()]) }}" 
                       class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ __('Gameweek') }} {{ $gameweek->week_number }}
                        @if($gameweek->is_playoff)
                            <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                {{ __('Playoff') }}
                                @if($gameweek->playoff_round)
                                    - {{ __(App\Models\Fixture::PLAYOFF_ROUNDS[$gameweek->playoff_round] ?? 'Round') }}
                                @endif
                            </span>
                        @endif
                    </h1>
                </div>
                <p class="mt-1 text-sm text-gray-600">{{ $gameweek->season->name ?? 'N/A' }}</p>
            </div>

            {{-- Botones de acción --}}
            <div class="flex items-center space-x-3">
                @if($gameweek->is_closed)
                    {{-- Procesar --}}
                    @php
                        $totalFixtures = $gameweek->fixtures->count();
                        $finishedFixtures = $gameweek->fixtures->where('status', 1)->count();
                        $canProcess = $totalFixtures > 0 && $finishedFixtures < $totalFixtures;
                    @endphp
                    
                    @if($canProcess)
                        <form method="POST" action="{{ route('admin.scoring.process', ['locale' => app()->getLocale(), 'gameweek' => $gameweek->id]) }}">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                    onclick="return confirm('{{ __('¿Procesar el scoring de esta gameweek?') }}')">
                                {{ __('Procesar Scoring') }}
                            </button>
                        </form>
                    @endif

                    {{-- Recalcular --}}
                    @if($finishedFixtures > 0)
                        <form method="POST" action="{{ route('admin.scoring.recalculate', ['locale' => app()->getLocale(), 'gameweek' => $gameweek->id]) }}">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors"
                                    onclick="return confirm('{{ __('¿Recalcular los puntos? Esto eliminará los datos actuales.') }}')">
                                {{ __('Recalcular') }}
                            </button>
                        </form>
                    @endif
                @else
                    <span class="px-4 py-2 bg-gray-100 text-gray-500 rounded-lg">
                        {{ __('Gameweek abierta') }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Información General --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Detalles del Gameweek --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Detalles') }}</h3>
            <dl class="space-y-3">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">{{ __('Liga(s)') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">
                        @if($gameweek->fixtures->isNotEmpty())
                            {{ $gameweek->fixtures->first()->league->name ?? 'N/A' }}
                        @else
                            <span class="text-gray-400">{{ __('Sin fixtures') }}</span>
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">{{ __('Temporada') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $gameweek->season->name ?? 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">{{ __('Inicio') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $gameweek->starts_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">{{ __('Fin') }}</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $gameweek->ends_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-600">{{ __('Estado') }}</dt>
                    <dd>
                        @if($gameweek->is_closed)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ __('Cerrada') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ __('Abierta') }}
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Estadísticas --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Estadísticas') }}</h3>
            @php
                $totalFixtures = $gameweek->fixtures->count();
                $finishedFixtures = $gameweek->fixtures->where('status', 1)->count();
                $pendingFixtures = $totalFixtures - $finishedFixtures;
            @endphp
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-3xl font-bold text-gray-900">{{ $totalFixtures }}</p>
                    <p class="text-sm text-gray-600">{{ __('Fixtures Totales') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600">{{ $finishedFixtures }}</p>
                    <p class="text-sm text-gray-600">{{ __('Finalizados') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-yellow-600">{{ $pendingFixtures }}</p>
                    <p class="text-sm text-gray-600">{{ __('Pendientes') }}</p>
                </div>
                <div class="text-center">
                    @php
                        $progressPercent = $totalFixtures > 0 ? round(($finishedFixtures / $totalFixtures) * 100) : 0;
                    @endphp
                    <p class="text-3xl font-bold text-blue-600">{{ $progressPercent }}%</p>
                    <p class="text-sm text-gray-600">{{ __('Progreso') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Fixtures --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Partidos') }}</h3>
        </div>

        @if($gameweek->fixtures->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">{{ __('No hay fixtures para esta gameweek') }}</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($gameweek->fixtures as $fixture)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            {{-- Equipo Local --}}
                            <div class="flex-1 text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $fixture->homeTeam->name }}</p>
                                @if($fixture->isFinished())
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ __('Puntos') }}: 
                                        <span class="font-semibold">
                                            {{ $fixture->homeTeam->fantasyPoints()->where('gameweek_id', $gameweek->id)->sum('points') ?? 0 }}
                                        </span>
                                    </p>
                                @endif
                            </div>

                            {{-- Resultado --}}
                            <div class="flex items-center justify-center mx-8 min-w-[120px]">
                                @if($fixture->isFinished())
                                    <div class="text-center">
                                        <div class="flex items-center justify-center space-x-3">
                                            <span class="text-2xl font-bold text-gray-900">{{ $fixture->home_goals }}</span>
                                            <span class="text-gray-400">-</span>
                                            <span class="text-2xl font-bold text-gray-900">{{ $fixture->away_goals }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-1">
                                            {{ __('Finalizado') }}
                                        </span>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <span class="text-xl text-gray-400">vs</span>
                                        <span class="block text-xs text-gray-500 mt-1">{{ __('Pendiente') }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Equipo Visitante --}}
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $fixture->awayTeam->name }}</p>
                                @if($fixture->isFinished())
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ __('Puntos') }}: 
                                        <span class="font-semibold">
                                            {{ $fixture->awayTeam->fantasyPoints()->where('gameweek_id', $gameweek->id)->sum('points') ?? 0 }}
                                        </span>
                                    </p>
                                @endif
                            </div>

                            {{-- Indicador de Ganador --}}
                            @if($fixture->isFinished())
                                <div class="ml-4">
                                    @if($fixture->winner())
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($fixture->isDraw())
                                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-admin-layout>
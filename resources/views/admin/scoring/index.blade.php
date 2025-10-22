<x-admin-layout>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Scoring & Puntuación') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('Gestiona el cálculo de puntos por gameweek') }}</p>
    </div>

    {{-- Filtros --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('admin.scoring.index', ['locale' => app()->getLocale()]) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Filtro por Liga --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Liga') }}</label>
                <select name="league_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('Todas las ligas') }}</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}" {{ request('league_id') == $league->id ? 'selected' : '' }}>
                            {{ $league->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Temporada --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Temporada') }}</label>
                <select name="season_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('Todas las temporadas') }}</option>
                    @foreach($seasons as $season)
                        <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>
                            {{ $season->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtro por Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('Todos') }}</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>{{ __('Abierta') }}</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>{{ __('Cerrada') }}</option>
                </select>
            </div>

            {{-- Botones --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    {{ __('Filtrar') }}
                </button>
                @if(request()->hasAny(['league_id', 'season_id', 'status']))
                    <a href="{{ route('admin.scoring.index', ['locale' => app()->getLocale()]) }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                        {{ __('Limpiar') }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Estadísticas Rápidas --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">{{ __('Gameweeks Totales') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_gameweeks'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">{{ __('Calculadas') }}</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['calculated'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">{{ __('Pendientes') }}</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-sm text-gray-600">{{ __('Fixtures Finalizados') }}</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['fixtures_finished'] }}</p>
        </div>
    </div>

    {{-- Tabla de Gameweeks --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('GW') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Liga') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Temporada') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Fechas') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Fixtures') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Estado') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Acciones') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($gameweeks as $gameweek)
                        @php
                            $totalFixtures = $gameweek->fixtures->count();
                            $finishedFixtures = $gameweek->fixtures->where('status', 1)->count();
                            $isCalculated = $totalFixtures > 0 && $finishedFixtures === $totalFixtures;
                            $canProcess = $gameweek->is_closed && $totalFixtures > 0;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            {{-- Número GW --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-bold text-gray-900">GW{{ $gameweek->week_number }}</div>
                                    @if($gameweek->is_playoff)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ __('Playoff') }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Liga(s) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($gameweek->fixtures->isNotEmpty())
                                        {{ $gameweek->fixtures->first()->league->name ?? 'N/A' }}
                                    @else
                                        <span class="text-gray-400">{{ __('Sin fixtures') }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Temporada --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">{{ $gameweek->season->name ?? 'N/A' }}</div>
                            </td>

                            {{-- Fechas --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-600">
                                    {{ $gameweek->starts_at->format('d/m/Y') }}<br>
                                    <span class="text-xs text-gray-500">{{ $gameweek->ends_at->format('d/m/Y') }}</span>
                                </div>
                            </td>

                            {{-- Fixtures --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $finishedFixtures }} / {{ $totalFixtures }}
                                    <span class="text-xs text-gray-500">{{ __('finalizados') }}</span>
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($gameweek->is_closed)
                                    @if($isCalculated)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('Calculada') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ __('Cerrada') }}
                                        </span>
                                    @endif
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ __('Abierta') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- Ver Detalle - Siempre habilitado --}}
                                    <a href="{{ route('admin.scoring.show', ['locale' => app()->getLocale(), 'gameweek' => $gameweek->id]) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="{{ __('Ver detalle') }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Procesar - Visible siempre, habilitado solo si cerrada y no calculada --}}
                                    @if($canProcess && !$isCalculated)
                                        <form method="POST" action="{{ route('admin.scoring.process', ['locale' => app()->getLocale(), 'gameweek' => $gameweek->id]) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900" 
                                                    title="{{ __('Procesar scoring') }}"
                                                    onclick="return confirm('{{ __('¿Estás seguro de procesar el scoring de esta gameweek?') }}')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" 
                                                class="text-gray-300 cursor-not-allowed" 
                                                title="{{ !$gameweek->is_closed ? __('Debe estar cerrada') : ($totalFixtures === 0 ? __('Sin fixtures') : __('Ya procesada')) }}"
                                                disabled>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Recalcular - Visible siempre, habilitado solo si ya calculada --}}
                                    @if($isCalculated)
                                        <form method="POST" action="{{ route('admin.scoring.recalculate', ['locale' => app()->getLocale(), 'gameweek' => $gameweek->id]) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-orange-600 hover:text-orange-900" 
                                                    title="{{ __('Recalcular puntos') }}"
                                                    onclick="return confirm('{{ __('¿Estás seguro de recalcular los puntos? Esto eliminará los datos actuales.') }}')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" 
                                                class="text-gray-300 cursor-not-allowed" 
                                                title="{{ __('No hay datos para recalcular') }}"
                                                disabled>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                {{ __('No hay gameweeks disponibles') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $gameweeks->appends(request()->query())->links() }}
        </div>
    </div>
</x-admin-layout>
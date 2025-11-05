<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('admin.player-match-stats.index', app()->getLocale()) }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                ← {{ __('Volver a partidos') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Puntuación del Partido') }}</h1>
            <div class="mt-2 text-sm text-gray-600">
                <span class="font-medium">{{ $realMatch->fixture->homeTeam->name ?? 'Local' }}</span>
                <span class="mx-2">{{ $realMatch->home_score ?? 0 }} - {{ $realMatch->away_score ?? 0 }}</span>
                <span class="font-medium">{{ $realMatch->fixture->awayTeam->name ?? 'Visitante' }}</span>
                <span class="ml-4 text-gray-400">{{ $realMatch->fixture->round ?? 'N/A' }}</span>
            </div>
        </div>

        {{-- Tabla de Puntuación --}}
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Jugador') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Pos') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Min') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Goles') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Asist') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('TA') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('TR') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('CS') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Puntos') }}</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ __('Desglose') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($playersWithPoints as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item['player']->known_as ?? $item['player']->full_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                {{ $item['player']->getPositionName() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item['stats']->minutes }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item['stats']->goals }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item['stats']->assists }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item['stats']->yellow }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                {{ $item['stats']->red }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item['stats']->clean_sheet)
                                    <span class="text-green-600">✓</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-lg font-bold {{ $item['points']['total'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $item['points']['total'] > 0 ? '+' : '' }}{{ $item['points']['total'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!empty($item['points']['breakdown']))
                                    <button 
                                        onclick="toggleBreakdown('breakdown-{{ $item['player']->id }}')"
                                        class="text-xs text-blue-600 hover:text-blue-800">
                                        {{ __('Ver detalle') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @if(!empty($item['points']['breakdown']))
                            <tr id="breakdown-{{ $item['player']->id }}" class="hidden bg-gray-50">
                                <td colspan="10" class="px-6 py-4">
                                    <div class="text-xs space-y-1">
                                        <p class="font-semibold text-gray-700 mb-2">{{ __('Desglose de puntos:') }}</p>
                                        @foreach($item['points']['breakdown'] as $detail)
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">{{ $detail['label'] }}</span>
                                                <span class="font-medium {{ $detail['points'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $detail['points'] > 0 ? '+' : '' }}{{ $detail['points'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                {{ __('No hay estadísticas cargadas para este partido.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Resumen --}}
        @if($playersWithPoints->isNotEmpty())
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">{{ __('Total Jugadores') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $playersWithPoints->count() }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">{{ __('Puntos Totales') }}</p>
                    <p class="text-2xl font-bold text-green-600">{{ $playersWithPoints->sum('points.total') }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <p class="text-sm text-gray-600">{{ __('Promedio por Jugador') }}</p>
                    <p class="text-2xl font-bold text-blue-600">{{ round($playersWithPoints->avg('points.total'), 1) }}</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        function toggleBreakdown(id) {
            const element = document.getElementById(id);
            element.classList.toggle('hidden');
        }
    </script>
</x-admin-layout>
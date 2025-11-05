<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Estadísticas de Jugadores') }}</h1>
        </div>

        {{-- Lista de Partidos --}}
        <div class="space-y-4">
            @forelse($matches as $match)
                <div class="bg-white rounded-xl shadow p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-2">
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                    {{ $match->fixture->round ?? 'N/A' }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $match->started_at_utc?->format('d/m/Y H:i') ?? 'Fecha pendiente' }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded {{ $match->status === 'finished' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($match->status) }}
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-4">
                                <div class="text-sm">
                                    <span class="font-medium">{{ $match->fixture->homeTeam->name ?? 'Local' }}</span>
                                    @if($match->status === 'finished')
                                        <span class="font-bold text-lg mx-2">{{ $match->home_score }}</span>
                                    @endif
                                </div>
                                <span class="text-gray-400">vs</span>
                                <div class="text-sm">
                                    @if($match->status === 'finished')
                                        <span class="font-bold text-lg mr-2">{{ $match->away_score }}</span>
                                    @endif
                                    <span class="font-medium">{{ $match->fixture->awayTeam->name ?? 'Visitante' }}</span>
                                </div>
                            </div>

                            <div class="mt-2 text-xs text-gray-500">
                                {{ __('Estadísticas cargadas') }}: {{ $match->player_stats_count }}
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.player-match-stats.manage', [app()->getLocale(), $match]) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                {{ __('Gestionar Stats') }}
                            </a>

                            <a href="{{ route('admin.player-match-stats.scoring', [app()->getLocale(), $match]) }}" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                {{ __('Ver Puntuación') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl shadow p-8 text-center">
                    <p class="text-gray-500">{{ __('No hay partidos disponibles.') }}</p>
                </div>
            @endforelse
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $matches->links() }}
        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <div class="container mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="mb-6">
            <a href="{{ route('admin.player-match-stats.index', app()->getLocale()) }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                ← {{ __('Volver a partidos') }}
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Gestionar Estadísticas') }}</h1>
            <div class="mt-2 text-sm text-gray-600">
                <span class="font-medium">{{ $realMatch->fixture->homeTeam->name ?? 'Local' }}</span>
                <span class="mx-2">{{ $realMatch->home_score ?? 0 }} - {{ $realMatch->away_score ?? 0 }}</span>
                <span class="font-medium">{{ $realMatch->fixture->awayTeam->name ?? 'Visitante' }}</span>
                <span class="ml-4 text-gray-400">{{ $realMatch->fixture->round ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Equipo Local --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-blue-600 rounded-full"></span>
                    {{ $realMatch->fixture->homeTeam->name ?? 'Local' }}
                </h3>
                
                @foreach($homeTeamPlayers as $player)
                    @php
                        $stat = $existingStats->get($player->id);
                    @endphp
                    <div class="mb-4 p-4 border rounded-lg {{ $stat ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="font-medium text-sm">{{ $player->known_as ?? $player->full_name }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $player->getPositionName() }})</span>
                            </div>
                            @if($stat)
                                <form method="POST" action="{{ route('admin.player-match-stats.destroy', [app()->getLocale(), $stat]) }}" class="inline" onsubmit="return confirm('¿Eliminar estadísticas?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button>
                                </form>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.player-match-stats.store', app()->getLocale()) }}">
                            @csrf
                            <input type="hidden" name="real_match_id" value="{{ $realMatch->id }}">
                            <input type="hidden" name="player_id" value="{{ $player->id }}">

                            <div class="grid grid-cols-4 gap-2 mb-2">
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Min') }}</label>
                                    <input type="number" name="minutes" min="0" max="120" value="{{ $stat->minutes ?? 0 }}" class="w-full text-sm border-gray-300 rounded" required>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Goles') }}</label>
                                    <input type="number" name="goals" min="0" value="{{ $stat->goals ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Asist') }}</label>
                                    <input type="number" name="assists" min="0" value="{{ $stat->assists ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Tiros') }}</label>
                                    <input type="number" name="shots" min="0" value="{{ $stat->shots ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-2 mb-2">
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Ataj') }}</label>
                                    <input type="number" name="saves" min="0" value="{{ $stat->saves ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('TA') }}</label>
                                    <input type="number" name="yellow" min="0" max="2" value="{{ $stat->yellow ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('TR') }}</label>
                                    <input type="number" name="red" min="0" max="1" value="{{ $stat->red ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Recib') }}</label>
                                    <input type="number" name="conceded" min="0" value="{{ $stat->conceded ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Rating') }}</label>
                                    <input type="number" name="rating" min="0" max="10" step="0.1" value="{{ $stat->rating ?? '' }}" placeholder="0.0-10.0" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div class="flex items-end">
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" name="clean_sheet" value="1" {{ $stat && $stat->clean_sheet ? 'checked' : '' }} class="mr-1">
                                        {{ __('Portería a cero') }}
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="mt-3 w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                {{ $stat ? __('Actualizar') : __('Guardar') }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- Equipo Visitante (igual estructura) --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                    <span class="w-3 h-3 bg-red-600 rounded-full"></span>
                    {{ $realMatch->fixture->awayTeam->name ?? 'Visitante' }}
                </h3>
                
                @foreach($awayTeamPlayers as $player)
                    @php
                        $stat = $existingStats->get($player->id);
                    @endphp
                    <div class="mb-4 p-4 border rounded-lg {{ $stat ? 'bg-green-50 border-green-200' : 'bg-gray-50' }}">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <span class="font-medium text-sm">{{ $player->known_as ?? $player->full_name }}</span>
                                <span class="text-xs text-gray-500 ml-2">({{ $player->getPositionName() }})</span>
                            </div>
                            @if($stat)
                                <form method="POST" action="{{ route('admin.player-match-stats.destroy', [app()->getLocale(), $stat]) }}" class="inline" onsubmit="return confirm('¿Eliminar estadísticas?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:text-red-800">{{ __('Eliminar') }}</button>
                                </form>
                            @endif
                        </div>

                        <form method="POST" action="{{ route('admin.player-match-stats.store', app()->getLocale()) }}">
                            @csrf
                            <input type="hidden" name="real_match_id" value="{{ $realMatch->id }}">
                            <input type="hidden" name="player_id" value="{{ $player->id }}">

                            <div class="grid grid-cols-4 gap-2 mb-2">
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Min') }}</label>
                                    <input type="number" name="minutes" min="0" max="120" value="{{ $stat->minutes ?? 0 }}" class="w-full text-sm border-gray-300 rounded" required>
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Goles') }}</label>
                                    <input type="number" name="goals" min="0" value="{{ $stat->goals ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Asist') }}</label>
                                    <input type="number" name="assists" min="0" value="{{ $stat->assists ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Tiros') }}</label>
                                    <input type="number" name="shots" min="0" value="{{ $stat->shots ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                            </div>

                            <div class="grid grid-cols-4 gap-2 mb-2">
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Ataj') }}</label>
                                    <input type="number" name="saves" min="0" value="{{ $stat->saves ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('TA') }}</label>
                                    <input type="number" name="yellow" min="0" max="2" value="{{ $stat->yellow ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('TR') }}</label>
                                    <input type="number" name="red" min="0" max="1" value="{{ $stat->red ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Recib') }}</label>
                                    <input type="number" name="conceded" min="0" value="{{ $stat->conceded ?? 0 }}" class="w-full text-sm border-gray-300 rounded">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('Rating') }}</label>
                                    <input type="number" name="rating" min="0" max="10" step="0.1" value="{{ $stat->rating ?? '' }}" placeholder="0.0-10.0" class="w-full text-sm border-gray-300 rounded">
                                </div>
                                <div class="flex items-end">
                                    <label class="flex items-center text-xs">
                                        <input type="checkbox" name="clean_sheet" value="1" {{ $stat && $stat->clean_sheet ? 'checked' : '' }} class="mr-1">
                                        {{ __('Portería a cero') }}
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="mt-3 w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition">
                                {{ $stat ? __('Actualizar') : __('Guardar') }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-admin-layout>
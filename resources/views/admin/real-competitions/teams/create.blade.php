<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.real-competitions.show', ['locale' => app()->getLocale(), 'realCompetition' => $realCompetition]) }}" 
                       class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Agregar Equipos') }}</h1>
                        <p class="text-sm text-gray-600">{{ $realCompetition->name }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.real-competitions.teams.store', ['locale' => app()->getLocale(), 'realCompetition' => $realCompetition]) }}" 
                      method="POST"
                      x-data="{ selectedTeams: [] }">
                    @csrf

                    {{-- Season Selector --}}
                    <div class="mb-6">
                        <label for="season_id" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('Temporada') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="season_id" 
                                id="season_id" 
                                required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('season_id') border-red-300 @enderror"
                                onchange="this.form.submit()">
                            <option value="">{{ __('Seleccionar temporada') }}</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" {{ $selectedSeason && $selectedSeason->id == $season->id ? 'selected' : '' }}>
                                    {{ $season->name }} ({{ $season->starts_at->format('Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('season_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($selectedSeason)
                        {{-- Teams Selection --}}
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Equipos Disponibles') }} <span class="text-red-500">*</span>
                                </label>
                                <span class="text-sm text-gray-500" x-text="selectedTeams.length + ' {{ __('seleccionados') }}'"></span>
                            </div>

                            @if($availableTeams->count() > 0)
                                <div class="border border-gray-200 rounded-lg divide-y divide-gray-200 max-h-96 overflow-y-auto">
                                    @foreach($availableTeams as $team)
                                        <label class="flex items-center p-4 hover:bg-gray-50 cursor-pointer transition-colors">
                                            <input type="checkbox" 
                                                   name="team_ids[]" 
                                                   value="{{ $team->id }}"
                                                   x-model="selectedTeams"
                                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <div class="ml-3 flex items-center gap-3 flex-1">
                                                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $team->name }}</p>
                                                    <p class="text-sm text-gray-500">{{ $team->country ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                @error('team_ids')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            @else
                                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <p>{{ __('Todos los equipos ya están asignados a esta temporada') }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        @if($availableTeams->count() > 0)
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                <a href="{{ route('admin.real-competitions.show', ['locale' => app()->getLocale(), 'realCompetition' => $realCompetition]) }}" 
                                   class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                                    {{ __('Cancelar') }}
                                </a>
                                <button type="submit" 
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        x-bind:disabled="selectedTeams.length === 0">
                                    {{ __('Agregar Equipos') }}
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg">
                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p>{{ __('Por favor, seleccione una temporada primero') }}</p>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Edit Fixture') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-fixtures.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Fixtures') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Edit') }}</span>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                
                <form action="{{ route('admin.real-fixtures.update', [app()->getLocale(), $realFixture]) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- External ID --}}
                    <div>
                        <label for="external_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('External ID') }}
                            <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                        </label>
                        <input type="number" 
                               name="external_id" 
                               id="external_id" 
                               value="{{ old('external_id', $realFixture->external_id) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('external_id') border-red-300 @enderror">
                        @error('external_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Competición --}}
                    <div>
                        <label for="real_competition_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Competition') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="real_competition_id" 
                                id="real_competition_id" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('real_competition_id') border-red-300 @enderror">
                            <option value="">{{ __('Select competition') }}</option>
                            @foreach($competitions as $comp)
                                <option value="{{ $comp->id }}" {{ old('real_competition_id', $realFixture->real_competition_id) == $comp->id ? 'selected' : '' }}>
                                    {{ $comp->name }} ({{ $comp->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('real_competition_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Temporada --}}
                    <div>
                        <label for="season_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Season') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="season_id" 
                                id="season_id" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('season_id') border-red-300 @enderror">
                            <option value="">{{ __('Select season') }}</option>
                            @foreach($seasons as $season)
                                <option value="{{ $season->id }}" {{ old('season_id', $realFixture->season_id) == $season->id ? 'selected' : '' }}>
                                    {{ $season->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('season_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Equipos --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Equipo Local --}}
                        <div>
                            <label for="home_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Home Team') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="home_team_id" 
                                    id="home_team_id" 
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('home_team_id') border-red-300 @enderror">
                                <option value="">{{ __('Select team') }}</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('home_team_id', $realFixture->home_team_id) == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('home_team_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Equipo Visitante --}}
                        <div>
                            <label for="away_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Away Team') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="away_team_id" 
                                    id="away_team_id" 
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('away_team_id') border-red-300 @enderror">
                                <option value="">{{ __('Select team') }}</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('away_team_id', $realFixture->away_team_id) == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('away_team_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Fecha y Hora --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Fecha --}}
                        <div>
                            <label for="match_date_utc" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Match Date') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="match_date_utc" 
                                   id="match_date_utc" 
                                   value="{{ old('match_date_utc', $realFixture->match_date_utc->format('Y-m-d')) }}"
                                   required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('match_date_utc') border-red-300 @enderror">
                            @error('match_date_utc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Hora --}}
                        <div>
                            <label for="match_time_utc" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Match Time') }} (UTC)
                                <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                            </label>
                            <input type="time" 
                                   name="match_time_utc" 
                                   id="match_time_utc" 
                                   value="{{ old('match_time_utc', $realFixture->match_time_utc ? $realFixture->match_time_utc->format('H:i') : '') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('match_time_utc') border-red-300 @enderror">
                            @error('match_time_utc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Ronda y Estado --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Ronda --}}
                        <div>
                            <label for="round" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Round') }}
                                <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                            </label>
                            <input type="text" 
                                   name="round" 
                                   id="round" 
                                   value="{{ old('round', $realFixture->round) }}"
                                   placeholder="1, 2, Quarter Final, etc."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('round') border-red-300 @enderror">
                            @error('round')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Status') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    id="status" 
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-300 @enderror">
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status', $realFixture->status) == $status ? 'selected' : '' }}>
                                        {{ __(ucfirst($status)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Venue --}}
                    <div>
                        <label for="venue" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Venue') }}
                            <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                        </label>
                        <input type="text" 
                               name="venue" 
                               id="venue" 
                               value="{{ old('venue', $realFixture->venue) }}"
                               placeholder="{{ __('Stadium name') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('venue') border-red-300 @enderror">
                        @error('venue')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.real-fixtures.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Update Fixture') }}
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-admin-layout>
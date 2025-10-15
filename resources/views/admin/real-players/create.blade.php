<x-admin-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Create Player') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Real Players') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Create') }}</span>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                
                <form action="{{ route('admin.real-players.store', app()->getLocale()) }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- External ID --}}
                    <div>
                        <label for="external_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('External ID') }}
                            <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                        </label>
                        <input type="number" 
                               name="external_id" 
                               id="external_id" 
                               value="{{ old('external_id') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('external_id') border-red-300 @enderror">
                        @error('external_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre completo --}}
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Full Name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="full_name" 
                               id="full_name" 
                               value="{{ old('full_name') }}"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('full_name') border-red-300 @enderror">
                        @error('full_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Posición y Nacionalidad --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Posición --}}
                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Position') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="position" 
                                    id="position" 
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('position') border-red-300 @enderror">
                                <option value="">{{ __('Select position') }}</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}" {{ old('position') == $pos ? 'selected' : '' }}>
                                        {{ $pos }}
                                    </option>
                                @endforeach
                            </select>
                            @error('position')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nacionalidad --}}
                        <div>
                            <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Nationality') }} (ISO-2)
                                <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                            </label>
                            <input type="text" 
                                   name="nationality" 
                                   id="nationality" 
                                   value="{{ old('nationality') }}"
                                   maxlength="2"
                                   placeholder="CA, US, MX..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('nationality') border-red-300 @enderror">
                            @error('nationality')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Fecha de nacimiento --}}
                    <div>
                        <label for="birthdate" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Birthdate') }}
                            <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                        </label>
                        <input type="date" 
                               name="birthdate" 
                               id="birthdate" 
                               value="{{ old('birthdate') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('birthdate') border-red-300 @enderror">
                        @error('birthdate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Photo URL --}}
                    <div>
                        <label for="photo_url" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Photo URL') }}
                            <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                        </label>
                        <input type="url" 
                               name="photo_url" 
                               id="photo_url" 
                               value="{{ old('photo_url') }}"
                               placeholder="https://..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('photo_url') border-red-300 @enderror">
                        @error('photo_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Asignación inicial a equipo --}}
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Initial Team Assignment') }}</h3>
                        <p class="text-sm text-gray-500 mb-4">{{ __('Optional: Assign player to a team immediately') }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            
                            {{-- Equipo --}}
                            <div>
                                <label for="real_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Team') }}
                                </label>
                                <select name="real_team_id" 
                                        id="real_team_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('real_team_id') border-red-300 @enderror">
                                    <option value="">{{ __('No team') }}</option>
                                    @foreach($teams as $team)
                                        <option value="{{ $team->id }}" {{ old('real_team_id') == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('real_team_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Temporada --}}
                            <div>
                                <label for="season_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Season') }}
                                </label>
                                <select name="season_id" 
                                        id="season_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('season_id') border-red-300 @enderror">
                                    <option value="">{{ __('Select season') }}</option>
                                    @foreach($seasons as $season)
                                        <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                            {{ $season->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('season_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Número de camiseta --}}
                            <div>
                                <label for="shirt_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Shirt Number') }}
                                </label>
                                <input type="number" 
                                       name="shirt_number" 
                                       id="shirt_number" 
                                       value="{{ old('shirt_number') }}"
                                       min="1"
                                       max="99"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('shirt_number') border-red-300 @enderror">
                                @error('shirt_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Create Player') }}
                        </button>
                    </div>

                </form>
                

            </div>

        </div>
    </div>
</x-admin-layout>
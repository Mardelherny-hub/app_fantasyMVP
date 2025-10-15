<x-admin-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Edit Player') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Real Players') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Edit') }}</span>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                
                <form action="{{ route('admin.real-players.update', [app()->getLocale(), $realPlayer]) }}" method="POST" class="space-y-6">
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
                               value="{{ old('external_id', $realPlayer->external_id) }}"
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
                               value="{{ old('full_name', $realPlayer->full_name) }}"
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
                                    <option value="{{ $pos }}" {{ old('position', $realPlayer->position) == $pos ? 'selected' : '' }}>
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
                                   value="{{ old('nationality', $realPlayer->nationality) }}"
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
                               value="{{ old('birthdate', $realPlayer->birthdate ? $realPlayer->birthdate->format('Y-m-d') : '') }}"
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
                               value="{{ old('photo_url', $realPlayer->photo_url) }}"
                               placeholder="https://..."
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('photo_url') border-red-300 @enderror">
                        @error('photo_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Info de equipo actual --}}
                    @if($currentMembership)
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Current Team') }}</h3>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-900">{{ $currentMembership->team->name }}</p>
                                        <p class="text-xs text-blue-700 mt-1">
                                            {{ __('Season') }}: {{ $currentMembership->season->name }}
                                            @if($currentMembership->shirt_number)
                                                | {{ __('Shirt') }}: #{{ $currentMembership->shirt_number }}
                                            @endif
                                        </p>
                                    </div>
                                    <a href="{{ route('admin.real-teams.show', [app()->getLocale(), $currentMembership->team]) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        {{ __('View Team') }} →
                                    </a>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                {{ __('To change team assignment, go to the team management page') }}
                            </p>
                        </div>
                    @else
                        <div class="border-t pt-6">
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                <p class="text-sm text-gray-600">{{ __('This player is currently not assigned to any team') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Botones --}}
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Update Player') }}
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-admin-layout>
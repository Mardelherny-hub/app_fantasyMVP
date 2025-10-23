<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Team') }}: {{ $team->name }}
            </h2>
            <a href="{{ route('admin.fantasy.teams.show', [app()->getLocale(), $team->id]) }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-md">
                {{ __('Cancel') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Mensajes de éxito/error --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.fantasy.teams.update', [app()->getLocale(), $team->id]) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nombre del Equipo --}}
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Team Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $team->name) }}"
                                   required
                                   maxlength="100"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">
                                {{ __('The slug will be automatically generated from the name.') }}
                            </p>
                        </div>

                        {{-- Manager (User) --}}
                        <div class="mb-6">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Manager') }}
                            </label>
                            <select name="user_id" 
                                    id="user_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('user_id') border-red-500 @enderror">
                                <option value="">{{ __('No owner (Bot)') }}</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('user_id', $team->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Liga --}}
                        <div class="mb-6">
                            <label for="league_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('League') }}
                            </label>
                            <select name="league_id" 
                                    id="league_id"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('league_id') border-red-500 @enderror">
                                <option value="">{{ __('No league assigned') }}</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}" 
                                            {{ old('league_id', $team->league_id) == $league->id ? 'selected' : '' }}>
                                        {{ $league->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('league_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Budget --}}
                        <div class="mb-6">
                            <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Budget') }}
                            </label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" 
                                       name="budget" 
                                       id="budget" 
                                       value="{{ old('budget', $team->budget) }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('budget') border-red-500 @enderror">
                            </div>
                            @error('budget')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Emblem URL --}}
                        <div class="mb-6">
                            <label for="emblem_url" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Emblem URL') }}
                            </label>
                            <input type="url" 
                                   name="emblem_url" 
                                   id="emblem_url" 
                                   value="{{ old('emblem_url', $team->emblem_url) }}"
                                   maxlength="500"
                                   placeholder="https://..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('emblem_url') border-red-500 @enderror">
                            @error('emblem_url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @if($team->emblem_url)
                                <div class="mt-2">
                                    <img src="{{ $team->emblem_url }}" 
                                         alt="Current emblem" 
                                         class="h-16 w-16 rounded-full border border-gray-300">
                                </div>
                            @endif
                        </div>

                        {{-- Checkboxes --}}
                        <div class="mb-6 space-y-4">
                            
                            {{-- Is Bot --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="is_bot" value="0">
                                    <input type="checkbox" 
                                           name="is_bot" 
                                           id="is_bot" 
                                           value="1"
                                           {{ old('is_bot', $team->is_bot) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_bot" class="font-medium text-gray-700">
                                        {{ __('Bot Team') }}
                                    </label>
                                    <p class="text-gray-500">
                                        {{ __('Mark this team as controlled by AI/Bot') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Squad Complete --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="hidden" name="is_squad_complete" value="0">
                                    <input type="checkbox" 
                                           name="is_squad_complete" 
                                           id="is_squad_complete" 
                                           value="1"
                                           {{ old('is_squad_complete', $team->is_squad_complete) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_squad_complete" class="font-medium text-gray-700">
                                        {{ __('Squad Complete') }}
                                    </label>
                                    <p class="text-gray-500">
                                        {{ __('Mark if the team has completed its initial squad selection') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Información adicional (solo lectura) --}}
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('Additional Information') }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">{{ __('Slug') }}:</span>
                                    <span class="font-medium ml-2">{{ $team->slug }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('Total Points') }}:</span>
                                    <span class="font-medium ml-2">{{ number_format($team->total_points) }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('Created') }}:</span>
                                    <span class="font-medium ml-2">{{ $team->created_at->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('Last Update') }}:</span>
                                    <span class="font-medium ml-2">{{ $team->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.fantasy.teams.show', [app()->getLocale(), $team->id]) }}" 
                               class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-md">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" 
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-md">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
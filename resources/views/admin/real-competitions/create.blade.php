<x-admin-layout>
    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center gap-4 mb-2">
                    <a href="{{ route('admin.real-competitions.index', ['locale' => app()->getLocale()]) }}" 
                       class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Crear Competición') }}</h1>
                </div>
                <p class="text-sm text-gray-600">{{ __('Registra una nueva competición de ligas canadienses') }}</p>
            </div>

            {{-- Alerts --}}
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">{{ __('Hay errores en el formulario') }}</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Form --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <form action="{{ route('admin.real-competitions.store', ['locale' => app()->getLocale()]) }}" 
                      method="POST" 
                      class="p-6 space-y-6">
                    @csrf

                    {{-- External ID --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <label for="external_id" class="block text-sm font-medium text-gray-700">
                                {{ __('ID Externo') }}
                            </label>
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open" @click.outside="open = false"
                                        class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full text-xs font-bold hover:bg-blue-200 transition-colors flex items-center justify-center">?</button>
                                <div x-show="open" x-transition
                                    class="absolute z-10 left-6 top-0 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg">
                                    {{ __('Opcional. Si usás una API externa como LiveScore o API-Football, colocá aquí el ID de la competición (ej: 257 para CPL). Si cargás datos manualmente, dejalo vacío.') }}
                                </div>
                            </div>
                        </div>
                        <input type="number" 
                            name="external_id" 
                            id="external_id" 
                            value="{{ old('external_id') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('external_id') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">{{ __('Dejá vacío si no usás API externa') }}</p>
                    </div>

                    {{-- Name --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('Nombre') }} <span class="text-red-500">*</span>
                            </label>
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open" @click.outside="open = false"
                                        class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full text-xs font-bold hover:bg-blue-200 transition-colors flex items-center justify-center">?</button>
                                <div x-show="open" x-transition
                                    class="absolute z-10 left-6 top-0 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg">
                                    {{ __('Nombre oficial de la competición. Ej: Canadian Premier League, MLS, Liga MX.') }}
                                </div>
                            </div>
                        </div>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
                               required
                               maxlength="255"
                               placeholder="Canadian Premier League"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('País') }}
                        </label>
                        <input type="text" 
                               name="country" 
                               id="country" 
                               value="{{ old('country', 'Canada') }}"
                               maxlength="100"
                               placeholder="Canada"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('country') border-red-500 @enderror">
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Type --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <label for="type" class="block text-sm font-medium text-gray-700">
                                {{ __('Tipo') }} <span class="text-red-500">*</span>
                            </label>
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open" @click.outside="open = false"
                                        class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full text-xs font-bold hover:bg-blue-200 transition-colors flex items-center justify-center">?</button>
                                <div x-show="open" x-transition
                                    class="absolute z-10 left-6 top-0 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg">
                                    {{ __('Liga: competición de temporada regular con tabla de posiciones. Copa: torneo por eliminación directa.') }}
                                </div>
                            </div>
                        </div>
                        <select name="type" 
                                id="type" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('type') border-red-500 @enderror">
                            <option value="">{{ __('Selecciona un tipo') }}</option>
                            <option value="league" {{ old('type') === 'league' ? 'selected' : '' }}>{{ __('Liga') }}</option>
                            <option value="cup" {{ old('type') === 'cup' ? 'selected' : '' }}>{{ __('Copa') }}</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- External Source --}}
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <label for="external_source" class="block text-sm font-medium text-gray-700">
                                {{ __('Fuente Externa') }}
                            </label>
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" @click="open = !open" @click.outside="open = false"
                                        class="w-5 h-5 bg-blue-100 text-blue-600 rounded-full text-xs font-bold hover:bg-blue-200 transition-colors flex items-center justify-center">?</button>
                                <div x-show="open" x-transition
                                    class="absolute z-10 left-6 top-0 w-64 p-3 bg-gray-800 text-white text-xs rounded-lg shadow-lg">
                                    {{ __('Indica de dónde provienen los datos: livescore, api-football, o dejá "manual" si cargás todo a mano.') }}
                                </div>
                            </div>
                        </div>
                        <input type="text" 
                            name="external_source" 
                            id="external_source" 
                            value="{{ old('external_source', 'manual') }}"
                            maxlength="50"
                            placeholder="manual"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('external_source') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">{{ __('Dejá "manual" si no usás API externa') }}</p>
                        @error('external_source')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Active --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="hidden" name="active" value="0">
                            <input type="checkbox" 
                                   name="active" 
                                   id="active" 
                                   value="1"
                                   {{ old('active', true) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        </div>
                        <div class="ml-3">
                            <label for="active" class="text-sm font-medium text-gray-700">
                                {{ __('Competición activa') }}
                            </label>
                            <p class="text-xs text-gray-500">{{ __('Las competiciones inactivas no se mostrarán en el sistema') }}</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.real-competitions.index', ['locale' => app()->getLocale()]) }}" 
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                            {{ __('Crear Competición') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
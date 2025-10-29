<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Question') }}
            </h2>
            <a href="{{ route('admin.quiz.questions.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Errores de validación --}}
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">{{ __('Whoops!') }}</strong>
                    <span class="block sm:inline">{{ __('There were some problems with your input.') }}</span>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.quiz.questions.store') }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Question Details') }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Categoría --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Category') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="category_id" 
                                        id="category_id" 
                                        required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category_id') border-red-500 @enderror">
                                    <option value="">{{ __('Select a category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Dificultad --}}
                            <div>
                                <label for="difficulty" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Difficulty') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="difficulty" 
                                        id="difficulty" 
                                        required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('difficulty') border-red-500 @enderror">
                                    <option value="">{{ __('Select difficulty') }}</option>
                                    <option value="1" {{ old('difficulty') == 1 ? 'selected' : '' }}>{{ __('Easy') }}</option>
                                    <option value="2" {{ old('difficulty') == 2 ? 'selected' : '' }}>{{ __('Medium') }}</option>
                                    <option value="3" {{ old('difficulty') == 3 ? 'selected' : '' }}>{{ __('Hard') }}</option>
                                </select>
                                @error('difficulty')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Estado --}}
                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ __('Active') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Traducciones de la pregunta --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Question Text (Translations)') }}</h3>

                        {{-- Español --}}
                        <div class="mb-4">
                            <label for="translations_es" class="block text-sm font-medium text-gray-700 mb-1">
                                🇪🇸 {{ __('Spanish') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="translations[es]" 
                                      id="translations_es" 
                                      rows="3" 
                                      required
                                      placeholder="{{ __('Enter question in Spanish...') }}"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('translations.es') border-red-500 @enderror">{{ old('translations.es') }}</textarea>
                            @error('translations.es')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Inglés --}}
                        <div class="mb-4">
                            <label for="translations_en" class="block text-sm font-medium text-gray-700 mb-1">
                                🇬🇧 {{ __('English') }}
                            </label>
                            <textarea name="translations[en]" 
                                      id="translations_en" 
                                      rows="3"
                                      placeholder="{{ __('Enter question in English...') }}"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('translations.en') border-red-500 @enderror">{{ old('translations.en') }}</textarea>
                            @error('translations.en')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Francés --}}
                        <div>
                            <label for="translations_fr" class="block text-sm font-medium text-gray-700 mb-1">
                                🇫🇷 {{ __('French') }}
                            </label>
                            <textarea name="translations[fr]" 
                                      id="translations_fr" 
                                      rows="3"
                                      placeholder="{{ __('Enter question in French...') }}"
                                      class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('translations.fr') border-red-500 @enderror">{{ old('translations.fr') }}</textarea>
                            @error('translations.fr')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Opciones de respuesta --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Answer Options (4 required)') }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ __('Mark one option as correct') }}</p>

                        @for($i = 0; $i < 4; $i++)
                            <div class="mb-6 p-4 border border-gray-200 rounded-lg {{ $i > 0 ? 'mt-4' : '' }}">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-700">{{ __('Option') }} {{ $i + 1 }}</h4>
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="options[{{ $i }}][is_correct]" 
                                               value="1"
                                               {{ old("options.{$i}.is_correct") ? 'checked' : '' }}
                                               class="rounded-full border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-green-500">
                                        <span class="ml-2 text-sm font-medium text-green-700">{{ __('Correct Answer') }}</span>
                                    </label>
                                    <input type="hidden" name="options[{{ $i }}][is_correct]" value="0">
                                </div>

                                <div class="space-y-3">
                                    {{-- Español --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            🇪🇸 {{ __('Spanish') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" 
                                               name="options[{{ $i }}][translations][es]" 
                                               required
                                               placeholder="{{ __('Option text in Spanish...') }}"
                                               value="{{ old("options.{$i}.translations.es") }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Inglés --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            🇬🇧 {{ __('English') }}
                                        </label>
                                        <input type="text" 
                                               name="options[{{ $i }}][translations][en]" 
                                               placeholder="{{ __('Option text in English...') }}"
                                               value="{{ old("options.{$i}.translations.en") }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    {{-- Francés --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            🇫🇷 {{ __('French') }}
                                        </label>
                                        <input type="text" 
                                               name="options[{{ $i }}][translations][fr]" 
                                               placeholder="{{ __('Option text in French...') }}"
                                               value="{{ old("options.{$i}.translations.fr") }}"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.quiz.questions.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:outline-none focus:ring transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('Create Question') }}
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-admin-layout>
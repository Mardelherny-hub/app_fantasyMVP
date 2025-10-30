<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Category') }}
            </h2>
            <a href="{{ route('admin.quiz.categories.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <strong class="text-sm font-medium text-red-800">{{ __('Whoops! There were some problems with your input.') }}</strong>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.quiz.categories.store') }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Category Details') }}</h3>

                        {{-- Code --}}
                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Code') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="code" 
                                   id="code" 
                                   value="{{ old('code') }}"
                                   required
                                   placeholder="example: tactics, strategy, statistics"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('code') border-red-500 @enderror">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Unique identifier for the category (lowercase, alphanumeric and dashes only)') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Traducciones --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Translations') }}</h3>

                        {{-- Español --}}
                        <div class="mb-6">
                            <label for="translations_es" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Name (Spanish)') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="translations[es]" 
                                   id="translations_es" 
                                   value="{{ old('translations.es') }}"
                                   required
                                   placeholder="Ej: Tácticas y Estrategias"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('translations.es') border-red-500 @enderror">
                            @error('translations.es')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Inglés --}}
                        <div class="mb-6">
                            <label for="translations_en" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Name (English)') }}
                            </label>
                            <input type="text" 
                                   name="translations[en]" 
                                   id="translations_en" 
                                   value="{{ old('translations.en') }}"
                                   placeholder="Ex: Tactics and Strategies"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('translations.en') border-red-500 @enderror">
                            @error('translations.en')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Francés --}}
                        <div class="mb-6">
                            <label for="translations_fr" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Name (French)') }}
                            </label>
                            <input type="text" 
                                   name="translations[fr]" 
                                   id="translations_fr" 
                                   value="{{ old('translations.fr') }}"
                                   placeholder="Ex: Tactiques et Stratégies"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('translations.fr') border-red-500 @enderror">
                            @error('translations.fr')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.quiz.categories.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Create Category') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-admin-layout>
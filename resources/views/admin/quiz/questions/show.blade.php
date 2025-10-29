<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Question Details') }} #{{ $question->id }}
            </h2>
            <div class="flex space-x-2">
                @can('quiz.questions.edit')
                    <a href="{{ route('admin.quiz.questions.edit', $question) }}" 
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        {{ __('Edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.quiz.questions.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Información General --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('General Information') }}</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Categoría --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                {{ __('Category') }}
                            </label>
                            <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $question->category->name }}
                            </span>
                        </div>

                        {{-- Dificultad --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                {{ __('Difficulty') }}
                            </label>
                            @if($question->difficulty === 1)
                                <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ __('Easy') }}
                                </span>
                            @elseif($question->difficulty === 2)
                                <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ __('Medium') }}
                                </span>
                            @else
                                <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ __('Hard') }}
                                </span>
                            @endif
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">
                                {{ __('Status') }}
                            </label>
                            @if($question->is_active)
                                <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    ✓ {{ __('Active') }}
                                </span>
                            @else
                                <span class="px-3 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    ✗ {{ __('Inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Fechas --}}
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500">{{ __('Created At') }}</label>
                                <p class="text-sm text-gray-900">{{ $question->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500">{{ __('Updated At') }}</label>
                                <p class="text-sm text-gray-900">{{ $question->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pregunta (Traducciones) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Question Text') }}</h3>
                    
                    @php
                        $questionTranslations = $question->translations->keyBy('locale');
                    @endphp

                    <div class="space-y-4">
                        {{-- Español --}}
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-2">
                                <span class="text-xl mr-2">🇪🇸</span>
                                <label class="text-sm font-medium text-gray-700">{{ __('Spanish') }}</label>
                            </div>
                            <p class="text-gray-900">
                                {{ $questionTranslations->get('es')->text ?? '-' }}
                            </p>
                        </div>

                        {{-- Inglés --}}
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-2">
                                <span class="text-xl mr-2">🇬🇧</span>
                                <label class="text-sm font-medium text-gray-700">{{ __('English') }}</label>
                            </div>
                            <p class="text-gray-900">
                                {{ $questionTranslations->get('en')->text ?? '-' }}
                            </p>
                        </div>

                        {{-- Francés --}}
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-2">
                                <span class="text-xl mr-2">🇫🇷</span>
                                <label class="text-sm font-medium text-gray-700">{{ __('French') }}</label>
                            </div>
                            <p class="text-gray-900">
                                {{ $questionTranslations->get('fr')->text ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Opciones de Respuesta --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Answer Options') }}</h3>
                    
                    <div class="space-y-4">
                        @foreach($question->options()->orderBy('order')->get() as $option)
                            @php
                                $optionTranslations = $option->translations->keyBy('locale');
                            @endphp

                            <div class="p-4 rounded-lg border-2 {{ $option->is_correct ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-700">
                                        {{ __('Option') }} {{ $option->order }}
                                    </h4>
                                    @if($option->is_correct)
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-500 text-white">
                                            ✓ {{ __('Correct Answer') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="space-y-2">
                                    {{-- Español --}}
                                    <div class="flex items-start">
                                        <span class="text-lg mr-2">🇪🇸</span>
                                        <div class="flex-1">
                                            <span class="text-xs text-gray-500">{{ __('Spanish') }}:</span>
                                            <p class="text-sm text-gray-900">{{ $optionTranslations->get('es')->text ?? '-' }}</p>
                                        </div>
                                    </div>

                                    {{-- Inglés --}}
                                    <div class="flex items-start">
                                        <span class="text-lg mr-2">🇬🇧</span>
                                        <div class="flex-1">
                                            <span class="text-xs text-gray-500">{{ __('English') }}:</span>
                                            <p class="text-sm text-gray-900">{{ $optionTranslations->get('en')->text ?? '-' }}</p>
                                        </div>
                                    </div>

                                    {{-- Francés --}}
                                    <div class="flex items-start">
                                        <span class="text-lg mr-2">🇫🇷</span>
                                        <div class="flex-1">
                                            <span class="text-xs text-gray-500">{{ __('French') }}:</span>
                                            <p class="text-sm text-gray-900">{{ $optionTranslations->get('fr')->text ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Acciones adicionales --}}
            <div class="mt-6 flex justify-end space-x-3">
                @can('quiz.questions.activate')
                    <form action="{{ route('admin.quiz.questions.toggle', $question) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 {{ $question->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none transition ease-in-out duration-150">
                            @if($question->is_active)
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                                {{ __('Deactivate') }}
                            @else
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ __('Activate') }}
                            @endif
                        </button>
                    </form>
                @endcan

                @can('quiz.questions.delete')
                    <form action="{{ route('admin.quiz.questions.destroy', $question) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('{{ __('Are you sure you want to delete this question?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            {{ __('Delete') }}
                        </button>
                    </form>
                @endcan
            </div>

        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Quiz') }}
            </h2>
            <a href="{{ route('admin.quiz.quizzes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
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

            <form action="{{ route('admin.quiz.quizzes.store') }}" method="POST">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">{{ __('Quiz Details') }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Título --}}
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Title') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="title" 
                                       id="title" 
                                       value="{{ old('title') }}"
                                       required
                                       placeholder="Ej: Historia del Fútbol Mundial"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tipo --}}
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Type') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="type" 
                                        id="type" 
                                        required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                                    <option value="">{{ __('Select type') }}</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Categoría --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Category') }}
                                </label>
                                <select name="category_id" 
                                        id="category_id" 
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('category_id') border-red-500 @enderror">
                                    <option value="">{{ __('Mixed / Random') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Leave empty for mixed categories') }}
                                </p>
                            </div>

                            {{-- Idioma --}}
                            <div>
                                <label for="locale" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Language') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="locale" 
                                        id="locale" 
                                        required
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('locale') border-red-500 @enderror">
                                    @foreach($locales as $code => $name)
                                        <option value="{{ $code }}" {{ old('locale', 'es') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('locale')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Cantidad de preguntas --}}
                            <div>
                                <label for="questions_count" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Number of Questions') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="questions_count" 
                                       id="questions_count" 
                                       value="{{ old('questions_count', 10) }}"
                                       required
                                       min="5"
                                       max="50"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('questions_count') border-red-500 @enderror">
                                @error('questions_count')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tiempo límite por pregunta --}}
                            <div>
                                <label for="time_limit_sec" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Time per Question (seconds)') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="time_limit_sec" 
                                       id="time_limit_sec" 
                                       value="{{ old('time_limit_sec', 30) }}"
                                       required
                                       min="10"
                                       max="300"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('time_limit_sec') border-red-500 @enderror">
                                @error('time_limit_sec')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Recompensa --}}
                            <div>
                                <label for="reward_amount" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Reward Amount') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="reward_amount" 
                                       id="reward_amount" 
                                       value="{{ old('reward_amount', 0) }}"
                                       required
                                       min="0"
                                       step="0.01"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('reward_amount') border-red-500 @enderror">
                                @error('reward_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Virtual coins awarded on completion') }}
                                </p>
                            </div>

                            {{-- Estado activo --}}
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                    {{ __('Active') }}
                                </label>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Info box --}}
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                {{ __('After creating the quiz, you will be redirected to assign questions manually or automatically.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.quiz.quizzes.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Create Quiz') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Quiz') }}: {{ $quiz->title }}
            </h2>
            <a href="{{ route('admin.quiz.quizzes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Back to List') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Mensajes --}}
            @if (session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

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

            {{-- SECCIÓN 1: Detalles del Quiz --}}
            <form action="{{ route('admin.quiz.quizzes.update', $quiz->id) }}" method="POST">
                @csrf
                @method('PUT')

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
                                       value="{{ old('title', $quiz->title) }}"
                                       required
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
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}" {{ old('type', $quiz->type) == $value ? 'selected' : '' }}>
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
                                        <option value="{{ $category->id }}" {{ old('category_id', $quiz->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                        <option value="{{ $code }}" {{ old('locale', $quiz->locale) == $code ? 'selected' : '' }}>
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
                                       value="{{ old('questions_count', $quiz->questions_count) }}"
                                       required
                                       min="5"
                                       max="50"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('questions_count') border-red-500 @enderror">
                                @error('questions_count')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tiempo límite --}}
                            <div>
                                <label for="time_limit_sec" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Time per Question (seconds)') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       name="time_limit_sec" 
                                       id="time_limit_sec" 
                                       value="{{ old('time_limit_sec', $quiz->time_limit_sec) }}"
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
                                       value="{{ old('reward_amount', $quiz->reward_amount) }}"
                                       required
                                       min="0"
                                       step="0.01"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('reward_amount') border-red-500 @enderror">
                                @error('reward_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Estado activo --}}
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       value="1"
                                       {{ old('is_active', $quiz->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                                    {{ __('Active') }}
                                </label>
                            </div>

                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Update Quiz') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- SECCIÓN 2: Question Builder --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold">{{ __('Quiz Questions') }}</h3>
                        <span class="text-sm text-gray-600">
                            {{ __('Assigned:') }} {{ $quiz->quizQuestions->count() }} / {{ $quiz->questions_count }}
                        </span>
                    </div>

                    {{-- Auto-assign form --}}
                    <form action="{{ route('admin.quiz.quizzes.auto-assign-questions', $quiz->id) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Auto-assign Random Questions') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="auto_count" class="block text-sm text-gray-600 mb-1">{{ __('Quantity') }}</label>
                                    <input type="number" 
                                           name="count" 
                                           id="auto_count" 
                                           value="{{ $quiz->questions_count }}"
                                           min="5"
                                           max="50"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label for="auto_difficulty" class="block text-sm text-gray-600 mb-1">{{ __('Difficulty') }}</label>
                                    <select name="difficulty" 
                                            id="auto_difficulty" 
                                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <option value="">{{ __('Mixed') }}</option>
                                        <option value="1">{{ __('Easy') }}</option>
                                        <option value="2">{{ __('Medium') }}</option>
                                        <option value="3">{{ __('Hard') }}</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        {{ __('Auto-assign') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Current assigned questions --}}
                    @if($quiz->quizQuestions->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Currently Assigned Questions') }}</h4>
                            <div class="space-y-2">
                                @foreach($quiz->quizQuestions->sortBy('order') as $quizQuestion)
                                    @php
                                        $question = $quizQuestion->question;
                                        $translation = $question->translations->firstWhere('locale', $quiz->locale) 
                                                    ?? $question->translations->first();
                                    @endphp
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div class="flex-1">
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-800 text-xs font-medium mr-3">
                                                {{ $quizQuestion->order }}
                                            </span>
                                            <span class="text-sm text-gray-900">{{ $translation->text ?? 'N/A' }}</span>
                                            <span class="ml-2 text-xs text-gray-500">
                                                ({{ $question->difficulty == 1 ? 'Easy' : ($question->difficulty == 2 ? 'Medium' : 'Hard') }})
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Manual assign form --}}
                    <form action="{{ route('admin.quiz.quizzes.assign-questions', $quiz->id) }}" method="POST" id="manualAssignForm">
                        @csrf
                        <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Manual Selection') }}</h4>
                        <div class="mb-4">
                            <label class="block text-sm text-gray-600 mb-2">
                                {{ __('Select questions (hold Ctrl/Cmd to select multiple)') }}
                            </label>
                            <select name="question_ids[]" 
                                    multiple 
                                    size="10"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                @foreach($availableQuestions as $question)
                                    @php
                                        $translation = $question->translations->firstWhere('locale', $quiz->locale) 
                                                    ?? $question->translations->first();
                                    @endphp
                                    <option value="{{ $question->id }}">
                                        [{{ $question->difficulty == 1 ? 'Easy' : ($question->difficulty == 2 ? 'Medium' : 'Hard') }}] 
                                        {{ $translation->text ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-gray-500">
                                {{ __('Available questions:') }} {{ $availableQuestions->count() }}
                            </p>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ __('Assign Selected Questions') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
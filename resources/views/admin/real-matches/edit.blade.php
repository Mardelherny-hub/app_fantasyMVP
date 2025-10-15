<x-admin-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Edit Match') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-matches.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Matches') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Edit') }}</span>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Formulario --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                
                <form action="{{ route('admin.real-matches.update', [app()->getLocale(), $realMatch]) }}" method="POST" class="space-y-6">
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
                               value="{{ old('external_id', $realMatch->external_id) }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('external_id') border-red-300 @enderror">
                        @error('external_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fixture --}}
                    <div>
                        <label for="real_fixture_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Fixture') }} <span class="text-red-500">*</span>
                        </label>
                        <select name="real_fixture_id" 
                                id="real_fixture_id" 
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('real_fixture_id') border-red-300 @enderror">
                            <option value="">{{ __('Select fixture') }}</option>
                            @foreach($fixtures as $fixture)
                                <option value="{{ $fixture->id }}" {{ old('real_fixture_id', $realMatch->real_fixture_id) == $fixture->id ? 'selected' : '' }}>
                                    {{ $fixture->homeTeam->name ?? 'TBD' }} vs {{ $fixture->awayTeam->name ?? 'TBD' }} 
                                    ({{ $fixture->competition->name }} - {{ $fixture->match_date_utc->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('real_fixture_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Resultado --}}
                    <div class="grid grid-cols-2 gap-6">
                        
                        {{-- Home Score --}}
                        <div>
                            <label for="home_score" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Home Score') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="home_score" 
                                   id="home_score" 
                                   value="{{ old('home_score', $realMatch->home_score) }}"
                                   min="0"
                                   required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('home_score') border-red-300 @enderror">
                            @error('home_score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Away Score --}}
                        <div>
                            <label for="away_score" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Away Score') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="away_score" 
                                   id="away_score" 
                                   value="{{ old('away_score', $realMatch->away_score) }}"
                                   min="0"
                                   required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('away_score') border-red-300 @enderror">
                            @error('away_score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Estado y Minuto --}}
                    <div class="grid grid-cols-2 gap-6">
                        
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
                                    <option value="{{ $status }}" {{ old('status', $realMatch->status) == $status ? 'selected' : '' }}>
                                        {{ __(strtoupper($status)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Minuto --}}
                        <div>
                            <label for="minute" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Minute') }}
                                <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                            </label>
                            <input type="number" 
                                   name="minute" 
                                   id="minute" 
                                   value="{{ old('minute', $realMatch->minute) }}"
                                   min="0"
                                   max="120"
                                   placeholder="90"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('minute') border-red-300 @enderror">
                            @error('minute')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Fechas --}}
                    <div class="grid grid-cols-2 gap-6">
                        
                        {{-- Started At --}}
                        <div>
                            <label for="started_at_utc" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Started At') }} (UTC)
                                <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                            </label>
                            <input type="datetime-local" 
                                   name="started_at_utc" 
                                   id="started_at_utc" 
                                   value="{{ old('started_at_utc', $realMatch->started_at_utc ? $realMatch->started_at_utc->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('started_at_utc') border-red-300 @enderror">
                            @error('started_at_utc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Finished At --}}
                        <div>
                            <label for="finished_at_utc" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Finished At') }} (UTC)
                                <span class="text-gray-500 font-normal">({{ __('Optional') }})</span>
                            </label>
                            <input type="datetime-local" 
                                   name="finished_at_utc" 
                                   id="finished_at_utc" 
                                   value="{{ old('finished_at_utc', $realMatch->finished_at_utc ? $realMatch->finished_at_utc->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('finished_at_utc') border-red-300 @enderror">
                            @error('finished_at_utc')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.real-matches.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Update Match') }}
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</x-admin-layout>
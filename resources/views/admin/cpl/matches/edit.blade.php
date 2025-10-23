<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit CPL Match') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.cpl.matches.update', [app()->getLocale(), $match]) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Fixture') }}</label>
                            <select name="real_fixture_id" required class="w-full rounded border-gray-300">
                                @foreach($fixtures as $fixture)
                                    <option value="{{ $fixture->id }}" {{ $match->real_fixture_id == $fixture->id ? 'selected' : '' }}>
                                        {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }} 
                                        ({{ $fixture->match_date_utc->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('real_fixture_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Status') }}</label>
                            <select name="status" required class="w-full rounded border-gray-300">
                                <option value="live" {{ $match->status == 'live' ? 'selected' : '' }}>Live</option>
                                <option value="ht" {{ $match->status == 'ht' ? 'selected' : '' }}>Half Time</option>
                                <option value="ft" {{ $match->status == 'ft' ? 'selected' : '' }}>Full Time</option>
                                <option value="finished" {{ $match->status == 'finished' ? 'selected' : '' }}>Finished</option>
                                <option value="postponed" {{ $match->status == 'postponed' ? 'selected' : '' }}>Postponed</option>
                                <option value="canceled" {{ $match->status == 'canceled' ? 'selected' : '' }}>Canceled</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Home Score') }}</label>
                                <input type="number" name="home_score" value="{{ $match->home_score }}" min="0" class="w-full rounded border-gray-300">
                                @error('home_score')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Away Score') }}</label>
                                <input type="number" name="away_score" value="{{ $match->away_score }}" min="0" class="w-full rounded border-gray-300">
                                @error('away_score')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Minute') }}</label>
                            <input type="number" name="minute" value="{{ $match->minute }}" min="0" max="120" class="w-full rounded border-gray-300">
                            @error('minute')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Started At') }}</label>
                                <input type="datetime-local" name="started_at_utc" 
                                       value="{{ $match->started_at_utc ? $match->started_at_utc->format('Y-m-d\TH:i') : '' }}" 
                                       class="w-full rounded border-gray-300">
                                @error('started_at_utc')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Finished At') }}</label>
                                <input type="datetime-local" name="finished_at_utc" 
                                       value="{{ $match->finished_at_utc ? $match->finished_at_utc->format('Y-m-d\TH:i') : '' }}" 
                                       class="w-full rounded border-gray-300">
                                @error('finished_at_utc')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <a href="{{ route('admin.cpl.matches.index', app()->getLocale()) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Update Match') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
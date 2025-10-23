<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Fixtures') }}</h1>
            <button onclick="document.getElementById('generateModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                {{ __('Generate Fixtures') }}
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select name="league_id" class="rounded-lg border-gray-300">
                    <option value="">{{ __('All Leagues') }}</option>
                    @foreach($leagues as $league)
                        <option value="{{ $league->id }}" {{ request('league_id') == $league->id ? 'selected' : '' }}>
                            {{ $league->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="rounded-lg border-gray-300">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Finished') }}</option>
                </select>

                <select name="is_playoff" class="rounded-lg border-gray-300">
                    <option value="">{{ __('All Types') }}</option>
                    <option value="0" {{ request('is_playoff') === '0' ? 'selected' : '' }}>{{ __('Regular') }}</option>
                    <option value="1" {{ request('is_playoff') === '1' ? 'selected' : '' }}>{{ __('Playoffs') }}</option>
                </select>

                <button type="submit" class="bg-gray-600 text-white rounded-lg px-4 py-2 hover:bg-gray-700">
                    {{ __('Filter') }}
                </button>
            </form>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('GW') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Match') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Result') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($fixtures as $fixture)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                GW{{ $fixture->gameweek->number }}
                                @if($fixture->is_playoff)
                                    <span class="ml-2 px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">
                                        {{ __('Playoff') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $fixture->homeTeam->name }} vs {{ $fixture->awayTeam->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                {{ $fixture->home_goals }} - {{ $fixture->away_goals }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($fixture->status === 0)
                                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">{{ __('Pending') }}</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">{{ __('Finished') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.fantasy.fixtures.show', ['locale' => $locale, 'fixture' => $fixture]) }}" 
                                   class="text-blue-600 hover:text-blue-900">{{ __('View') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No fixtures found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="px-6 py-4">
                {{ $fixtures->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Generate -->
    <div id="generateModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">{{ __('Generate Fixtures') }}</h3>
            <form method="POST" action="{{ route('admin.fantasy.fixtures.generate', $locale) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('League') }}</label>
                        <select name="league_id" required class="w-full rounded-lg border-gray-300">
                            @foreach($leagues as $league)
                                <option value="{{ $league->id }}">{{ $league->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Type') }}</label>
                        <select name="type" required class="w-full rounded-lg border-gray-300">
                            <option value="regular">{{ __('Regular Season') }}</option>
                            <option value="playoffs">{{ __('Playoffs') }}</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            {{ __('Generate') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
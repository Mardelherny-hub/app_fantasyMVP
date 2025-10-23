<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('CPL Matches') }}
            </h2>
            <a href="{{ route('admin.cpl.matches.create', app()->getLocale()) }}" 
               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Create Match') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" class="mb-6 flex gap-4">
                        <select name="status" class="rounded border-gray-300">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>Live</option>
                            <option value="finished" {{ request('status') == 'finished' ? 'selected' : '' }}>Finished</option>
                            <option value="postponed" {{ request('status') == 'postponed' ? 'selected' : '' }}>Postponed</option>
                        </select>
                        <button type="submit" class="bg-gray-500 text-white px-4 py-2 rounded">
                            {{ __('Filter') }}
                        </button>
                    </form>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Match') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Score') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($matches as $match)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        {{ $match->started_at_utc ? $match->started_at_utc->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        {{ $match->fixture->homeTeam->name ?? 'TBD' }} vs {{ $match->fixture->awayTeam->name ?? 'TBD' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold">
                                        {{ $match->home_score ?? '-' }} - {{ $match->away_score ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $match->status == 'finished' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $match->status == 'live' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $match->status == 'postponed' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                            {{ strtoupper($match->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('admin.cpl.matches.events.index', [app()->getLocale(), $match]) }}" 
                                           class="text-blue-600 hover:text-blue-900 mr-3">{{ __('Events') }}</a>
                                        <a href="{{ route('admin.cpl.matches.edit', [app()->getLocale(), $match]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3">{{ __('Edit') }}</a>
                                        <form action="{{ route('admin.cpl.matches.destroy', [app()->getLocale(), $match]) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('{{ __('Are you sure?') }}')">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        {{ __('No matches found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $matches->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
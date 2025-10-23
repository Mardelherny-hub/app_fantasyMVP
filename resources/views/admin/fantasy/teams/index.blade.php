<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fantasy Teams') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">{{ __('Total Teams') }}</div>
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">{{ __('In League') }}</div>
                    <div class="text-2xl font-bold">{{ $stats['with_league'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">{{ __('Bots') }}</div>
                    <div class="text-2xl font-bold">{{ $stats['bots'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-4">
                    <div class="text-sm text-gray-500">{{ __('Complete Squad') }}</div>
                    <div class="text-2xl font-bold">{{ $stats['complete'] }}</div>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        
                        {{-- Búsqueda --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Search') }}
                            </label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('Team name...') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Liga --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('League') }}
                            </label>
                            <select name="league_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All') }}</option>
                                @foreach($leagues as $league)
                                    <option value="{{ $league->id }}" 
                                            {{ request('league_id') == $league->id ? 'selected' : '' }}>
                                        {{ $league->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bots --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Type') }}
                            </label>
                            <select name="bots" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All') }}</option>
                                <option value="no" {{ request('bots') == 'no' ? 'selected' : '' }}>
                                    {{ __('Users') }}
                                </option>
                                <option value="yes" {{ request('bots') == 'yes' ? 'selected' : '' }}>
                                    {{ __('Bots') }}
                                </option>
                            </select>
                        </div>

                        {{-- Squad Complete --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Squad') }}
                            </label>
                            <select name="squad_complete" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('All') }}</option>
                                <option value="yes" {{ request('squad_complete') == 'yes' ? 'selected' : '' }}>
                                    {{ __('Complete') }}
                                </option>
                                <option value="no" {{ request('squad_complete') == 'no' ? 'selected' : '' }}>
                                    {{ __('Incomplete') }}
                                </option>
                            </select>
                        </div>

                        {{-- Botón Filtrar --}}
                        <div class="flex items-end">
                            <button type="submit" 
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                {{ __('Filter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Team') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Manager') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('League') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Points') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Budget') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($teams as $team)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($team->emblem_url)
                                                <img src="{{ $team->emblem_url }}" 
                                                     alt="{{ $team->name }}" 
                                                     class="h-10 w-10 rounded-full mr-3">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                    <span class="text-gray-500 font-bold">
                                                        {{ substr($team->name, 0, 1) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $team->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $team->slug }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($team->is_bot)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                🤖 {{ __('Bot') }}
                                            </span>
                                        @elseif($team->user)
                                            <div class="text-sm text-gray-900">{{ $team->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $team->user->email }}</div>
                                        @else
                                            <span class="text-sm text-gray-400">{{ __('No owner') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($team->league)
                                            {{ $team->league->name }}
                                        @else
                                            <span class="text-gray-400">{{ __('No league') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-lg font-bold text-indigo-600">
                                            {{ number_format($team->total_points) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-900">
                                        ${{ number_format($team->budget, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($team->is_squad_complete)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                ✓ {{ __('Complete') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                ⚠ {{ __('Incomplete') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('admin.fantasy.teams.show', [app()->getLocale(), $team->id]) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 mr-3">
                                            {{ __('View') }}
                                        </a>
                                        <a href="{{ route('admin.fantasy.teams.edit', [app()->getLocale(), $team->id]) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ __('Edit') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('No teams found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($teams->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $teams->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Matches Played') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Real Matches') }}</span>
                        </nav>
                    </div>
                    <a href="{{ route('admin.real-matches.create', app()->getLocale()) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        {{ __('Create Match') }}
                    </a>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filtros --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" action="{{ route('admin.real-matches.index', app()->getLocale()) }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        
                        {{-- Competición --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Competition') }}
                            </label>
                            <select name="competition" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('All') }}</option>
                                @foreach($competitions as $comp)
                                    <option value="{{ $comp->id }}" {{ request('competition') == $comp->id ? 'selected' : '' }}>
                                        {{ $comp->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Temporada --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Season') }}
                            </label>
                            <select name="season" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('All') }}</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" {{ request('season') == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Status') }}
                            </label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('All') }}</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ __(strtoupper($status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Fecha desde --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Date From') }}
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Fecha hasta --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Date To') }}
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Búsqueda por equipo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Search Team') }}
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="{{ __('Team name') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.real-matches.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Clear') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Date') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Match') }}
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Score') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Competition') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($matches as $match)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($match->started_at_utc)
                                            <div>{{ $match->started_at_utc->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $match->started_at_utc->format('H:i') }}</div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="font-medium text-gray-900">{{ $match->fixture->homeTeam->name ?? 'TBD' }}</div>
                                        <div class="text-gray-500">{{ $match->fixture->awayTeam->name ?? 'TBD' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-lg font-bold text-gray-900">
                                            {{ $match->home_score }} - {{ $match->away_score }}
                                        </div>
                                        @if($match->status === 'live' && $match->minute)
                                            <div class="text-xs text-green-600 font-medium">{{ $match->minute }}'</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="text-gray-900">{{ $match->fixture->competition->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $match->fixture->season->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'live' => 'bg-green-100 text-green-800',
                                                'ht' => 'bg-yellow-100 text-yellow-800',
                                                'ft' => 'bg-blue-100 text-blue-800',
                                                'finished' => 'bg-gray-100 text-gray-800',
                                                'postponed' => 'bg-yellow-100 text-yellow-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$match->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                            {{ __(strtoupper($match->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.real-matches.show', [app()->getLocale(), $match]) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                {{ __('View') }}
                                            </a>
                                            <form action="{{ route('admin.real-matches.destroy', [app()->getLocale(), $match]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('No matches found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($matches->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $matches->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-admin-layout>
<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Real Players') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Real Players') }}</span>
                        </nav>
                    </div>
                    <a href="{{ route('admin.real-players.create', app()->getLocale()) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        {{ __('Create Player') }}
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
                <form method="GET" action="{{ route('admin.real-players.index', app()->getLocale()) }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        {{-- Búsqueda --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Search') }}
                            </label>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="{{ __('Player name') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Posición --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Position') }}
                            </label>
                            <select name="position" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('All') }}</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                                        {{ $pos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nacionalidad --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Nationality') }}
                            </label>
                            <select name="nationality" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('All') }}</option>
                                @foreach($nationalities as $nat)
                                    <option value="{{ $nat }}" {{ request('nationality') == $nat ? 'selected' : '' }}>
                                        {{ $nat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Equipo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Current Team') }}
                            </label>
                            <select name="team" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('All') }}</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ request('team') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" 
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
                                    {{ __('Player') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Position') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Nationality') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Current Team') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Age') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($players as $player)
                                @php
                                    $currentMembership = $player->memberships->firstWhere('to_date', null);
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            @if($player->photo_url)
                                                <img src="{{ $player->photo_url }}" alt="{{ $player->full_name }}" class="w-10 h-10 rounded-full mr-3">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                    <span class="text-gray-500 font-medium text-sm">
                                                        {{ substr($player->full_name, 0, 2) }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $player->full_name }}</div>
                                                @if($player->external_id)
                                                    <div class="text-xs text-gray-500">ID: {{ $player->external_id }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full 
                                            {{ $player->position === 'GK' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $player->position === 'DF' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $player->position === 'MF' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $player->position === 'FW' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ $player->position }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $player->nationality ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($currentMembership)
                                            <div class="text-gray-900">{{ $currentMembership->team->name }}</div>
                                            @if($currentMembership->shirt_number)
                                                <div class="text-xs text-gray-500">#{{ $currentMembership->shirt_number }}</div>
                                            @endif
                                        @else
                                            <span class="text-gray-400">{{ __('Free Agent') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($player->birthdate)
                                            {{ \Carbon\Carbon::parse($player->birthdate)->age }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <a href="{{ route('admin.real-players.show', [app()->getLocale(), $player]) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{ route('admin.real-players.edit', [app()->getLocale(), $player]) }}" 
                                               class="text-yellow-600 hover:text-yellow-900">
                                                {{ __('Edit') }}
                                            </a>
                                            <form action="{{ route('admin.real-players.destroy', [app()->getLocale(), $player]) }}" 
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
                                        {{ __('No players found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($players->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $players->links() }}
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-admin-layout>
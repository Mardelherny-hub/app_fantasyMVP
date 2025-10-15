<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $realTeam->name }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-teams.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Teams') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ $realTeam->name }}</span>
                        </nav>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.real-teams.edit', [app()->getLocale(), $realTeam]) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.real-teams.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- Info del Equipo --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        @if($realTeam->logo_url)
                            <img src="{{ $realTeam->logo_url }}" alt="{{ $realTeam->name }}" class="w-32 h-32 mx-auto mb-4">
                        @else
                            <div class="w-32 h-32 mx-auto mb-4 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="text-4xl text-gray-400">⚽</span>
                            </div>
                        @endif
                        
                        <h2 class="text-xl font-bold text-center text-gray-900 mb-2">{{ $realTeam->name }}</h2>
                        
                        @if($realTeam->short_name)
                            <p class="text-center text-gray-500 mb-4">{{ $realTeam->short_name }}</p>
                        @endif
                        
                        <div class="space-y-3 mt-6">
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Country') }}</p>
                                <p class="font-medium text-gray-900">{{ $realTeam->country ?? '-' }}</p>
                            </div>
                            @if($realTeam->founded_year)
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Founded') }}</p>
                                    <p class="font-medium text-gray-900">{{ $realTeam->founded_year }}</p>
                                </div>
                            @endif
                            @if($realTeam->stadium)
                                <div>
                                    <p class="text-sm text-gray-600">{{ __('Stadium') }}</p>
                                    <p class="font-medium text-gray-900">{{ $realTeam->stadium }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">{{ __('Current Players') }}</p>
                                <p class="font-medium text-blue-600">{{ $realTeam->memberships->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Plantilla del Equipo --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Current Squad') }}</h3>
                            <a href="{{ route('admin.real-teams.players.index', ['locale' => app()->getLocale(), 'realTeam' => $realTeam->id]) }}"
                                class="px-3 py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 text-sm">
                                {{ __('Add Player') }}
                            </a>
                        </div>

                        @if($realTeam->memberships->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">{{ __('No players in this team') }}</p>
                                <a href="{{ route('admin.real-teams.players.index', ['locale' => app()->getLocale(), 'realTeam' => $realTeam->id]) }}" 
                                   class="mt-3 inline-block text-blue-600 hover:text-blue-700">
                                    {{ __('Add first player') }} →
                                </a>
                            </div>
                        @else
                            {{-- Goalkeepers --}}
                            @if($playersByPosition->has('GK'))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-yellow-100 text-yellow-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">GK</span>
                                        {{ __('Goalkeepers') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition['GK'] as $membership)
                                            @php $player = $membership->player; @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $membership->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality ?? '-' }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('admin.real-players.show', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('View') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Defenders --}}
                            @if($playersByPosition->has('DF'))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">DF</span>
                                        {{ __('Defenders') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition['DF'] as $membership)
                                            @php $player = $membership->player; @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $membership->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality ?? '-' }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('admin.real-players.show', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('View') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Midfielders --}}
                            @if($playersByPosition->has('MF'))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">MF</span>
                                        {{ __('Midfielders') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition['MF'] as $membership)
                                            @php $player = $membership->player; @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $membership->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality ?? '-' }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('admin.real-players.show', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('View') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Forwards --}}
                            @if($playersByPosition->has('FW'))
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                        <span class="w-8 h-8 bg-red-100 text-red-700 rounded-full flex items-center justify-center text-xs font-bold mr-2">FW</span>
                                        {{ __('Forwards') }}
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($playersByPosition['FW'] as $membership)
                                            @php $player = $membership->player; @endphp
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                                <div class="flex items-center space-x-3">
                                                    <span class="text-sm font-mono font-bold text-gray-500 w-8">{{ $membership->shirt_number ?? '-' }}</span>
                                                    <div>
                                                        <p class="font-medium text-gray-900">{{ $player->full_name }}</p>
                                                        <p class="text-xs text-gray-500">{{ $player->nationality ?? '-' }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('admin.real-players.show', [app()->getLocale(), $player]) }}" 
                                                       class="text-blue-600 hover:text-blue-700 text-sm">
                                                        {{ __('View') }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-admin-layout>
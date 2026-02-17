<x-admin-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Fixture Details') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-fixtures.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Fixtures') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Details') }}</span>
                        </nav>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.real-fixtures.edit', [app()->getLocale(), $realFixture]) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.real-fixtures.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Back') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Partido --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Match Information') }}</h2>
                
                <div class="flex items-center justify-center mb-6 py-8 border-y">
                    <div class="text-center flex-1">
                        <div class="text-2xl font-bold text-gray-900">{{ $realFixture->homeTeam->name ?? 'TBD' }}</div>
                        @if($realFixture->homeTeam)
                            <div class="text-sm text-gray-500 mt-1">{{ $realFixture->homeTeam->country }}</div>
                        @endif
                    </div>
                    
                    <div class="px-8">
                        @if($realFixture->match)
                            <div class="text-4xl font-bold text-gray-900">
                                {{ $realFixture->match->home_score }} - {{ $realFixture->match->away_score }}
                            </div>
                            <div class="text-center mt-2">
                                @php
                                    $statusColors = [
                                        'live' => 'bg-green-100 text-green-800',
                                        'ht' => 'bg-yellow-100 text-yellow-800',
                                        'ft' => 'bg-blue-100 text-blue-800',
                                        'finished' => 'bg-gray-100 text-gray-800',
                                        'postponed' => 'bg-yellow-100 text-yellow-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $statusColors[$realFixture->match->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $color }}">
                                    {{ __(strtoupper($realFixture->match->status)) }}
                                </span>
                            </div>
                        @else
                            <div class="text-3xl font-bold text-gray-400">VS</div>
                            <div class="text-center mt-2">
                                @php
                                    $statusColors = [
                                        'scheduled' => 'bg-blue-100 text-blue-800',
                                        'postponed' => 'bg-yellow-100 text-yellow-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $color = $statusColors[$realFixture->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $color }}">
                                    {{ __(ucfirst($realFixture->status)) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="text-center flex-1">
                        <div class="text-2xl font-bold text-gray-900">{{ $realFixture->awayTeam->name ?? 'TBD' }}</div>
                        @if($realFixture->awayTeam)
                            <div class="text-sm text-gray-500 mt-1">{{ $realFixture->awayTeam->country }}</div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Date') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">
                            {{ $realFixture->match_date_utc->format('d/m/Y') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Time') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">
                            {{ $realFixture->match_time_utc ? $realFixture->match_time_utc->format('H:i') : '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Round') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">
                            {{ $realFixture->round ?? '-' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500">{{ __('Venue') }}</div>
                        <div class="font-semibold text-gray-900 mt-1">
                            {{ $realFixture->venue ?? '-' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Competición y Temporada --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                
                {{-- Competición --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Competition') }}</h3>
                    @if($realFixture->competition)
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">{{ __('Name') }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $realFixture->competition->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">{{ __('Country') }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $realFixture->competition->country }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">{{ __('Type') }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ __(ucfirst($realFixture->competition->type)) }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No competition data') }}</p>
                    @endif
                </div>

                {{-- Temporada --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Season') }}</h3>
                    @if($realFixture->season)
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">{{ __('Name') }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $realFixture->season->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">{{ __('Start Date') }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $realFixture->season->starts_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">{{ __('End Date') }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ $realFixture->season->ends_at->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No season data') }}</p>
                    @endif
                </div>

            </div>

            {{-- Datos Técnicos --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Technical Data') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">{{ __('External ID') }}</span>
                        <div class="text-sm font-medium text-gray-900 mt-1">{{ $realFixture->external_id ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">{{ __('Created At') }}</span>
                        <div class="text-sm font-medium text-gray-900 mt-1">{{ $realFixture->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">{{ __('Updated At') }}</span>
                        <div class="text-sm font-medium text-gray-900 mt-1">{{ $realFixture->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Cargar Resultado (solo si está scheduled y no tiene match) --}}
            @if($realFixture->status === 'scheduled' && !$realFixture->match)
                <div class="bg-white rounded-lg shadow-sm border border-green-200 p-6 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 mb-4">{{ __('Record Result') }}</h3>
                    <form action="{{ route('admin.real-matches.store', app()->getLocale()) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="real_fixture_id" value="{{ $realFixture->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            {{-- Home Score --}}
                            <div class="text-center">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $realFixture->homeTeam->name }}</label>
                                <input type="number" name="home_score" value="0" min="0" max="20" required
                                       class="w-24 mx-auto text-center text-2xl font-bold rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="text-center text-gray-400 text-2xl font-bold">—</div>

                            {{-- Away Score --}}
                            <div class="text-center">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $realFixture->awayTeam->name }}</label>
                                <input type="number" name="away_score" value="0" min="0" max="20" required
                                       class="w-24 mx-auto text-center text-2xl font-bold rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Status') }}</label>
                                <select name="status" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="finished">{{ __('Finished') }}</option>
                                    <option value="live">{{ __('Live') }}</option>
                                    <option value="ht">{{ __('Half Time') }}</option>
                                    <option value="postponed">{{ __('Postponed') }}</option>
                                </select>
                            </div>

                            {{-- Started At --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Started At') }}</label>
                                <input type="datetime-local" name="started_at_utc" 
                                       value="{{ $realFixture->match_date_utc->format('Y-m-d') }}T{{ $realFixture->match_time_utc ? $realFixture->match_time_utc->format('H:i') : '19:00' }}"
                                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Finished At --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Finished At') }}</label>
                                <input type="datetime-local" name="finished_at_utc"
                                       class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t">
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                                {{ __('Save Result') }}
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Partido Jugado --}}
            @if($realFixture->match)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-gray-900">{{ __('Match Played') }}</h3>
                        <a href="{{ route('admin.real-matches.show', [app()->getLocale(), $realFixture->match]) }}" 
                           class="text-sm text-blue-600 hover:text-blue-800">
                            {{ __('View Full Details') }} →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">{{ __('Status') }}</span>
                            <div class="text-sm font-medium text-gray-900 mt-1">{{ __(strtoupper($realFixture->match->status)) }}</div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">{{ __('Minute') }}</span>
                            <div class="text-sm font-medium text-gray-900 mt-1">{{ $realFixture->match->minute ?? '-' }}</div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">{{ __('Started At') }}</span>
                            <div class="text-sm font-medium text-gray-900 mt-1">
                                {{ $realFixture->match->started_at_utc ? $realFixture->match->started_at_utc->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">{{ __('Finished At') }}</span>
                            <div class="text-sm font-medium text-gray-900 mt-1">
                                {{ $realFixture->match->finished_at_utc ? $realFixture->match->finished_at_utc->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
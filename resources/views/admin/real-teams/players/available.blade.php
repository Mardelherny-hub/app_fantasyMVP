<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Header --}}
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Add Players to') }} {{ $team->name }}</h1>
                    <p class="text-sm text-gray-500 mt-1">{{ __('Players without active team membership') }}</p>
                </div>
                <a href="{{ route('admin.real-teams.show', ['locale' => app()->getLocale(), 'realTeam' => $team]) }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    {{ __('Back to Team') }}
                </a>
            </div>

            {{-- Filtros --}}
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Search') }}</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" 
                               placeholder="{{ __('Player name...') }}"
                               class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Position') }}</label>
                        <select name="position" class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('All') }}</option>
                            @php $pos = $filters['position'] ?? ''; @endphp
                            <option value="GK" @selected($pos==='GK')>GK</option>
                            <option value="DF" @selected($pos==='DF')>DF</option>
                            <option value="MF" @selected($pos==='MF')>MF</option>
                            <option value="FW" @selected($pos==='FW')>FW</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nationality') }}</label>
                        <input type="text" name="nationality" value="{{ $filters['nationality'] ?? '' }}" 
                               placeholder="CA, US, MX..."
                               maxlength="2"
                               class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Filter') }}
                        </button>
                        <a href="{{ route('admin.real-teams.players.index', ['locale' => app()->getLocale(), 'realTeam' => $team]) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Clear') }}
                        </a>
                    </div>
                </form>
            </div>

            {{-- Resultados --}}
            <form method="POST" 
                  action="{{ route('admin.real-teams.players.store', ['locale' => app()->getLocale(), 'realTeam' => $team->id]) }}"
                  x-data="{ selectedCount: 0 }">
                @csrf

                <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
                    
                    {{-- Toolbar --}}
                    <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <h3 class="font-semibold text-gray-900">{{ __('Available Players') }}</h3>
                            <span class="text-sm text-gray-500">({{ $players->total() }} {{ __('found') }})</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span x-show="selectedCount > 0" class="text-sm font-medium text-blue-600" x-text="selectedCount + ' selected'"></span>
                            <button type="button" 
                                    @click="
                                        let checkboxes = document.querySelectorAll('input[name=\'player_ids[]\']');
                                        let allChecked = [...checkboxes].every(c => c.checked);
                                        checkboxes.forEach(c => c.checked = !allChecked);
                                        selectedCount = [...checkboxes].filter(c => c.checked).length;
                                    "
                                    class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                {{ __('Select All / None') }}
                            </button>
                        </div>
                    </div>

                    {{-- Lista de jugadores --}}
                    <div class="divide-y divide-gray-200">
                        @forelse($players as $p)
                            <label class="flex items-center justify-between px-4 py-3 hover:bg-blue-50 cursor-pointer transition-colors">
                                <div class="flex items-center gap-4">
                                    <input type="checkbox" 
                                           name="player_ids[]" 
                                           value="{{ $p->id }}" 
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                           @change="selectedCount = document.querySelectorAll('input[name=\'player_ids[]\']:checked').length">
                                    
                                    @if($p->photo_url)
                                        <img src="{{ $p->photo_url }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-gray-500 font-medium text-sm">{{ substr($p->full_name, 0, 2) }}</span>
                                        </div>
                                    @endif

                                    <div>
                                        <div class="font-medium text-gray-900">{{ $p->full_name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $p->nationality ?? '—' }}
                                            @if($p->birthdate)
                                                · {{ \Carbon\Carbon::parse($p->birthdate)->age }} {{ __('years') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <span class="px-2.5 py-1 text-xs font-medium rounded-full 
                                    {{ $p->position === 'GK' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $p->position === 'DF' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $p->position === 'MF' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $p->position === 'FW' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $p->position }}
                                </span>
                            </label>
                        @empty
                            <div class="p-8 text-center text-gray-500">
                                {{ __('No available players found with these filters.') }}
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer: paginación + acción --}}
                    <div class="p-4 border-t bg-gray-50 flex items-center justify-between">
                        <div>{{ $players->links() }}</div>
                        <div class="flex items-center gap-3">
                            <label class="text-sm text-gray-700">{{ __('From date:') }}</label>
                            <input type="date" name="from_date" class="rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" 
                                   value="{{ now()->toDateString() }}">
                            <button type="submit" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                                {{ __('Add Selected to Team') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-admin-layout>
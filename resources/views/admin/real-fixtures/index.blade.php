<x-admin-layout>
    <div class="py-6" x-data="resultModal()" x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Partidos') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Partidos Programados') }}</span>
                        </nav>
                    </div>
                    <a href="{{ route('admin.real-fixtures.create', app()->getLocale()) }}" 
                       class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                        {{ __('Crear Partido') }}
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
                <form method="GET" action="{{ route('admin.real-fixtures.index', app()->getLocale()) }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        
                        {{-- Competición --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Competición') }}
                            </label>
                            <select name="competition" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('Todos') }}</option>
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
                                {{ __('Temporada') }}
                            </label>
                            <select name="season" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('Todos') }}</option>
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
                                {{ __('Estado') }}
                            </label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('Todos') }}</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ __(ucfirst($status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Ronda --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Ronda') }}
                            </label>
                            <input type="number" name="round" value="{{ request('round') }}" 
                                   placeholder="1, 2, 3..." 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        
                        {{-- Fecha desde --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Desde Fecha') }}
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Fecha hasta --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Hasta Fecha') }}
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        {{-- Búsqueda por equipo --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Buscar Equipo') }}
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="{{ __('Nombre del equipo') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('admin.real-fixtures.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Limpiar') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                            {{ __('Filtrar') }}
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
                                    {{ __('Fecha') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Partido') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Competición') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Ronda') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Estado') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Estadio') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Acciones') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($fixtures as $fixture)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $fixture->match_date_utc->format('d/m/Y') }}</div>
                                        @if($fixture->match_time_utc)
                                            <div class="text-xs text-gray-500">{{ $fixture->match_time_utc->format('H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium">{{ $fixture->homeTeam->name ?? 'TBD' }}</span>
                                            <span class="text-gray-500">vs</span>
                                            <span class="font-medium">{{ $fixture->awayTeam->name ?? 'TBD' }}</span>
                                        </div>
                                        @if($fixture->match)
                                            <div class="text-sm font-bold text-gray-900 mt-1">
                                                {{ $fixture->match->home_score }} - {{ $fixture->match->away_score }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="text-gray-900">{{ $fixture->competition->name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">{{ $fixture->season->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $fixture->round ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'scheduled' => 'bg-blue-100 text-blue-800',
                                                'postponed' => 'bg-yellow-100 text-yellow-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                            ];
                                            $color = $statusColors[$fixture->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $color }}">
                                            {{ __(ucfirst($fixture->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $fixture->venue ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            @if($fixture->status === 'scheduled' && !$fixture->match)
                                                <button type="button"
                                                        @click="openCreate({{ $fixture->id }}, '{{ addslashes($fixture->homeTeam->name) }}', '{{ addslashes($fixture->awayTeam->name) }}', '{{ $fixture->match_date_utc->format('Y-m-d') }}', '{{ $fixture->match_time_utc ? $fixture->match_time_utc->format('H:i') : '' }}')"
                                                        class="text-green-600 hover:text-green-900">
                                                    {{ __('Resultado') }}
                                                </button>
                                            @elseif($fixture->match)
                                                <button type="button"
                                                        @click="openEdit({{ $fixture->match->id }}, {{ $fixture->id }}, '{{ addslashes($fixture->homeTeam->name) }}', '{{ addslashes($fixture->awayTeam->name) }}', {{ $fixture->match->home_score }}, {{ $fixture->match->away_score }}, '{{ $fixture->match->status }}', '{{ $fixture->match->started_at_utc ? $fixture->match->started_at_utc->format('Y-m-d\TH:i') : '' }}', '{{ $fixture->match->finished_at_utc ? $fixture->match->finished_at_utc->format('Y-m-d\TH:i') : '' }}')"
                                                        class="text-yellow-600 hover:text-yellow-900">
                                                    {{ __('Editar Resultado') }}
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.real-fixtures.show', [app()->getLocale(), $fixture]) }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                {{ __('Ver') }}
                                            </a>
                                            <form action="{{ route('admin.real-fixtures.destroy', [app()->getLocale(), $fixture]) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('{{ __('¿Estás seguro?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        {{ __('No se encontraron partidos') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                @if($fixtures->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $fixtures->links() }}
                    </div>
                @endif

            </div>

        </div>

        {{-- Modal Cargar/Editar Resultado --}}
        <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                {{-- Overlay --}}
                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="close()"></div>

                {{-- Modal --}}
                <div x-show="open" x-transition class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6 z-10">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                        <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form :action="formAction" method="POST">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <input type="hidden" name="real_fixture_id" :value="fixtureId">

                        {{-- Marcador --}}
                        <div class="grid grid-cols-3 gap-4 items-end mb-6">
                            <div class="text-center">
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="homeName"></label>
                                <input type="number" name="home_score" x-model="homeScore" min="0" max="20" required
                                       class="w-20 mx-auto text-center text-2xl font-bold rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div class="text-center text-gray-400 text-2xl font-bold">—</div>
                            <div class="text-center">
                                <label class="block text-sm font-medium text-gray-700 mb-1" x-text="awayName"></label>
                                <input type="number" name="away_score" x-model="awayScore" min="0" max="20" required
                                       class="w-20 mx-auto text-center text-2xl font-bold rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- Estado + Fechas --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
                                <select name="status" x-model="status" required class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                    <option value="finished">{{ __('Finalizado') }}</option>
                                    <option value="live">{{ __('En Vivo') }}</option>
                                    <option value="ht">{{ __('Entretiempo') }}</option>
                                    <option value="postponed">{{ __('Pospuesto') }}</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Inicio') }}</label>
                                    <input type="datetime-local" name="started_at_utc" x-model="startedAt"
                                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fin') }}</label>
                                    <input type="datetime-local" name="finished_at_utc" x-model="finishedAt"
                                           class="w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                            <button type="button" @click="close()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                                {{ __('Guardar Resultado') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function resultModal() {
            return {
                open: false,
                isEdit: false,
                title: '',
                formAction: '',
                fixtureId: null,
                homeName: '',
                awayName: '',
                homeScore: 0,
                awayScore: 0,
                status: 'finished',
                startedAt: '',
                finishedAt: '',
                openCreate(fixtureId, homeName, awayName, matchDate, matchTime) {
                    this.isEdit = false;
                    this.title = 'Cargar Resultado';
                    this.formAction = '{{ route("admin.real-matches.store", app()->getLocale()) }}';
                    this.fixtureId = fixtureId;
                    this.homeName = homeName;
                    this.awayName = awayName;
                    this.homeScore = 0;
                    this.awayScore = 0;
                    this.status = 'finished';
                    this.startedAt = matchDate + 'T' + (matchTime || '19:00');
                    this.finishedAt = '';
                    this.open = true;
                },
                openEdit(matchId, fixtureId, homeName, awayName, homeScore, awayScore, status, startedAt, finishedAt) {
                    this.isEdit = true;
                    this.title = 'Editar Resultado';
                    this.formAction = '/' + '{{ app()->getLocale() }}' + '/admin/real-matches/' + matchId;
                    this.fixtureId = fixtureId;
                    this.homeName = homeName;
                    this.awayName = awayName;
                    this.homeScore = homeScore;
                    this.awayScore = awayScore;
                    this.status = status;
                    this.startedAt = startedAt || '';
                    this.finishedAt = finishedAt || '';
                    this.open = true;
                },
                close() {
                    this.open = false;
                }
            }
        }
    </script>
</x-admin-layout>
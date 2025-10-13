<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Agregar jugadores a {{ $team->name }}
                </h2>
                <p class="text-sm text-gray-600">Jugadores reales sin membresía activa.</p>
            </div>
            <a href="{{ route('admin.real-teams.show', $team) }}"
               class="inline-flex items-center px-3 py-2 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm">
               Volver al equipo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">
            <div class="bg-white shadow rounded-lg p-4">
                <form method="GET" class="grid md:grid-cols-5 gap-3">
                    <div class="md:col-span-2">
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Buscar por nombre"
                               class="w-full rounded-md border-gray-300">
                    </div>
                    <div>
                        <select name="position" class="w-full rounded-md border-gray-300">
                            <option value="">Todas las posiciones</option>
                            @php $pos = $filters['position'] ?? ''; @endphp
                            <option value="1" @selected($pos==='1')>GK</option>
                            <option value="2" @selected($pos==='2')>DF</option>
                            <option value="3" @selected($pos==='3')>MF</option>
                            <option value="4" @selected($pos==='4')>FW</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" name="nationality" value="{{ $filters['nationality'] ?? '' }}" placeholder="Nacionalidad (ISO2 ej. AR)"
                               class="w-full rounded-md border-gray-300">
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-md">Filtrar</button>
                        <a href="{{ route('admin.real-teams.players.index', $team) }}" class="text-sm text-gray-600 hover:underline">Limpiar</a>
                    </div>
                </form>
            </div>

            <form method="POST"       
                action="{{ route('admin.real-teams.players.store', ['locale' => app()->getLocale(), 'realTeam' => $team->id]) }}">
                @csrf
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b">
                        <h3 class="font-semibold">Resultados</h3>
                    </div>

                    <div class="divide-y">
                        @forelse($players as $p)
                            <label class="flex items-center justify-between p-4 hover:bg-gray-50">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" name="player_ids[]" value="{{ $p->id }}" class="rounded">
                                    <div>
                                        <div class="font-medium">{{ $p->full_name }}</div>
                                        <div class="text-xs text-gray-500">
                                            Pos: {{ $p->position }} · {{ $p->nationality ?? '—' }}
                                        </div>
                                    </div>
                                </div>
                                @if($p->photo_url)
                                    <img src="{{ $p->photo_url }}" alt="" class="w-10 h-10 rounded object-cover">
                                @endif
                            </label>
                        @empty
                            <div class="p-6 text-center text-gray-500">No hay jugadores disponibles con estos filtros.</div>
                        @endforelse
                    </div>

                    <div class="p-4 flex items-center justify-between">
                        <div>{{ $players->links() }}</div>
                        <div class="flex items-center gap-3">
                            <input type="date" name="from_date" class="rounded-md border-gray-300" value="{{ now()->toDateString() }}">
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md">
                                Agregar seleccionados
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-admin-layout>

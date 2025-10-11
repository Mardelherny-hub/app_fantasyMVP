<x-admin-layout>
    <x-slot name="header">
    <x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jugadores') }}
        </h2>

        <div class="flex space-x-2">
            <!-- Botón Importar (SVG inline) -->
            <a href="{{ route('admin.players.import', app()->getLocale()) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                {{ __('Importar') }}
            </a>

            <!-- Botón Nuevo (SVG inline) -->
            <a href="{{ route('admin.players.create', app()->getLocale()) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                {{ __('Nuevo') }}
            </a>
        </div>
    </div>
</x-slot>


    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filtros --}}
            <div class="bg-white p-4 sm:p-6 rounded-xl shadow mb-6">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">{{ __('Buscar') }}</label>
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="{{ __('Nombre o alias') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Posición') }}</label>
                        <select name="position"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Todas') }}</option>
                            <option value="1" @selected(request('position')==='1')>1 — GK</option>
                            <option value="2" @selected(request('position')==='2')>2 — DF</option>
                            <option value="3" @selected(request('position')==='3')>3 — MF</option>
                            <option value="4" @selected(request('position')==='4')>4 — FW</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Nacionalidad') }}</label>
                        <select name="nationality"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Todas') }}</option>
                            @foreach($nationalities as $code)
                                <option value="{{ $code }}" @selected(request('nationality')===$code)>{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Activo') }}</label>
                        <select name="active"
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Todos') }}</option>
                            <option value="yes" @selected(request('active')==='yes')>{{ __('Sólo activos') }}</option>
                            <option value="no"  @selected(request('active')==='no')>{{ __('Sólo inactivos') }}</option>
                        </select>
                    </div>

                    <div class="md:col-span-5 flex items-center justify-between gap-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="with_trashed" value="1" @checked(request()->boolean('with_trashed'))
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">{{ __('Incluir eliminados') }}</span>
                        </label>

                        <div class="space-x-2">
                            <a href="{{ route('admin.players.index', request()->route('locale')) }}"
                               class="px-3 py-2 text-sm rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                                {{ __('Limpiar') }}
                            </a>
                            <button class="px-3 py-2 text-sm rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                {{ __('Filtrar') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nombre') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Alias') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Posición') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nac.') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nacimiento') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Altura') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Peso') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Acciones') }}</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($players as $player)
                            @php
                                $posMap = [1=>'GK',2=>'DF',3=>'MF',4=>'FW'];
                            @endphp
                            <tr @class(['bg-gray-50'=>!is_null($player->deleted_at)])>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $player->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($player->photo_url)
                                            <img src="{{ $player->photo_url }}" alt="" class="w-10 h-10 rounded object-cover">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $player->full_name }}</div>
                                            <div class="text-xs text-gray-500">
                                                @if($player->is_active)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-green-700 text-[10px] font-semibold">{{ __('Activo') }}</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-semibold">{{ __('Inactivo') }}</span>
                                                @endif
                                                @if($player->deleted_at)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-100 text-red-700 text-[10px] font-semibold">{{ __('Eliminado') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $player->known_as }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $posMap[$player->position] ?? $player->position }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $player->nationality }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ optional($player->birthdate)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $player->height_cm ? $player->height_cm.' cm' : '' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $player->weight_kg ? $player->weight_kg.' kg' : '' }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <div class="inline-flex items-center gap-2">
                                        @if(is_null($player->deleted_at))
                                            {{-- Toggle activo --}}
                                            <form method="POST" action="{{ route('admin.players.toggle', [request()->route('locale'), $player]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="px-2 py-1 rounded border text-xs
                                                    {{ $player->is_active ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                                    {{ $player->is_active ? __('Desactivar') : __('Activar') }}
                                                </button>
                                            </form>

                                            <a href="{{ route('admin.players.edit', [request()->route('locale'), $player]) }}"
                                               class="px-2 py-1 rounded border border-indigo-300 text-indigo-700 text-xs hover:bg-indigo-50">
                                                {{ __('Editar') }}
                                            </a>

                                            <form method="POST" action="{{ route('admin.players.destroy', [request()->route('locale'), $player]) }}"
                                                  onsubmit="return confirm('{{ __('¿Eliminar este jugador?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="px-2 py-1 rounded border border-red-300 text-red-700 text-xs hover:bg-red-50">
                                                    {{ __('Eliminar') }}
                                                </button>
                                            </form>
                                        @else
                                            {{-- Restaurar --}}
                                            <form method="POST" action="{{ route('admin.players.restore', [request()->route('locale'), $player->id]) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button class="px-2 py-1 rounded border border-green-300 text-green-700 text-xs hover:bg-green-50">
                                                    {{ __('Restaurar') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-6 text-center text-sm text-gray-500">
                                    {{ __('No hay jugadores para mostrar.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 bg-gray-50">
                    {{ $players->links() }}
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

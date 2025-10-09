<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Jornadas') }}</h2>
            <a href="{{ route('admin.gameweeks.create', app()->getLocale()) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                {{ __('Crear Jornada') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Mensajes --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" class="mb-6 flex gap-3 items-end flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Temporada') }}</label>
                    <select name="season_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Todas') }}</option>
                        @foreach($seasons as $season)
                            <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>
                                {{ $season->name }} {{ $season->is_active ? '⭐' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
                    <select name="closed" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Todas') }}</option>
                        <option value="yes" {{ request('closed') === 'yes' ? 'selected' : '' }}>{{ __('Cerradas') }}</option>
                        <option value="no" {{ request('closed') === 'no' ? 'selected' : '' }}>{{ __('Abiertas') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tipo') }}</label>
                    <select name="playoff" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Todas') }}</option>
                        <option value="yes" {{ request('playoff') === 'yes' ? 'selected' : '' }}>{{ __('Playoffs') }}</option>
                        <option value="no" {{ request('playoff') === 'no' ? 'selected' : '' }}>{{ __('Temporada Regular') }}</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">{{ __('Filtrar') }}</button>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="bg-white overflow-hidden shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('GW') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Temporada') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Inicio') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Fin') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tipo') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Estado') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($gameweeks as $gameweek)
                            <tr class="{{ $gameweek->is_playoff ? 'bg-yellow-50' : '' }}">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-bold text-gray-900">GW{{ $gameweek->number }}</div>
                                    @if($gameweek->is_playoff)
                                        <div class="text-xs text-yellow-700">
                                            {{ $gameweek->playoff_round == 1 ? __('Cuartos') : '' }}
                                            {{ $gameweek->playoff_round == 2 ? __('Semifinales') : '' }}
                                            {{ $gameweek->playoff_round == 3 ? __('Final') : '' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $gameweek->season->name }}
                                    @if($gameweek->season->is_active)
                                        <span class="text-xs text-green-600">⭐</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $gameweek->starts_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $gameweek->ends_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($gameweek->is_playoff)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ __('Playoff') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ __('Regular') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.gameweeks.toggle', [app()->getLocale(), $gameweek]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition
                                                       {{ $gameweek->is_closed 
                                                          ? 'bg-red-100 text-red-800 hover:bg-red-200' 
                                                          : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                            {{ $gameweek->is_closed ? __('Cerrada') : __('Abierta') }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.gameweeks.edit', [app()->getLocale(), $gameweek]) }}" 
                                       class="text-blue-600 hover:text-blue-900">{{ __('Editar') }}</a>
                                    
                                    <form method="POST" 
                                          action="{{ route('admin.gameweeks.destroy', [app()->getLocale(), $gameweek]) }}" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta jornada?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Eliminar') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                    {{ __('No hay jornadas creadas.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $gameweeks->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
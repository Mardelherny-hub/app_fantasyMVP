<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Ligas') }}</h2>
            <a href="{{ route('admin.leagues.create', app()->getLocale()) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                {{ __('Crear Liga') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            {{-- Mensajes de éxito/error --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filtros --}}
            <form method="GET" class="bg-white p-4 rounded-xl shadow flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Buscar') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" 
                           placeholder="{{ __('Nombre o código...') }}"
                           class="w-full rounded-lg border-gray-300">
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tipo') }}</label>
                    <select name="type" class="w-full rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="1" @selected(request('type')=='1')>{{ __('Privada') }}</option>
                        <option value="2" @selected(request('type')=='2')>{{ __('Pública') }}</option>
                    </select>
                </div>
                <div class="w-40">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
                    <select name="locked" class="w-full rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="0" @selected(request('locked')==='0')>{{ __('Abierta') }}</option>
                        <option value="1" @selected(request('locked')==='1')>{{ __('Cerrada') }}</option>
                    </select>
                </div>
                <div class="w-32">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Idioma') }}</label>
                    <select name="locale" class="w-full rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="es" @selected(request('locale')=='es')>ES</option>
                        <option value="en" @selected(request('locale')=='en')>EN</option>
                        <option value="fr" @selected(request('locale')=='fr')>FR</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">
                    {{ __('Filtrar') }}
                </button>
                @if(request()->hasAny(['q','type','locked','locale']))
                    <a href="{{ route('admin.leagues.index', ['locale' => app()->getLocale()]) }}" 
                       class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        {{ __('Limpiar') }}
                    </a>
                @endif
            </form>

            {{-- Tabla --}}
            <div class="bg-white overflow-hidden shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Liga') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Código') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Owner') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tipo') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Cupos') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Estado') }}</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Acciones') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($leagues as $league)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $league->id }}</td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.leagues.show', [app()->getLocale(), $league]) }}" 
                                       class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                        {{ $league->name }}
                                    </a>
                                    <div class="text-xs text-gray-500">{{ $league->locale }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $league->code }}</code>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $league->owner->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $league->type == 1 ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $league->type == 1 ? __('Privada') : __('Pública') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $league->fantasyTeams()->count() }} / {{ $league->max_participants }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $league->is_locked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $league->is_locked ? __('Cerrada') : __('Abierta') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Toggle Lock --}}
                                        <form method="POST" 
                                              action="{{ route('admin.leagues.toggle-lock', [app()->getLocale(), $league]) }}"
                                              class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" 
                                                    class="text-xs px-2 py-1 rounded {{ $league->is_locked ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                                {{ $league->is_locked ? __('Abrir') : __('Cerrar') }}
                                            </button>
                                        </form>

                                        {{-- Editar --}}
                                        <a href="{{ route('admin.leagues.edit', [app()->getLocale(), $league]) }}" 
                                           class="text-blue-600 hover:text-blue-900">
                                            {{ __('Editar') }}
                                        </a>

                                        {{-- Eliminar --}}
                                        <form method="POST" 
                                              action="{{ route('admin.leagues.destroy', [app()->getLocale(), $league]) }}"
                                              class="inline"
                                              onsubmit="return confirm('{{ __('¿Estás seguro de eliminar esta liga?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                {{ __('Eliminar') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                    {{ __('No hay ligas registradas.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="bg-white px-4 py-3 rounded-xl shadow">
                {{ $leagues->links() }}
            </div>

        </div>
    </div>
</x-admin-layout>
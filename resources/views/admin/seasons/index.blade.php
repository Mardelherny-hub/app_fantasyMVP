<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Temporadas') }}</h2>
            <a href="{{ route('admin.seasons.create', app()->getLocale()) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                {{ __('Crear Temporada') }}
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
            <form method="GET" class="mb-6 flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Buscar') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" 
                           placeholder="Nombre o código..." 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
                    <select name="active" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Todas') }}</option>
                        <option value="yes" {{ request('active') === 'yes' ? 'selected' : '' }}>{{ __('Activas') }}</option>
                        <option value="no" {{ request('active') === 'no' ? 'selected' : '' }}>{{ __('Inactivas') }}</option>
                    </select>
                </div>
                <div class="ml-auto">
                    <button type="submit" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">{{ __('Filtrar') }}</button>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="bg-white overflow-hidden shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Código') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Inicio') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Fin') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Estado') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($seasons as $season)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $season->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $season->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <span class="font-mono px-2 py-0.5 bg-gray-100 rounded">{{ $season->code }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $season->starts_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $season->ends_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.seasons.toggle', [app()->getLocale(), $season]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition
                                                       {{ $season->is_active 
                                                          ? 'bg-green-100 text-green-800 hover:bg-green-200' 
                                                          : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                            {{ $season->is_active ? __('Activa') : __('Inactiva') }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.seasons.edit', [app()->getLocale(), $season]) }}" 
                                       class="text-blue-600 hover:text-blue-900">{{ __('Editar') }}</a>
                                    
                                    <form method="POST" 
                                          action="{{ route('admin.seasons.destroy', [app()->getLocale(), $season]) }}" 
                                          class="inline"
                                          onsubmit="return confirm('¿Estás seguro de eliminar esta temporada?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Eliminar') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                    {{ __('No hay temporadas creadas.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $seasons->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
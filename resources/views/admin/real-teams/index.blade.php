<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Equipos CPL') }}</h2>
            <a href="{{ route('admin.real-teams.create', app()->getLocale()) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                {{ __('Crear Equipo') }}
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
                           placeholder="Nombre del equipo..." 
                           class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('País') }}</label>
                    <select name="country" class="rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">{{ __('Todos') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ request('country') === $country ? 'selected' : '' }}>
                                {{ $country }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="with_trashed" 
                               value="1"
                               {{ request('with_trashed') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">{{ __('Incluir eliminados') }}</span>
                    </label>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Equipo') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre Corto') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('País') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Fundado') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Logo') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($teams as $team)
                            <tr class="{{ $team->trashed() ? 'bg-red-50' : '' }}">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $team->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $team->name }}</div>
                                    @if($team->trashed())
                                        <span class="text-xs text-red-600">{{ __('Eliminado') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <span class="font-mono px-2 py-0.5 bg-blue-100 text-blue-800 rounded">{{ $team->short_name }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <span class="font-mono">{{ $team->country }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $team->founded_year ?? '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if($team->logo_url)
                                        <img src="{{ $team->logo_url }}" alt="{{ $team->name }}" class="w-8 h-8 object-contain">
                                    @else
                                        <span class="text-xs text-gray-400">{{ __('Sin logo') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium space-x-2">
                                    @if($team->trashed())
                                        <form method="POST" 
                                              action="{{ route('admin.real-teams.restore', [app()->getLocale(), $team->id]) }}" 
                                              class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900">{{ __('Restaurar') }}</button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.real-teams.edit', [app()->getLocale(), $team]) }}" 
                                           class="text-blue-600 hover:text-blue-900">{{ __('Editar') }}</a>
                                        
                                        <form method="POST" 
                                              action="{{ route('admin.real-teams.destroy', [app()->getLocale(), $team]) }}" 
                                              class="inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este equipo?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Eliminar') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                                    {{ __('No hay equipos creados.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4">
                {{ $teams->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
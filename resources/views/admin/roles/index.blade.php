<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Roles') }}</h2>
            <a href="{{ route('admin.roles.create', app()->getLocale()) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ __('Nuevo rol') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            {{-- Filtros --}}
            <form method="GET" class="bg-white p-4 rounded-xl shadow flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Buscar') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="mt-1 rounded-lg border-gray-300" placeholder="{{ __('Nombre del rol') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Permiso') }}</label>
                    <select name="perm" class="mt-1 rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        @foreach($permissions as $perm)
                            <option value="{{ $perm }}" @selected(request('perm')===$perm)>{{ $perm }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ml-auto">
                    <button class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">{{ __('Filtrar') }}</button>
                </div>
            </form>

            {{-- Tabla --}}
            <div class="bg-white overflow-hidden shadow rounded-xl">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('ID') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nombre') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Permisos') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($roles as $role)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $role->id }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $role->name }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($role->permissions as $p)
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs">{{ $p->name }}</span>
                                        @endforeach
                                        @if($role->permissions->isEmpty())
                                            <span class="text-xs text-gray-500">{{ __('Sin permisos') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('admin.roles.edit', [app()->getLocale(), $role]) }}" class="px-3 py-1.5 text-sm bg-gray-100 rounded hover:bg-gray-200">{{ __('Editar') }}</a>

                                        <form method="POST" action="{{ route('admin.roles.destroy', [app()->getLocale(), $role]) }}" onsubmit="return confirm('{{ __('¿Eliminar este rol? Esta acción no se puede deshacer.') }}')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">{{ __('Eliminar') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">{{ __('No hay roles para mostrar.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $roles->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>

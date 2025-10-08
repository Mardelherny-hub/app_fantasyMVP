<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Usuarios') }}
            </h2>
            <a href="{{ route('admin.users.create', app()->getLocale()) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ __('Nuevo usuario') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            {{-- Filtros --}}
            <form method="GET" class="bg-white p-4 rounded-xl shadow flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Buscar') }}</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="mt-1 rounded-lg border-gray-300" placeholder="{{ __('Nombre, email o usuario') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Rol') }}</label>
                    <select name="role" class="mt-1 rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" @selected(request('role')===$role)>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Verificado') }}</label>
                    <select name="verified" class="mt-1 rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="yes" @selected(request('verified')==='yes')>{{ __('Si') }}</option>
                        <option value="no" @selected(request('verified')==='no')>{{ __('No') }}</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="with_trashed" name="with_trashed" value="1" @checked(request()->boolean('with_trashed')) class="rounded border-gray-300">
                    <label for="with_trashed" class="text-sm text-gray-700">{{ __('Incluir desactivados') }}</label>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Email') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Roles') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Verificado') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($users as $user)
                            <tr @class(['opacity-60'=>method_exists($user,'trashed') && $user->trashed()])>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $user->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $user->avatar ?? $user->profile_photo_url }}" class="w-8 h-8 rounded-full" alt="">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ '@'.$user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $r)
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded text-xs">{{ $r->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($user->email_verified_at)
                                        <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded text-xs">{{ __('Si') }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 rounded text-xs">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('admin.users.edit', [app()->getLocale(), $user]) }}" class="px-3 py-1.5 text-sm bg-gray-100 rounded hover:bg-gray-200">{{ __('Editar') }}</a>

                                        <form method="POST" action="{{ route('admin.users.toggle', [app()->getLocale(), $user]) }}">
                                            @csrf @method('PATCH')
                                            <button class="px-3 py-1.5 text-sm bg-orange-100 text-orange-700 rounded hover:bg-orange-200">
                                                {{ (method_exists($user,'trashed') && $user->trashed()) ? __('Reactivar') : __('Desactivar') }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.users.destroy', [app()->getLocale(), $user]) }}" onsubmit="return confirm('{{ __('¿Eliminar definitivamente? Esta acción no se puede deshacer.') }}')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">{{ __('Eliminar') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">{{ __('No hay usuarios para mostrar.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>

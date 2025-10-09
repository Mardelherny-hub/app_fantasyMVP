<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Ligas') }}</h2>
            <a href="{{ route('admin.leagues.create', app()->getLocale()) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                {{ __('Nueva liga') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            {{-- Filtros --}}
            <form method="GET" class="bg-white p-4 rounded-xl shadow flex flex-wrap gap-3 items-end">
                <div>
                    <x-label :value="__('Buscar')" />
                    <input type="text" name="q" value="{{ request('q') }}" class="mt-1 rounded-lg border-gray-300" placeholder="{{ __('Nombre o código') }}">
                </div>
                <div>
                    <x-label :value="__('Tipo')" />
                    <select name="type" class="mt-1 rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        <option value="1" @selected(request('type')==='1')>{{ __('Privada') }}</option>
                        <option value="2" @selected(request('type')==='2')>{{ __('Pública') }}</option>
                    </select>
                </div>
                <div>
                    <x-label :value="__('Bloqueada')" />
                    <select name="locked" class="mt-1 rounded-lg border-gray-300">
                        <option value="">{{ __('Todas') }}</option>
                        <option value="1" @selected(request('locked')==='1')>{{ __('Sí') }}</option>
                        <option value="0" @selected(request('locked')==='0')>{{ __('No') }}</option>
                    </select>
                </div>
                <div>
                    <x-label :value="__('Idioma')" />
                    <select name="locale" class="mt-1 rounded-lg border-gray-300">
                        <option value="">{{ __('Todos') }}</option>
                        @foreach(['es'=>'Español','en'=>'English','fr'=>'Français'] as $key=>$label)
                            <option value="{{ $key }}" @selected(request('locale')===$key)>{{ $label }}</option>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Liga') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Código') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Owner') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Tipo') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Cupo máx.') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Bots') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Idioma') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Bloqueada') }}</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($leagues as $league)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $league->id }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $league->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    <span class="font-mono px-2 py-0.5 bg-gray-100 rounded">{{ $league->code }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    @if($league->owner)
                                        {{ $league->owner->name }}
                                        <span class="text-xs text-gray-500">({{ $league->owner->email }})</span>
                                    @else
                                        <span class="text-xs text-gray-500">{{ __('Sin owner') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $league->type === 1 ? __('Privada') : __('Pública') }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $league->max_participants }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($league->auto_fill_bots)
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-xs">{{ __('Sí') }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-50 text-gray-700 rounded text-xs">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ strtoupper($league->locale) }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($league->is_locked)
                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded text-xs">{{ __('Sí') }}</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded text-xs">{{ __('No') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex gap-2">
                                        <a href="{{ route('admin.leagues.edit', [app()->getLocale(), $league]) }}" class="px-3 py-1.5 text-sm bg-gray-100 rounded hover:bg-gray-200">{{ __('Editar') }}</a>
                                        <form method="POST" action="{{ route('admin.leagues.destroy', [app()->getLocale(), $league]) }}" onsubmit="return confirm('{{ __('¿Eliminar esta liga?') }}')">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1.5 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">{{ __('Eliminar') }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-6 text-center text-sm text-gray-500">{{ __('No hay ligas para mostrar.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $leagues->links() }}
        </div>
    </div>
</x-admin-layout>

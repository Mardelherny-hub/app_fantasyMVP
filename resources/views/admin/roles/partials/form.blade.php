@php
    $r = $r ?? ($role ?? null);
    $selectedPermissions = old('permissions', isset($currentPermissions) ? $currentPermissions : []);
@endphp

<div class="space-y-4">
    <div>
        <x-label for="name" :value="__('Nombre del rol')" />
        <x-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name', $r->name ?? '') }}" />
        <x-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <input type="hidden" name="guard_name" value="web">

    <div>
        <x-label :value="__('Permisos')" />
        <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-2">
            @foreach($permissions as $perm)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="permissions[]" value="{{ $perm }}" class="rounded border-gray-300"
                           @checked(in_array($perm, (array)$selectedPermissions))>
                    <span>{{ $perm }}</span>
                </label>
            @endforeach
        </div>
        <x-error :messages="$errors->get('permissions')" class="mt-2" />
    </div>

    <div class="pt-4 flex items-center justify-end gap-3">
        <a href="{{ route('admin.roles.index', app()->getLocale()) }}" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">{{ __('Cancelar') }}</a>
        <x-button>{{ ($mode ?? null)==='edit' ? __('Guardar cambios') : __('Crear rol') }}</x-primary-button>
    </div>
</div>

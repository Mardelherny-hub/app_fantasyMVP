@php
    $u = $u ?? ($user ?? null);
    $selectedRoles = old('roles', isset($currentRoles) ? $currentRoles : []);
@endphp

<div class="space-y-4">
    <div>
        <x-label for="name" :value="__('Nombre')" />
        <x-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name', $u->name ?? '') }}" />
        <x-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-label for="username" :value="__('Usuario')" />
            <x-input id="username" name="username" type="text" class="mt-1 block w-full" value="{{ old('username', $u->username ?? '') }}" />
            <x-error :messages="$errors->get('username')" class="mt-2" />
        </div>
        <div>
            <x-label for="email" :value="__('Email')" />
            <x-input id="email" name="email" type="email" class="mt-1 block w-full" required value="{{ old('email', $u->email ?? '') }}" />
            <x-error :messages="$errors->get('email')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-label for="password" :value="__('Contraseña')" />
            <x-input id="password" name="password" type="password" class="mt-1 block w-full"  required @endif />
            <x-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <x-label for="password_confirmation" :value="__('Confirmar contraseña')" />
            <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full"  required @endif />
        </div>
    </div>

    <div>
        <x-label :value="__('Roles')" />
        <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-2">
            @foreach($roles as $role)
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="checkbox" name="roles[]" value="{{ $role }}" class="rounded border-gray-300"
                        @checked(in_array($role, (array)$selectedRoles))>
                    <span>{{ $role }}</span>
                </label>
            @endforeach
        </div>
        <x-error :messages="$errors->get('roles')" class="mt-2" />
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" id="email_verified_at" name="email_verified_at" value="1"
               @checked(old('email_verified_at', isset($u) && $u->email_verified_at ? 1 : 0))
               class="rounded border-gray-300">
        <label for="email_verified_at" class="text-sm text-gray-700">{{ __('Marcar email como verificado') }}</label>
    </div>

    <div class="pt-4 flex items-center justify-end gap-3">
        <a href="{{ route('admin.users.index', app()->getLocale()) }}" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">{{ __('Cancelar') }}</a>
        <x-button>{{ ($mode ?? null)==='edit' ? __('Guardar cambios') : __('Crear usuario') }}</x-primary-button>
    </div>
</div>

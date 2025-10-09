@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        <div class="font-semibold mb-1">{{ __('Hay errores en el formulario:') }}</div>
        <ul class="list-disc pl-5 space-y-0.5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $lg = $lg ?? ($league ?? null);
@endphp

<div class="space-y-4">
    <div>
        <x-label for="name" :value="__('Nombre de la liga')" />
        <x-input id="name" name="name" type="text" class="mt-1 block w-full" required value="{{ old('name', $lg->name ?? '') }}" />
        <x-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    @if(isset($lg) && $lg->code)
        <div>
            <x-label for="code" :value="__('Código (solo lectura)')" />
            <x-input id="code" name="code" type="text" class="mt-1 block w-full bg-gray-50" value="{{ old('code', $lg->code) }}" readonly />
            <p class="text-xs text-gray-500 mt-1">{{ __('Se genera automáticamente si no existe.') }}</p>
            <x-error :messages="$errors->get('code')" class="mt-2" />
        </div>
    @endif

    <div>
        <x-label for="season_id" :value="__('Temporada')" />
        <select id="season_id" name="season_id" class="mt-1 block w-full rounded-lg border-gray-300">
            <option value="">{{ __('(Usar temporada activa por defecto)') }}</option>
            @foreach($seasons as $s)
                <option value="{{ $s->id }}"
                    @selected(
                        old('season_id',
                            ($lg->season_id ?? null) ?: ($defaultSeasonId ?? null)
                        ) == $s->id
                    )>
                    {{ $s->name }} ({{ $s->code }})
                </option>
            @endforeach
        </select>
        <x-error :messages="$errors->get('season_id')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <x-label for="owner_user_id" :value="__('Propietario (manager)')" />
            <select id="owner_user_id" name="owner_user_id" class="mt-1 block w-full rounded-lg border-gray-300" required>
                <option value="">{{ __('Selecciona un usuario') }}</option>
                @foreach($owners as $u)
                    <option value="{{ $u->id }}" @selected(old('owner_user_id', $lg->owner_user_id ?? '') == $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                @endforeach
            </select>
            <x-error :messages="$errors->get('owner_user_id')" class="mt-2" />
        </div>
        <div>
            <x-label for="type" :value="__('Tipo')" />
            <select id="type" name="type" class="mt-1 block w-full rounded-lg border-gray-300">
                <option value="1" @selected(old('type', $lg->type ?? 1)==1)>{{ __('Privada') }}</option>
                <option value="2" @selected(old('type', $lg->type ?? 1)==2)>{{ __('Pública') }}</option>
            </select>
            <x-error :messages="$errors->get('type')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-label for="max_participants" :value="__('Cupo máximo')" />
            <x-input id="max_participants" name="max_participants" type="number" min="2" max="40" class="mt-1 block w-full"
                          value="{{ old('max_participants', $lg->max_participants ?? 10) }}" />
            <x-error :messages="$errors->get('max_participants')" class="mt-2" />
        </div>
        <div>
            <x-label for="playoff_teams" :value="__('Clasificados a playoffs')" />
            <x-input id="playoff_teams" name="playoff_teams" type="number" min="2" max="20" class="mt-1 block w-full"
                          value="{{ old('playoff_teams', $lg->playoff_teams ?? 5) }}" />
            <x-error :messages="$errors->get('playoff_teams')" class="mt-2" />
        </div>
        <div>
            <x-label for="locale" :value="__('Idioma de la liga')" />
            <select id="locale" name="locale" class="mt-1 block w-full rounded-lg border-gray-300">
                @foreach(['es'=>'Español','en'=>'English','fr'=>'Français'] as $key=>$label)
                    <option value="{{ $key }}" @selected(old('locale', $lg->locale ?? 'es')===$key)>{{ $label }}</option>
                @endforeach
            </select>
            <x-error :messages="$errors->get('locale')" class="mt-2" />
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <x-label for="regular_season_gameweeks" :value="__('Semanas fase regular')" />
            <x-input id="regular_season_gameweeks" name="regular_season_gameweeks" type="number" min="1" max="60" class="mt-1 block w-full"
                          value="{{ old('regular_season_gameweeks', $lg->regular_season_gameweeks ?? 27) }}" />
            <x-error :messages="$errors->get('regular_season_gameweeks')" class="mt-2" />
        </div>
        <div>
            <x-label for="total_gameweeks" :value="__('Semanas totales (incluye playoffs)')" />
            <x-input id="total_gameweeks" name="total_gameweeks" type="number" min="1" max="70" class="mt-1 block w-full"
                          value="{{ old('total_gameweeks', $lg->total_gameweeks ?? 30) }}" />
            <x-error :messages="$errors->get('total_gameweeks')" class="mt-2" />
        </div>
        <div>
            <x-label for="playoff_format" :value="__('Formato de playoffs')" />
            <select id="playoff_format" name="playoff_format" class="mt-1 block w-full rounded-lg border-gray-300">
                <option value="1" @selected(old('playoff_format', $lg->playoff_format ?? 1)==1)>{{ __('Page Playoff') }}</option>
                <option value="2" @selected(old('playoff_format', $lg->playoff_format ?? 1)==2)>{{ __('Standard') }}</option>
            </select>
            <x-error :messages="$errors->get('playoff_format')" class="mt-2" />
        </div>
    </div>

    <div class="flex items-center gap-2">
        <input type="hidden" name="auto_fill_bots" value="0">
        <input type="checkbox" id="auto_fill_bots" name="auto_fill_bots" value="1"
               @checked(old('auto_fill_bots', $lg->auto_fill_bots ?? true))
               class="rounded border-gray-300">
        <label for="auto_fill_bots" class="text-sm text-gray-700">{{ __('Completar cupos con bots automáticamente') }}</label>
        <x-error :messages="$errors->get('auto_fill_bots')" class="mt-2" />
    </div>

    {{-- is_locked se gestiona fuera del CRUD, sólo mostramos estado si edita --}}
    @if(isset($lg))
        <p class="text-sm text-gray-600">
            <strong>{{ __('Bloqueada:') }}</strong>
            {{ $lg->is_locked ? __('Sí') : __('No') }}
        </p>
    @endif

    <div class="pt-4 flex items-center justify-end gap-3">
        <a href="{{ route('admin.leagues.index', app()->getLocale()) }}" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">{{ __('Cancelar') }}</a>
        <x-button>{{ ($mode ?? null)==='edit' ? __('Guardar cambios') : __('Crear liga') }}</x-primary-button>
    </div>
</div>

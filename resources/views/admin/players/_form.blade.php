@php
    $posMap = [1=>'GK',2=>'DF',3=>'MF',4=>'FW'];
    $locale = request()->route('locale');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Nombre completo') }} *</label>
        <input type="text" name="full_name" value="{{ old('full_name', $player->full_name ?? '') }}"
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('full_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Alias / Conocido como') }}</label>
        <input type="text" name="known_as" value="{{ old('known_as', $player->known_as ?? '') }}"
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('known_as') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Posición') }} *</label>
        <select name="position"
                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Seleccione...') }}</option>
            @foreach($posMap as $k => $v)
                <option value="{{ $k }}" @selected((string)old('position', $player->position ?? '')===(string)$k)>{{ $k }} — {{ $v }}</option>
            @endforeach
        </select>
        @error('position') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Nacionalidad (ISO 3166-1 alpha-2)') }}</label>
        <input type="text" name="nationality" maxlength="2" placeholder="AR, BR, ES..."
               value="{{ old('nationality', $player->nationality ?? '') }}"
               class="mt-1 block w-28 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 uppercase">
        @error('nationality') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">{{ __('Fecha de nacimiento') }}</label>
        <input type="date" name="birthdate" value="{{ old('birthdate', optional($player->birthdate ?? null)->format('Y-m-d')) }}"
               class="mt-1 block w-56 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
        @error('birthdate') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Altura (cm)') }}</label>
            <input type="number" name="height_cm" min="150" max="220"
                   value="{{ old('height_cm', $player->height_cm ?? '') }}"
                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('height_cm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">{{ __('Peso (kg)') }}</label>
            <input type="number" name="weight_kg" min="50" max="120"
                   value="{{ old('weight_kg', $player->weight_kg ?? '') }}"
                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            @error('weight_kg') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">{{ __('URL de foto') }}</label>
        <input type="url" name="photo_url" value="{{ old('photo_url', $player->photo_url ?? '') }}"
               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://...">
        @error('photo_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        @if(!empty($player?->photo_url))
            <img src="{{ $player->photo_url }}" alt="" class="mt-3 w-28 h-28 object-cover rounded">
        @endif
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   @checked(old('is_active', $player->is_active ?? true))
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="ml-2 text-sm text-gray-700">{{ __('Activo') }}</span>
        </label>
        @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('admin.players.index', $locale) }}"
       class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
        {{ __('Cancelar') }}
    </a>

    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        {{ $submitLabel ?? __('Guardar') }}
    </button>
</div>

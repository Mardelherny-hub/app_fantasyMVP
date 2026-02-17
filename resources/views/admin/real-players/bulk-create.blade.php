<x-admin-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header --}}
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Bulk Upload Players') }}</h1>
                        <nav class="text-sm text-gray-500 mt-1">
                            <a href="{{ route('admin.dashboard', app()->getLocale()) }}" class="hover:text-gray-700">Dashboard</a>
                            <span class="mx-2">/</span>
                            <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" class="hover:text-gray-700">{{ __('Real Players') }}</a>
                            <span class="mx-2">/</span>
                            <span>{{ __('Bulk Upload') }}</span>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-red-800 mb-2">{{ __('Hay errores en el formulario') }}</h3>
                    <ul class="list-disc list-inside text-sm text-red-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Instrucciones + Descarga plantilla --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-5 mb-6">
                <h3 class="text-sm font-semibold text-blue-900 mb-2">{{ __('Instrucciones') }}</h3>
                <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside mb-4">
                    <li>{{ __('Descargá la plantilla CSV con el botón de abajo.') }}</li>
                    <li>{{ __('Completá los datos de los jugadores en Excel o Google Sheets.') }}</li>
                    <li>{{ __('Columnas obligatorias: full_name, position (GK, DF, MF, FW).') }}</li>
                    <li>{{ __('Columnas opcionales: nationality (ISO-2), birthdate (YYYY-MM-DD), shirt_number, photo_url.') }}</li>
                    <li>{{ __('Guardá como CSV y subilo seleccionando equipo y temporada.') }}</li>
                </ol>
                <a href="{{ route('admin.real-players.download-template', app()->getLocale()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Download CSV Template') }}
                </a>
            </div>

            {{-- Formulario de carga --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <form action="{{ route('admin.real-players.bulk-store', app()->getLocale()) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-6">
                    @csrf

                    {{-- Equipo y Temporada --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="real_team_id" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Team') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="real_team_id" 
                                    id="real_team_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('No team (Free Agents)') }}</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ old('real_team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="season_id" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Season') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="season_id" 
                                    id="season_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('No season') }}</option>
                                @foreach($seasons as $season)
                                    <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }} {{ $season->is_active ? '✓' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Archivo CSV --}}
                    <div>
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('CSV File') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               name="csv_file" 
                               id="csv_file" 
                               accept=".csv,.txt"
                               required
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="mt-1 text-xs text-gray-500">{{ __('Formato: CSV separado por comas. Máximo 2MB.') }}</p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('admin.real-players.index', app()->getLocale()) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700">
                            {{ __('Upload & Create Players') }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-admin-layout>
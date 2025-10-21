<div class="space-y-6">
    <!-- Header con estadísticas -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('Gestión de Precios') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Temporada') }}: {{ $season->name }}</p>
            </div>
            <button wire:click="openBulkModal" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                {{ __('Ajuste Masivo') }}
            </button>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-xs text-gray-600 uppercase">{{ __('Total Jugadores') }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_players'] ?? 0 }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-xs text-blue-600 uppercase">{{ __('Precio Promedio') }}</p>
                <p class="text-2xl font-bold text-blue-900">${{ number_format($stats['avg_price'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-xs text-green-600 uppercase">{{ __('Precio Mínimo') }}</p>
                <p class="text-2xl font-bold text-green-900">${{ number_format($stats['min_price'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-xs text-purple-600 uppercase">{{ __('Precio Máximo') }}</p>
                <p class="text-2xl font-bold text-purple-900">${{ number_format($stats['max_price'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4">
                <p class="text-xs text-orange-600 uppercase">{{ __('Valor Total') }}</p>
                <p class="text-2xl font-bold text-orange-900">${{ number_format($stats['total_value'] ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- Stats por posición -->
        @if(isset($stats['by_position']))
        <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-3">
            @foreach($stats['by_position'] as $pos => $data)
            <div class="border border-gray-200 rounded p-3">
                <p class="text-sm font-semibold text-gray-700">{{ $pos }}</p>
                <div class="mt-2 space-y-1 text-xs text-gray-600">
                    <p>{{ __('Jugadores') }}: <span class="font-medium">{{ $data['count'] }}</span></p>
                    <p>{{ __('Promedio') }}: <span class="font-medium">${{ number_format($data['avg'], 2) }}</span></p>
                    <p>{{ __('Rango') }}: <span class="font-medium">${{ number_format($data['min'], 2) }} - ${{ number_format($data['max'], 2) }}</span></p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Búsqueda -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Buscar Jugador') }}</label>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="{{ __('Nombre del jugador...') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Posición -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Posición') }}</label>
                <select wire:model.live="filterPosition" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">{{ __('Todas') }}</option>
                    @foreach($positions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Precio Mínimo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Precio Mín') }}</label>
                <input type="number" 
                       wire:model.live.debounce.500ms="filterMinPrice" 
                       step="0.01" 
                       placeholder="$0.00"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Precio Máximo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Precio Máx') }}</label>
                <input type="number" 
                       wire:model.live.debounce.500ms="filterMaxPrice" 
                       step="0.01" 
                       placeholder="$999,999.99"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <!-- Botón limpiar filtros -->
        @if($search || $filterPosition || $filterMinPrice || $filterMaxPrice)
        <div class="mt-3">
            <button wire:click="clearFilters" 
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                {{ __('Limpiar filtros') }}
            </button>
        </div>
        @endif
    </div>
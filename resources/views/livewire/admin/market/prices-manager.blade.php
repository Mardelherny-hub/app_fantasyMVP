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

        <!-- Tabla de jugadores -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('full_name')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-700">
                            {{ __('Jugador') }}
                            @if($sortBy === 'full_name')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('position')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-700">
                            {{ __('Posición') }}
                            @if($sortBy === 'position')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th wire:click="sortBy('market_value')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase cursor-pointer hover:text-gray-700">
                            {{ __('Valor de Mercado') }}
                            @if($sortBy === 'market_value')
                                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                            {{ __('Acciones') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($players as $player)
                        @php
                            $valuation = $player->valuations->first();
                            $currentPrice = $valuation ? (float) $valuation->market_value : 0.50;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900">{{ $player->known_as ?: $player->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @switch($player->position)
                                        @case(1) bg-yellow-100 text-yellow-800 @break
                                        @case(2) bg-blue-100 text-blue-800 @break
                                        @case(3) bg-green-100 text-green-800 @break
                                        @case(4) bg-red-100 text-red-800 @break
                                    @endswitch
                                ">
                                    {{ $positions[$player->position] ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($editingPlayerId === $player->id)
                                    <div class="flex items-center justify-end space-x-2">
                                        <span class="text-gray-500">$</span>
                                        <input type="number" 
                                            wire:model="editPrice" 
                                            step="0.01" 
                                            min="0.50"
                                            class="w-28 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-right text-sm">
                                        <button wire:click="savePrice" class="p-1 text-green-600 hover:text-green-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                        <button wire:click="cancelEdit" class="p-1 text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-lg font-semibold text-gray-900">${{ number_format($currentPrice, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($editingPlayerId !== $player->id)
                                    <button wire:click="startEdit({{ $player->id }})" 
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        {{ __('Editar') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                {{ __('No se encontraron jugadores') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $players->links() }}
            </div>
        </div>

        <!-- Modal Ajuste Masivo -->
        @if($showBulkModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeBulkModal"></div>
                
                <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Ajuste Masivo de Precios') }}</h3>
                    
                    <!-- Tipo de ajuste -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Tipo de Ajuste') }}</label>
                        <select wire:model.live="bulkType" class="w-full rounded-md border-gray-300">
                            <option value="position">{{ __('Por Posición') }}</option>
                            <option value="range">{{ __('Por Rango de Precio') }}</option>
                        </select>
                    </div>

                    @if($bulkType === 'position')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Posición') }}</label>
                        <select wire:model="bulkPosition" class="w-full rounded-md border-gray-300">
                            <option value="">{{ __('Seleccionar...') }}</option>
                            @foreach($positions as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @else
                    <div class="mb-4 grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Precio Mín') }}</label>
                            <input type="number" wire:model="bulkMinPrice" step="0.01" class="w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Precio Máx') }}</label>
                            <input type="number" wire:model="bulkMaxPrice" step="0.01" class="w-full rounded-md border-gray-300">
                        </div>
                    </div>
                    @endif

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Porcentaje de Ajuste') }}</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" wire:model="bulkPercentage" step="0.1" min="-50" max="100" 
                                class="w-full rounded-md border-gray-300">
                            <span class="text-gray-500 font-medium">%</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ __('Negativo = reducción, Positivo = aumento. Rango: -50% a +100%') }}</p>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeBulkModal" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            {{ __('Cancelar') }}
                        </button>
                        <button wire:click="executeBulkAdjustment" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            {{ __('Aplicar Ajuste') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    </div>
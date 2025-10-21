<div class="space-y-6">
    <!-- Precios por Posición -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Precio Promedio por Posición') }}</h3>
        <div class="grid grid-cols-4 gap-4">
            @foreach($pricesByPosition as $pos => $price)
            <div class="text-center p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-600">{{ $pos }}</p>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($price, 2) }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Top Jugadores -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Jugadores Más Vendidos') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ __('Jugador') }}</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">{{ __('Transfers') }}</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">{{ __('Precio Prom') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($topPlayers as $index => $item)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 text-sm font-medium">{{ $item['player']->full_name }}</td>
                        <td class="px-4 py-2 text-sm text-center">{{ $item['transfers_count'] }}</td>
                        <td class="px-4 py-2 text-sm text-right">${{ number_format($item['avg_price'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Equipos -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Equipos Más Activos') }}</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ __('Equipo') }}</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500">{{ __('Compras') }}</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">{{ __('Total Gastado') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($topTeams as $index => $item)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 text-sm font-medium">{{ $item['team']->name }}</td>
                        <td class="px-4 py-2 text-sm text-center">{{ $item['purchases'] }}</td>
                        <td class="px-4 py-2 text-sm text-right">${{ number_format($item['total_spent'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Estadísticas de Ofertas -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Estadísticas de Ofertas') }}</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded">
                <p class="text-sm text-gray-600">{{ __('Total') }}</p>
                <p class="text-2xl font-bold">{{ $offerStats['total'] }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded">
                <p class="text-sm text-green-600">{{ __('Aceptadas') }}</p>
                <p class="text-2xl font-bold text-green-900">{{ $offerStats['accepted'] }}</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded">
                <p class="text-sm text-red-600">{{ __('Rechazadas') }}</p>
                <p class="text-2xl font-bold text-red-900">{{ $offerStats['rejected'] }}</p>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded">
                <p class="text-sm text-yellow-600">{{ __('Pendientes') }}</p>
                <p class="text-2xl font-bold text-yellow-900">{{ $offerStats['pending'] }}</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded">
                <p class="text-sm text-blue-600">{{ __('Tasa Éxito') }}</p>
                <p class="text-2xl font-bold text-blue-900">{{ $offerStats['success_rate'] }}%</p>
            </div>
        </div>
    </div>
</div>
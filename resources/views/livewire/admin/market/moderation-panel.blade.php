<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ __('Panel de Moderación') }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Herramientas de control y supervisión del mercado') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">{{ __('Acciones críticas') }}</span>
            </div>
        </div>
    </div>

    <!-- Actividad Sospechosa -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <div class="px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('Actividad Sospechosa') }}</h3>
            </div>
            
            <!-- Tabs -->
            <div class="px-6">
                <div class="flex space-x-4 border-b">
                    <button wire:click="setActivityTab('unusual_prices')" 
                            class="px-4 py-2 text-sm font-medium transition border-b-2 
                                   {{ $activityTab === 'unusual_prices' ? 'border-red-600 text-red-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        {{ __('Precios Anormales') }}
                        @if(count($suspiciousActivity['unusual_prices'] ?? []) > 0)
                        <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                            {{ count($suspiciousActivity['unusual_prices']) }}
                        </span>
                        @endif
                    </button>
                    
                    <button wire:click="setActivityTab('hyperactive_users')" 
                            class="px-4 py-2 text-sm font-medium transition border-b-2 
                                   {{ $activityTab === 'hyperactive_users' ? 'border-orange-600 text-orange-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        {{ __('Usuarios Hiperactivos') }}
                        @if(count($suspiciousActivity['hyperactive_users'] ?? []) > 0)
                        <span class="ml-2 px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                            {{ count($suspiciousActivity['hyperactive_users']) }}
                        </span>
                        @endif
                    </button>
                    
                    <button wire:click="setActivityTab('repeated_rejections')" 
                            class="px-4 py-2 text-sm font-medium transition border-b-2 
                                   {{ $activityTab === 'repeated_rejections' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-600 hover:text-gray-900' }}">
                        {{ __('Rechazos Repetidos') }}
                        @if(count($suspiciousActivity['repeated_rejections'] ?? []) > 0)
                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                            {{ count($suspiciousActivity['repeated_rejections']) }}
                        </span>
                        @endif
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenido de tabs -->
        <div class="p-6">
            @if($activityTab === 'unusual_prices')
                <!-- Precios Anormales -->
                @if(count($suspiciousActivity['unusual_prices'] ?? []) > 0)
                <div class="space-y-4">
                    @foreach($suspiciousActivity['unusual_prices'] as $item)
                    <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded 
                                                 {{ $item['severity'] === 'high' ? 'bg-red-600 text-white' : 'bg-orange-500 text-white' }}">
                                        {{ $item['severity'] === 'high' ? __('ALTA') : __('MEDIA') }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-900">
                                        Transfer #{{ $item['transfer']->id }}
                                    </span>
                                </div>
                                
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><strong>{{ __('Jugador') }}:</strong> {{ $item['transfer']->player->full_name }}</p>
                                    <p><strong>{{ __('Precio Transfer') }}:</strong> ${{ number_format($item['transfer']->price, 2) }}</p>
                                    <p><strong>{{ __('Valor Mercado') }}:</strong> ${{ number_format($item['market_value'], 2) }}</p>
                                    <p><strong>{{ __('Ratio') }}:</strong> 
                                        <span class="font-semibold {{ $item['ratio'] > 1.5 ? 'text-red-600' : 'text-orange-600' }}">
                                            {{ number_format($item['ratio'] * 100, 0) }}%
                                        </span>
                                    </p>
                                    @if($item['transfer']->fromTeam)
                                    <p><strong>{{ __('Vendedor') }}:</strong> {{ $item['transfer']->fromTeam->name }}</p>
                                    @endif
                                    <p><strong>{{ __('Comprador') }}:</strong> {{ $item['transfer']->toTeam->name }}</p>
                                    <p><strong>{{ __('Fecha') }}:</strong> {{ $item['transfer']->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            <button wire:click="openRevertTransferModal({{ $item['transfer']->id }})" 
                                    class="ml-4 px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded transition">
                                {{ __('Revertir') }}
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-500 py-8">{{ __('No se detectaron precios anormales') }}</p>
                @endif

            @elseif($activityTab === 'hyperactive_users')
                <!-- Usuarios Hiperactivos -->
                @if(count($suspiciousActivity['hyperactive_users'] ?? []) > 0)
                <div class="space-y-4">
                    @foreach($suspiciousActivity['hyperactive_users'] as $item)
                    <div class="border border-orange-200 rounded-lg p-4 bg-orange-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><strong>{{ __('Usuario') }}:</strong> {{ $item['to_team']['user']['email'] ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Equipo') }}:</strong> {{ $item['to_team']['name'] ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Transfers en 24h') }}:</strong> 
                                        <span class="font-semibold text-orange-600">{{ $item['count'] }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            @if(isset($item['to_team']['user']['id']))
                            <button wire:click="openBlockUserModal({{ $item['to_team']['user']['id'] }})" 
                                    class="ml-4 px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded transition">
                                {{ __('Bloquear') }}
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-500 py-8">{{ __('No se detectaron usuarios hiperactivos') }}</p>
                @endif

            @elseif($activityTab === 'repeated_rejections')
                <!-- Rechazos Repetidos -->
                @if(count($suspiciousActivity['repeated_rejections'] ?? []) > 0)
                <div class="space-y-4">
                    @foreach($suspiciousActivity['repeated_rejections'] as $item)
                    <div class="border border-yellow-200 rounded-lg p-4 bg-yellow-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="space-y-1 text-sm text-gray-700">
                                    <p><strong>{{ __('Usuario') }}:</strong> {{ $item['buyer_team']['user']['email'] ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Equipo') }}:</strong> {{ $item['buyer_team']['name'] ?? 'N/A' }}</p>
                                    <p><strong>{{ __('Ofertas rechazadas (7 días)') }}:</strong> 
                                        <span class="font-semibold text-yellow-600">{{ $item['count'] }}</span>
                                    </p>
                                </div>
                            </div>
                            
                            @if(isset($item['buyer_team']['user']['id']))
                            <button wire:click="openBlockUserModal({{ $item['buyer_team']['user']['id'] }})" 
                                    class="ml-4 px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm rounded transition">
                                {{ __('Bloquear') }}
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center text-gray-500 py-8">{{ __('No se detectaron rechazos repetidos') }}</p>
                @endif
            @endif
        </div>
    </div>

    <!-- Modal: Cancelar Listing -->
    @if($showCancelListingModal && $targetListing)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeCancelListingModal"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Cancelar Listing') }}</h3>
                            
                            <div class="space-y-3 mb-4">
                                <p class="text-sm text-gray-700"><strong>{{ __('Jugador') }}:</strong> {{ $targetListing->player->full_name }}</p>
                                <p class="text-sm text-gray-700"><strong>{{ __('Precio') }}:</strong> ${{ number_format($targetListing->price, 2) }}</p>
                                <p class="text-sm text-gray-700"><strong>{{ __('Vendedor') }}:</strong> {{ $targetListing->fantasyTeam->name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Motivo de cancelación') }}</label>
                                <textarea wire:model="cancelReason" 
                                          rows="3" 
                                          class="w-full rounded-md border-gray-300"
                                          placeholder="{{ __('Explica por qué cancelas este listing...') }}"></textarea>
                                @error('cancelReason') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                    <button wire:click="closeCancelListingModal" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ __('Cancelar') }}
                    </button>
                    <button wire:click="executeCancelListing" 
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                        {{ __('Confirmar Cancelación') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal: Revertir Transfer -->
    @if($showRevertTransferModal && $targetTransfer)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeRevertTransferModal"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('⚠️ Revertir Transfer - ACCIÓN CRÍTICA') }}</h3>
                            
                            <div class="space-y-3 mb-4">
                                <p class="text-sm text-gray-700"><strong>{{ __('Transfer') }}:</strong> #{{ $targetTransfer->id }}</p>
                                <p class="text-sm text-gray-700"><strong>{{ __('Jugador') }}:</strong> {{ $targetTransfer->player->full_name }}</p>
                                <p class="text-sm text-gray-700"><strong>{{ __('Precio') }}:</strong> ${{ number_format($targetTransfer->price, 2) }}</p>
                                @if($targetTransfer->fromTeam)
                                <p class="text-sm text-gray-700"><strong>{{ __('De') }}:</strong> {{ $targetTransfer->fromTeam->name }}</p>
                                @endif
                                <p class="text-sm text-gray-700"><strong>{{ __('A') }}:</strong> {{ $targetTransfer->toTeam->name }}</p>
                            </div>

                            <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
                                <p class="text-xs text-yellow-800">
                                    <strong>{{ __('IMPORTANTE') }}:</strong> {{ __('Esta acción revertirá los presupuestos pero NO modificará automáticamente el roster. Deberás ajustarlo manualmente.') }}
                                </p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Motivo de reversión') }}</label>
                                <textarea wire:model="revertReason" 
                                          rows="3" 
                                          class="w-full rounded-md border-gray-300"
                                          placeholder="{{ __('Explica por qué reviertes este transfer...') }}"></textarea>
                                @error('revertReason') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <label class="flex items-center">
                                <input type="checkbox" wire:model="revertConfirmed" class="rounded border-gray-300 mr-2">
                                <span class="text-sm text-gray-700">{{ __('Confirmo que entiendo las consecuencias de esta acción') }}</span>
                            </label>
                            @error('revertConfirmed') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                    <button wire:click="closeRevertTransferModal" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ __('Cancelar') }}
                    </button>
                    <button wire:click="executeRevertTransfer" 
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                        {{ __('REVERTIR TRANSFER') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal: Bloquear Usuario -->
    @if($showBlockUserModal && $targetUser)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeBlockUserModal"></div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-5 pb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Bloquear Usuario del Mercado') }}</h3>
                    
                    <div class="space-y-3 mb-4">
                        <p class="text-sm text-gray-700"><strong>{{ __('Usuario') }}:</strong> {{ $targetUser->email }}</p>
                        <p class="text-sm text-gray-700"><strong>{{ __('Nombre') }}:</strong> {{ $targetUser->name }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Duración del bloqueo (horas)') }}</label>
                        <input type="number" wire:model="blockHours" min="0" max="8760" 
                               class="w-full rounded-md border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">{{ __('0 = Permanente') }}</p>
                        @error('blockHours') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Motivo del bloqueo') }}</label>
                        <textarea wire:model="blockReason" 
                                  rows="3" 
                                  class="w-full rounded-md border-gray-300"
                                  placeholder="{{ __('Explica por qué bloqueas a este usuario...') }}"></textarea>
                        @error('blockReason') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
                    <button wire:click="closeBlockUserModal" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ __('Cancelar') }}
                    </button>
                    <button wire:click="executeBlockUser" 
                            class="px-4 py-2 bg-red-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700">
                        {{ __('Bloquear Usuario') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
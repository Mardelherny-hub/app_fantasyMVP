<div>
    {{-- Deadline Warning --}}
    @if($deadline && now()->diffInHours($deadline, false) > 0 && now()->diffInHours($deadline, false) < 24)
        <div class="mb-6 bg-orange-500/20 border border-orange-500/50 rounded-lg p-4">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span class="text-orange-200 font-semibold">
                    {{ __('⏰ Te quedan :hours horas para completar tu plantilla', ['hours' => (int)now()->diffInHours($deadline, false)]) }}
                </span>
            </div>
        </div>
    @endif

    {{-- Alerts --}}
    @if (session()->has('success'))
        <div class="mb-6 bg-green-500/20 border border-green-500/50 rounded-lg p-4">
            <p class="text-green-200">{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-500/20 border border-red-500/50 rounded-lg p-4">
            <p class="text-red-200">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-12 gap-6">
        
        {{-- Panel Izquierdo: Wizard --}}
        <div class="col-span-12 lg:col-span-8">
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6">
                
                {{-- Steps Navigation --}}
                <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                    <button 
                        wire:click="goToStep(1)"
                        class="flex-1 min-w-[100px] py-3 text-center rounded-lg transition-all {{ $currentStep == 1 ? 'bg-blue-600 text-white shadow-lg' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        <span class="block text-xs">{{ __('Paso 1') }}</span>
                        <span class="block font-semibold">GK</span>
                        <span class="block text-xs">{{ $summary['positions']['GK']['current'] }}/{{ $summary['positions']['GK']['max'] }}</span>
                    </button>
                    
                    <button 
                        wire:click="goToStep(2)"
                        class="flex-1 min-w-[100px] py-3 text-center rounded-lg transition-all {{ $currentStep == 2 ? 'bg-blue-600 text-white shadow-lg' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        <span class="block text-xs">{{ __('Paso 2') }}</span>
                        <span class="block font-semibold">DF</span>
                        <span class="block text-xs">{{ $summary['positions']['DF']['current'] }}/{{ $summary['positions']['DF']['max'] }}</span>
                    </button>
                    
                    <button 
                        wire:click="goToStep(3)"
                        class="flex-1 min-w-[100px] py-3 text-center rounded-lg transition-all {{ $currentStep == 3 ? 'bg-blue-600 text-white shadow-lg' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        <span class="block text-xs">{{ __('Paso 3') }}</span>
                        <span class="block font-semibold">MF</span>
                        <span class="block text-xs">{{ $summary['positions']['MF']['current'] }}/{{ $summary['positions']['MF']['max'] }}</span>
                    </button>
                    
                    <button 
                        wire:click="goToStep(4)"
                        class="flex-1 min-w-[100px] py-3 text-center rounded-lg transition-all {{ $currentStep == 4 ? 'bg-blue-600 text-white shadow-lg' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        <span class="block text-xs">{{ __('Paso 4') }}</span>
                        <span class="block font-semibold">FW</span>
                        <span class="block text-xs">{{ $summary['positions']['FW']['current'] }}/{{ $summary['positions']['FW']['max'] }}</span>
                    </button>
                    
                    <button 
                        wire:click="goToStep(5)"
                        class="flex-1 min-w-[100px] py-3 text-center rounded-lg transition-all {{ $currentStep == 5 ? 'bg-green-600 text-white shadow-lg' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
                        <span class="block text-xs">{{ __('Paso 5') }}</span>
                        <span class="block font-semibold">{{ __('Revisar') }}</span>
                    </button>
                </div>
                
                {{-- Step Content --}}
                <div class="min-h-[500px]">
                    @if($currentStep <= 4)
                        {{-- Pasos de selección de jugadores --}}
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-bold text-white">
                                    @switch($currentStep)
                                        @case(1) {{ __('Selecciona Arqueros (GK)') }} @break
                                        @case(2) {{ __('Selecciona Defensores (DF)') }} @break
                                        @case(3) {{ __('Selecciona Mediocampistas (MF)') }} @break
                                        @case(4) {{ __('Selecciona Delanteros (FW)') }} @break
                                    @endswitch
                                </h3>
                                
                                {{-- Búsqueda --}}
                                <input 
                                    type="text" 
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="{{ __('Buscar jugador...') }}"
                                    class="px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            {{-- Lista de jugadores disponibles --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                @forelse($availablePlayers as $player)
                                    @php
                                        $valuation = $player->valuations->first();
                                        $price = $valuation ? $valuation->market_value : 5.00;
                                    @endphp
                                    
                                    <div class="bg-white/5 border border-white/10 rounded-lg p-4 hover:bg-white/10 transition">
                                        <div class="flex items-center gap-3">
                                            @if($player->photo_url)
                                                <img src="{{ $player->photo_url }}" alt="{{ $player->known_as }}" class="w-12 h-12 rounded-full object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center">
                                                    <span class="text-xl">⚽</span>
                                                </div>
                                            @endif
                                            
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-white">{{ $player->known_as ?? $player->full_name }}</h4>
                                                <p class="text-sm text-gray-400">{{ $player->nationality }}</p>
                                            </div>
                                            
                                            <div class="text-right">
                                                <p class="text-lg font-bold text-green-400">${{ number_format($price, 1) }}</p>
                                                <button 
                                                    wire:click="addPlayer({{ $player->id }})"
                                                    wire:loading.attr="disabled"
                                                    class="mt-1 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition disabled:opacity-50">
                                                    {{ __('Agregar') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-2 text-center py-12 text-gray-400">
                                        {{ __('No hay jugadores disponibles') }}
                                    </div>
                                @endforelse
                            </div>
                            
                            {{-- Paginación --}}
                            <div class="mt-4">
                                {{ $availablePlayers->links() }}
                            </div>
                        </div>
                    @else
                        {{-- Paso 5: Resumen y selección de capitanes --}}
                        <div>
                            <h3 class="text-xl font-bold text-white mb-4">{{ __('Resumen de tu Plantilla') }}</h3>
                            
                            @if($summary['can_complete'])
                                <div class="mb-6 p-4 bg-green-500/20 border border-green-500/50 rounded-lg">
                                    <p class="text-green-200">✅ {{ __('Tu plantilla está completa y lista para activar') }}</p>
                                </div>
                                
                                {{-- Selección de capitanes --}}
                                <div class="mb-6 space-y-4">
                                    <div>
                                        <label class="block text-white font-semibold mb-2">{{ __('Capitán') }}</label>
                                        <select wire:model="captainId" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white">
                                            <option value="">{{ __('Seleccionar capitán') }}</option>
                                            @foreach($selectedPlayers as $player)
                                                <option value="{{ $player->id }}">{{ $player->known_as ?? $player->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-white font-semibold mb-2">{{ __('Vicecapitán') }}</label>
                                        <select wire:model="viceCaptainId" class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white">
                                            <option value="">{{ __('Seleccionar vicecapitán') }}</option>
                                            @foreach($selectedPlayers as $player)
                                                <option value="{{ $player->id }}">{{ $player->known_as ?? $player->full_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <button 
                                    wire:click="completeSquad"
                                    wire:loading.attr="disabled"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition disabled:opacity-50">
                                    {{ __('✅ Completar Armado') }}
                                </button>
                            @else
                                <div class="mb-6 p-4 bg-red-500/20 border border-red-500/50 rounded-lg">
                                    <p class="text-red-200 font-semibold mb-2">❌ {{ __('Aún faltan jugadores') }}</p>
                                    <ul class="text-sm text-red-300 space-y-1">
                                        @foreach($summary['errors'] as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Panel Derecho: Resumen --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            
            {{-- Panel de Presupuesto --}}
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">💰 {{ __('Presupuesto') }}</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">{{ __('Gastado') }}</span>
                        <span class="text-red-400 font-bold">${{ number_format($summary['budget']['spent'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">{{ __('Restante') }}</span>
                        <span class="text-green-400 font-bold">${{ number_format($summary['budget']['remaining'], 2) }}</span>
                    </div>
                    <div class="pt-2 border-t border-white/10">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">{{ __('Usado') }}</span>
                            <span class="text-white">{{ $summary['budget']['percentage_used'] }}%</span>
                        </div>
                        <div class="mt-2 w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full transition-all" style="width: {{ $summary['budget']['percentage_used'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Panel de Límites --}}
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">📊 {{ __('Límites por Posición') }}</h3>
                
                <div class="space-y-4">
                    @foreach($summary['positions'] as $pos => $data)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-semibold {{ $data['meets_minimum'] ? 'text-green-400' : 'text-orange-400' }}">
                                    {{ $pos }}
                                </span>
                                <span class="text-sm text-white">{{ $data['current'] }}/{{ $data['max'] }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all {{ $data['meets_minimum'] ? 'bg-green-500' : 'bg-orange-500' }}" 
                                     style="width: {{ ($data['current'] / $data['max']) * 100 }}%"></div>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">{{ __('Mín: :min', ['min' => $data['min']]) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            
            {{-- Jugadores Seleccionados --}}
            <div class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl p-6">
                <h3 class="text-lg font-bold text-white mb-4">
                    👥 {{ __('Jugadores') }} ({{ $summary['total']['current'] }}/{{ $summary['total']['required'] }})
                </h3>
                
                <div class="space-y-2 max-h-[400px] overflow-y-auto">
                    @forelse($selectedPlayers as $player)
                        <div class="flex items-center justify-between p-2 bg-white/5 rounded-lg">
                            <div class="flex items-center gap-2 flex-1">
                                <span class="text-xs font-bold text-gray-400">
                                    {{ \App\Models\Player::POSITIONS[$player->selected_position] ?? '' }}
                                </span>
                                <span class="text-sm text-white truncate">{{ $player->known_as ?? $player->full_name }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-green-400">${{ number_format($player->selected_price, 1) }}</span>
                                <button 
                                    wire:click="removePlayer({{ $player->id }})"
                                    class="text-red-400 hover:text-red-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-8">{{ __('No hay jugadores seleccionados') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    {{-- Loading Overlay --}}
    <div wire:loading class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white/10 border border-white/20 rounded-2xl p-8 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
            <p class="text-white">{{ __('Procesando...') }}</p>
        </div>
    </div>
</div>
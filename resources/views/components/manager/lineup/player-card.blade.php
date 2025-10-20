@props([
    'roster',           // FantasyRoster con relación player cargada
    'variant' => 'starter', // 'starter' o 'bench'
    'clickable' => true,    // Si es clickable
])

@php
    $player = $roster->player;
    $isStarter = $variant === 'starter';
    
    // Colores según posición (retro gaming palette)
    $positionColors = [
        1 => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-400', 'border' => 'border-yellow-500/50', 'glow' => 'shadow-yellow-500/20'], // GK
        2 => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-400', 'border' => 'border-blue-500/50', 'glow' => 'shadow-blue-500/20'],       // DF
        3 => ['bg' => 'bg-emerald-500/20', 'text' => 'text-emerald-400', 'border' => 'border-emerald-500/50', 'glow' => 'shadow-emerald-500/20'], // MF
        4 => ['bg' => 'bg-red-500/20', 'text' => 'text-red-400', 'border' => 'border-red-500/50', 'glow' => 'shadow-red-500/20'],           // FW
    ];
    
    $colors = $positionColors[$player->position] ?? $positionColors[4];
    
    // Clases base según variante
    if ($isStarter) {
        $cardClasses = 'bg-slate-800/80 border-2 ' . $colors['border'] . ' rounded-lg p-3 transition-all duration-200 group';
        $hoverClasses = 'hover:' . $colors['border'] . ' hover:shadow-lg hover:' . $colors['glow'] . ' hover:-translate-y-1';
    } else {
        $cardClasses = 'bg-slate-700/50 border border-slate-600 rounded-lg p-2.5 transition-all duration-200 group';
        $hoverClasses = 'hover:border-gray-400 hover:shadow-lg hover:shadow-gray-500/10 hover:-translate-y-0.5';
    }
    
    $cursorClass = $clickable ? 'cursor-pointer' : 'cursor-default';
@endphp

<div {{ $attributes->merge(['class' => "$cardClasses $hoverClasses $cursorClass"]) }}>
    
    {{-- Foto del jugador (placeholder retro) --}}
    @if($isStarter)
        <div class="relative mb-2 aspect-square rounded-lg overflow-hidden bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center border {{ $colors['border'] }}">
            @if($player->photo_url)
                <img src="{{ $player->photo_url }}" alt="{{ $player->display_name }}" class="w-full h-full object-cover">
            @else
                {{-- Placeholder retro con iniciales --}}
                <div class="text-3xl font-black {{ $colors['text'] }} opacity-50 font-mono">
                    {{ strtoupper(substr($player->known_as ?? $player->full_name, 0, 2)) }}
                </div>
            @endif
            
            {{-- Badge de posición (overlay) --}}
            <div class="absolute top-1 right-1 px-2 py-0.5 rounded {{ $colors['bg'] }} backdrop-blur-sm border {{ $colors['border'] }}">
                <span class="text-xs font-black {{ $colors['text'] }} tracking-wider">
                    {{ $player->position_name }}
                </span>
            </div>

            {{-- Indicadores de estado (overlay bottom) --}}
            <div class="absolute bottom-0 left-0 right-0 flex items-center justify-center space-x-1 p-1 bg-black/50 backdrop-blur-sm">
                {{-- TODO: Agregar badges de lesión, suspensión, sin partido --}}
                {{-- Placeholder: --}}
                {{-- @if($player->is_injured)
                    <span class="text-xs px-1.5 py-0.5 bg-red-500/80 text-white rounded font-bold">!</span>
                @endif --}}
            </div>
        </div>
    @endif

    {{-- Badge de posición (para banco compacto) --}}
    @if(!$isStarter)
        <div class="text-xs font-bold text-center mb-1.5 px-1.5 py-0.5 rounded {{ $colors['bg'] }} {{ $colors['text'] }} border {{ $colors['border'] }}">
            {{ $player->position_name }}
        </div>
    @endif

    {{-- Nombre del jugador --}}
    <div class="text-center {{ $isStarter ? 'mb-1.5' : '' }}">
        <div class="{{ $isStarter ? 'text-xs' : 'text-xs' }} font-bold text-white truncate group-hover:text-cyan-300 transition-colors">
            {{ $player->known_as ?? $player->full_name }}
        </div>
        
        {{-- Número de camiseta (opcional, si existe) --}}
        {{-- @if($player->shirt_number)
            <div class="text-xs text-gray-500 font-mono">
                #{{ $player->shirt_number }}
            </div>
        @endif --}}
    </div>

    {{-- Estadísticas rápidas (solo para titulares) --}}
    @if($isStarter)
        <div class="flex items-center justify-center space-x-2 text-xs text-gray-400 mb-2">
            {{-- Puntos última GW --}}
            {{-- <div class="flex items-center space-x-1">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="font-mono">{{ $player->last_gw_points ?? 0 }}</span>
            </div> --}}
            
            {{-- Placeholder para stats --}}
            <span class="font-mono text-gray-500">···</span>
        </div>
    @endif

    {{-- Badge de capitanía --}}
    @if($roster->captaincy === 1)
        <div class="mt-2 mx-auto w-7 h-7 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center shadow-lg shadow-yellow-500/50 border-2 border-yellow-300 group-hover:scale-110 transition-transform">
            <span class="text-xs font-black text-slate-900">C</span>
        </div>
    @elseif($roster->captaincy === 2)
        <div class="mt-2 mx-auto w-7 h-7 bg-gradient-to-br from-gray-300 to-gray-500 rounded-full flex items-center justify-center shadow-lg shadow-gray-500/50 border-2 border-gray-200 group-hover:scale-110 transition-transform">
            <span class="text-xs font-black text-slate-900">V</span>
        </div>
    @endif

    {{-- Slot (número de posición en el roster) - Solo visible en hover --}}
    <div class="absolute top-1 left-1 opacity-0 group-hover:opacity-100 transition-opacity">
        <div class="w-5 h-5 bg-black/70 rounded flex items-center justify-center">
            <span class="text-xs font-mono text-gray-400">{{ $roster->slot }}</span>
        </div>
    </div>

    {{-- Efecto pixelado retro en hover --}}
    <div class="absolute inset-0 pointer-events-none opacity-0 group-hover:opacity-10 transition-opacity rounded-lg" 
         style="background-image: repeating-linear-gradient(0deg, transparent, transparent 1px, rgba(255,255,255,0.1) 1px, rgba(255,255,255,0.1) 2px);"></div>
</div>
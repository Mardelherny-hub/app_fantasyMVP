<div>
<div class="space-y-6">
    {{-- Header con búsqueda --}}
    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-white">{{ __('Ligas Públicas Disponibles') }}</h2>
                <p class="text-gray-400 text-sm mt-1">{{ __('Selecciona una liga para unirte') }}</p>
            </div>
            
            <div class="relative w-full md:w-80">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('Buscar liga...') }}"
                    class="w-full bg-white/5 border border-white/20 rounded-lg px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20"
                >
                <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Alertas --}}
    @if (session()->has('success'))
        <div class="bg-emerald-500/20 border border-emerald-500/50 rounded-lg p-4 flex items-start">
            <svg class="w-5 h-5 text-emerald-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-emerald-300">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 flex items-start">
            <svg class="w-5 h-5 text-red-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-red-300">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Grid de ligas --}}
    @if($leagues->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($leagues as $league)
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6 hover:border-emerald-500/30 transition-all duration-300 group">
                    {{-- Header --}}
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-white mb-1 group-hover:text-emerald-400 transition">
                                {{ $league->name }}
                            </h3>
                            <p class="text-xs text-gray-400">
                                {{ __('Código:') }} <span class="text-emerald-400 font-mono">{{ $league->code }}</span>
                            </p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Info --}}
                    <div class="space-y-2 mb-4 text-sm">
                        <div class="flex items-center text-gray-400">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ __('Owner:') }} {{ $league->owner->name ?? __('N/A') }}</span>
                        </div>
                        
                        <div class="flex items-center text-gray-400">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ __('Temporada:') }} {{ $league->season->name ?? __('N/A') }}</span>
                        </div>

                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-gray-400">
                                {{ __('Cupos:') }} 
                                <span class="font-semibold" :class="{'text-emerald-400': {{ $league->getRemainingSpots() }} > 3, 'text-yellow-400': {{ $league->getRemainingSpots() }} <= 3 && {{ $league->getRemainingSpots() }} > 0, 'text-red-400': {{ $league->getRemainingSpots() }} === 0}">
                                    {{ $league->fantasy_teams_count }}/{{ $league->max_participants }}
                                </span>
                                @if($league->getRemainingSpots() > 0)
                                    <span class="text-xs text-gray-500">({{ $league->getRemainingSpots() }} {{ __('disponibles') }})</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Botón --}}
                    @php
                        $isMember = auth()->user()->leagues()->where('leagues.id', $league->id)->exists();
                        $isFull = $league->isFull();
                    @endphp

                    <button 
                        wire:click="joinLeague({{ $league->id }})"
                        @if($isMember || $isFull) disabled @endif
                        class="w-full py-2.5 rounded-lg font-semibold transition-all duration-300 
                            @if($isMember)
                                bg-blue-600 text-white cursor-default
                            @elseif($isFull)
                                bg-gray-700 text-gray-500 cursor-not-allowed
                            @else
                                bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 hover:shadow-lg hover:shadow-emerald-500/30 hover:scale-105
                            @endif
                        "
                    >
                        @if($isMember)
                            {{ __('✓ Ya eres miembro') }}
                        @elseif($isFull)
                            {{ __('Liga Llena') }}
                        @else
                            {{ __('Unirse a Liga') }}
                        @endif
                    </button>
                </div>
            @endforeach
        </div>

        {{-- Paginación --}}
        <div class="mt-6">
            {{ $leagues->links() }}
        </div>
    @else
        {{-- Estado vacío --}}
        <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-12 text-center">
            <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-white mb-2">{{ __('No hay ligas disponibles') }}</h3>
            <p class="text-gray-400 mb-6">
                @if($search)
                    {{ __('No se encontraron ligas con el término de búsqueda') }} "{{ $search }}"
                @else
                    {{ __('Actualmente no hay ligas públicas disponibles') }}
                @endif
            </p>
            <a href="{{ route('manager.onboarding.create-private', ['locale' => app()->getLocale()]) }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 font-bold rounded-lg hover:shadow-lg transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('Crear tu propia liga') }}
            </a>
        </div>
    @endif

    {{-- Botón volver --}}
    <div class="text-center">
        <a href="{{ route('manager.onboarding.welcome', ['locale' => app()->getLocale()]) }}" 
           class="inline-flex items-center text-gray-400 hover:text-emerald-400 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Volver al inicio') }}
        </a>
    </div>
</div>
</div>

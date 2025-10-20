<div class="min-h-screen bg-slate-900 text-white py-8 px-4">
    {{-- Header --}}
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-black text-white">{{ __('Dashboard') }}</h1>
                <p class="text-gray-400 mt-1">{{ __('Bienvenido, :name', ['name' => auth()->user()->name]) }}</p>
            </div>
            
            {{-- Quick Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-2 bg-white/5 border border-white/20 text-white rounded-lg hover:bg-white/10 transition text-sm">
                    {{ __('Unirse a otra liga') }}
                </a>
            </div>
        </div>

        {{-- Selector de Liga (si tiene múltiples) --}}
        @if($leagueMembers->count() > 1)
        <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-4 mb-6">
            <label class="block text-sm font-medium text-gray-400 mb-2">{{ __('Seleccionar liga') }}</label>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                @foreach($leagueMembers as $member)
                <button 
                    wire:click="selectLeague({{ $member->league_id }})"
                    class="p-4 rounded-lg border-2 transition {{ $selectedLeagueId === $member->league_id 
                        ? 'bg-cyan-500/20 border-cyan-500' 
                        : 'bg-white/5 border-white/10 hover:border-white/30' }}">
                    <div class="font-semibold text-white">{{ $member->league->name }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $member->league->season->name }}</div>
                </button>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($selectedMember)
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Alerta de Deadline --}}
        @if($hasIncompleteSquad && $hasDeadline)
            <x-manager.deadline-alert :leagueMember="$selectedMember" />
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Puntos Totales --}}
            <div class="bg-gradient-to-br from-cyan-500/10 to-blue-500/10 border border-cyan-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400">{{ __('Puntos Totales') }}</span>
                    <svg class="w-8 h-8 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-black text-white">
                    {{ $selectedTeam ? number_format($selectedTeam->total_points) : 0 }}
                </div>
            </div>

            {{-- Presupuesto --}}
            <div class="bg-gradient-to-br from-green-500/10 to-emerald-500/10 border border-green-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400">{{ __('Presupuesto') }}</span>
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-black text-white">
                    ${{ $selectedTeam ? number_format($selectedTeam->budget, 2) : '100.00' }}
                </div>
            </div>

            {{-- Posición --}}
            <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border border-yellow-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400">{{ __('Posición') }}</span>
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-black text-white">
                    @php
                        $position = $standings->where('fantasy_team_id', $selectedTeam?->id)->first()?->position ?? '-';
                    @endphp
                    {{ $position }}°
                </div>
            </div>

            {{-- Gameweek Actual --}}
            <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400">{{ __('Gameweek') }}</span>
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-black text-white">
                    GW{{ $currentGameweek?->number ?? 1 }}
                </div>
            </div>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- Left Column: Team Info --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Team Card --}}
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">{{ __('Mi Equipo') }}</h2>
                        @if($selectedTeam && $selectedTeam->is_squad_complete)
                        <span class="px-3 py-1 bg-green-500/20 border border-green-500/50 text-green-400 text-xs font-semibold rounded-full">
                            {{ __('Plantilla Completa') }}
                        </span>
                        @else
                        <span class="px-3 py-1 bg-yellow-500/20 border border-yellow-500/50 text-yellow-400 text-xs font-semibold rounded-full">
                            {{ __('Plantilla Incompleta') }}
                        </span>
                        @endif
                    </div>

                    @if($selectedTeam)
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-400 text-sm">{{ __('Nombre del equipo') }}</span>
                            <p class="text-white font-semibold text-lg">{{ $selectedTeam->name }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 pt-3 border-t border-white/10">
                            <div>
                                <span class="text-gray-400 text-sm">{{ __('Liga') }}</span>
                                <p class="text-white font-semibold">{{ $selectedMember->league->name }}</p>
                            </div>
                            <div>
                                <span class="text-gray-400 text-sm">{{ __('Temporada') }}</span>
                                <p class="text-white font-semibold">{{ $selectedMember->league->season->name }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3 mt-6">
                        @if(!$selectedTeam->is_squad_complete)
                        <a href="{{ route('manager.squad-builder.index', ['locale' => app()->getLocale()]) }}" 
                           class="flex-1 px-4 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-lg hover:shadow-lg hover:shadow-cyan-500/30 transition text-center">
                            {{ __('Armar Plantilla') }}
                        </a>
                        @else
                        <a href="{{ route('manager.lineup.index', ['locale' => app()->getLocale()]) }}" 
   class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 border border-emerald-500/30 rounded-xl hover:border-emerald-400 transition group">
    <div class="flex items-center space-x-3">
        <div class="w-12 h-12 bg-emerald-500/20 rounded-lg flex items-center justify-center group-hover:bg-emerald-500/30 transition">
            <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <h3 class="font-bold text-white">{{ __('Mi Alineación') }}</h3>
            <p class="text-xs text-gray-400">{{ __('Gestionar titulares y suplentes') }}</p>
        </div>
    </div>
    <svg class="w-5 h-5 text-emerald-400 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
</a>

                        <button class="flex-1 px-4 py-3 bg-white/5 border border-white/20 text-white font-semibold rounded-lg hover:bg-white/10 transition">
                            {{ __('Transferencias') }}
                        </button>
                        @endif
                    </div>
                    @else
                    <p class="text-gray-400 text-center py-8">{{ __('No se encontró equipo para esta liga') }}</p>
                    @endif
                </div>

                {{-- Gameweek Info --}}
                @if($currentGameweek)
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">{{ __('Gameweek :number', ['number' => $currentGameweek->number]) }}</h2>
                    
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                            <span class="text-gray-400">{{ __('Inicio') }}</span>
                            <span class="text-white font-semibold">{{ $currentGameweek->starts_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                            <span class="text-gray-400">{{ __('Fin') }}</span>
                            <span class="text-white font-semibold">{{ $currentGameweek->ends_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                            <span class="text-gray-400">{{ __('Estado') }}</span>
                            @if($currentGameweek->is_active)
                            <span class="px-3 py-1 bg-green-500/20 border border-green-500/50 text-green-400 text-xs font-semibold rounded-full">
                                {{ __('En Curso') }}
                            </span>
                            @else
                            <span class="px-3 py-1 bg-gray-500/20 border border-gray-500/50 text-gray-400 text-xs font-semibold rounded-full">
                                {{ __('Próxima') }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Right Column: Standings --}}
            <div class="space-y-6">
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">{{ __('Tabla de Posiciones') }}</h2>
                    
                    @if($standings->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($standings as $standing)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $standing->fantasy_team_id === $selectedTeam?->id ? 'bg-cyan-500/20 border-2 border-cyan-500' : 'bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full font-bold {{ $standing->position <= 3 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-white/10 text-gray-400' }}">
                                    {{ $standing->position }}
                                </span>
                                <div>
                                    <p class="font-semibold text-white text-sm">{{ $standing->fantasyTeam->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $standing->played }}J | {{ $standing->points_league }}pts</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-white">{{ number_format($standing->points_fantasy) }}</p>
                                <p class="text-xs text-gray-400">{{ __('pts') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <button class="w-full mt-4 px-4 py-2 bg-white/5 border border-white/20 text-white text-sm font-semibold rounded-lg hover:bg-white/10 transition">
                        {{ __('Ver tabla completa') }}
                    </button>
                    @else
                    <p class="text-gray-400 text-center py-8 text-sm">{{ __('Aún no hay clasificación disponible') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="max-w-7xl mx-auto">
        <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h3 class="text-xl font-bold text-white mb-2">{{ __('No tienes ligas activas') }}</h3>
            <p class="text-gray-400 mb-6">{{ __('Únete o crea una liga para comenzar a competir') }}</p>
            <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-lg hover:shadow-lg hover:shadow-cyan-500/30 transition">
                {{ __('Explorar ligas') }}
            </a>
        </div>
    </div>
    @endif
</div>
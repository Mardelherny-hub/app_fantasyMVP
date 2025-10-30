<div class="min-h-screen bg-slate-900 text-white py-8 px-4">
    
    {{-- ========================================
         HEADER SECTION
         ======================================== --}}
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-black text-white">{{ __('Dashboard') }}</h1>
                <p class="text-gray-400 mt-1">{{ __('Bienvenido, :name', ['name' => auth()->user()->name]) }}</p>
            </div>
            
            {{-- Quick Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('manager.education.index', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-2 bg-emerald-500/20 border border-emerald-500/50 text-emerald-400 rounded-lg hover:bg-emerald-500/30 transition text-sm font-semibold">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    {{ __('Education') }}
                </a>
                <a href="{{ route('manager.onboarding.welcome', ['locale' => app()->getLocale()]) }}" 
                   class="px-4 py-2 bg-white/5 border border-white/20 text-white rounded-lg hover:bg-white/10 transition text-sm">
                    {{ __('Unirse a otra liga') }}
                </a>
            </div>
        </div>

        {{-- League Selector (si tiene múltiples) --}}
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

    {{-- ========================================
         MAIN CONTENT (SI HAY LIGA SELECCIONADA)
         ======================================== --}}
    @if($selectedMember)
    <div class="max-w-7xl mx-auto space-y-6">
        
        {{-- Alerta de Deadline --}}
        @if($hasIncompleteSquad && $hasDeadline)
            <x-manager.deadline-alert :leagueMember="$selectedMember" />
        @endif

        {{-- ========================================
             STATS CARDS (4 columnas)
             ======================================== --}}
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

            {{-- Posición en Liga --}}
            <div class="bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400">{{ __('Posición') }}</span>
                    <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="text-3xl font-black text-white">
                    #{{ $userPosition ?? '-' }}
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
                    ${{ $selectedTeam ? number_format($selectedTeam->budget, 2) : '0.00' }}
                </div>
            </div>

            {{-- Valor del Equipo --}}
            <div class="bg-gradient-to-br from-yellow-500/10 to-orange-500/10 border border-yellow-500/30 rounded-xl p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-400">{{ __('Valor del Equipo') }}</span>
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div class="text-3xl font-black text-white">
                    ${{ $selectedTeam ? number_format($selectedTeam->team_value, 2) : '0.00' }}
                </div>
            </div>

        </div>

        {{-- ========================================
             TWO COLUMN LAYOUT
             ======================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- ========================================
                 LEFT COLUMN (2/3)
                 ======================================== --}}
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
                        
                        <div class="pt-3">
                            <a href="{{ route('manager.lineup.index', ['locale' => app()->getLocale()]) }}" 
                               class="block w-full text-center px-4 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white font-bold rounded-lg hover:shadow-lg hover:shadow-cyan-500/30 transition">
                                {{ __('Ver Alineación') }}
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Gameweek Info --}}
                @if($currentGameweek)
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">{{ __('Jornada Actual') }}</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                            <span class="text-gray-400">{{ __('Jornada') }}</span>
                            <span class="text-white font-semibold">{{ $currentGameweek->name }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                            <span class="text-gray-400">{{ __('Inicio') }}</span>
                            <span class="text-white font-semibold">{{ $currentGameweek->starts_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-white/5 rounded-lg">
                            <span class="text-gray-400">{{ __('Fin') }}</span>
                            <span class="text-white font-semibold">{{ $currentGameweek->ends_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
                @endif

            </div>

            {{-- ========================================
                 RIGHT COLUMN (1/3)
                 ======================================== --}}
            <div class="space-y-6">
                
                {{-- Education Card (NUEVA) --}}
                <div class="bg-gradient-to-br from-emerald-500/10 to-teal-500/10 backdrop-blur-lg border border-emerald-500/30 rounded-xl p-6 hover:border-emerald-500/50 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-emerald-500/20 rounded-lg mr-3">
                                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-white">{{ __('Education') }}</h3>
                                <p class="text-xs text-gray-400">{{ __('Learn & Earn') }}</p>
                            </div>
                        </div>
                        <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-bold rounded-full uppercase">{{ __('New') }}</span>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-xs text-gray-300">
                            <svg class="w-3 h-3 text-emerald-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Quick quizzes & trivia') }}</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-300">
                            <svg class="w-3 h-3 text-emerald-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Earn coins & rewards') }}</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-300">
                            <svg class="w-3 h-3 text-emerald-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ __('Compete in rankings') }}</span>
                        </div>
                    </div>

                    <a href="{{ route('manager.education.index', ['locale' => app()->getLocale()]) }}" 
                       class="block w-full px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm font-bold rounded-lg text-center hover:shadow-lg hover:shadow-emerald-500/30 transition-all duration-300">
                        {{ __('Start Now') }}
                        <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                </div>

                {{-- Standings --}}
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">{{ __('Tabla de Posiciones') }}</h2>
                    
                    @if($standings->isNotEmpty())
                    <div class="space-y-2">
                        @foreach($standings->take(5) as $standing)
                        <div class="flex items-center justify-between p-3 rounded-lg {{ $standing->fantasy_team_id === $selectedTeam?->id ? 'bg-cyan-500/20 border-2 border-cyan-500' : 'bg-white/5' }}">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 flex items-center justify-center rounded-full font-bold {{ $standing->position <= 3 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-white/10 text-gray-400' }}">
                                    {{ $standing->position }}
                                </span>
                                <div>
                                    <p class="text-white font-semibold text-sm">{{ $standing->fantasyTeam->name }}</p>
                                    <p class="text-gray-400 text-xs">{{ $standing->points }} pts</p>
                                </div>
                            </div>
                            <span class="text-emerald-400 font-bold">{{ $standing->fantasy_points }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-400 text-sm text-center py-4">{{ __('No hay datos disponibles') }}</p>
                    @endif
                </div>

            </div>

        </div>

    </div>
    @endif

</div>
<div class="min-h-screen bg-slate-900 text-white py-8 px-4">
    
    {{-- ========================================
         HEADER SECTION
         ======================================== --}}
    <div class="max-w-7xl mx-auto mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-black text-white">{{ __('Panel') }}</h1>
                <p class="text-gray-400 mt-1">{{ __('Bienvenido, :name', ['name' => auth()->user()->name]) }}</p>
            </div>
            
            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-3">
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
                        ? 'border-emerald-500 bg-emerald-500/10' 
                        : 'border-white/10 bg-white/5 hover:border-white/20' }}">
                    <div class="font-semibold text-white">{{ $member->league->name }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $member->league->season->name ?? 'N/A' }}</div>
                </button>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- ========================================
         ALERT: SQUAD BUILDER
         ======================================== --}}
    @if($selectedLeague && !$hasSquad)
    <div class="max-w-7xl mx-auto mb-6">
        <div class="bg-gradient-to-r from-yellow-900/50 to-orange-900/50 border-l-4 border-yellow-500 rounded-lg p-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-yellow-400 mb-2">{{ __('¡Debes armar tu plantilla!') }}</h3>
                    <p class="text-gray-300 mb-4">
                        {{ __('Tienes :hours horas para completar tu equipo de :count jugadores en la liga :league.', [
                            'hours' => $deadlineHours ?? '59.6',
                            'count' => 23,
                            'league' => $selectedLeague->name ?? 'Test'
                        ]) }}
                    </p>
                    <p class="text-sm text-gray-400 mb-4">
                        {{ __('Deadline: :date', ['date' => '06/11/2025 22:38']) }}
                    </p>
                    <a href="{{ route('manager.squad-builder.index', ['locale' => app()->getLocale()]) }}" 
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-slate-900 font-bold rounded-lg hover:shadow-lg hover:shadow-yellow-500/30 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('Armar mi plantilla ahora') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ========================================
         MAIN CONTENT - 2 COLUMNAS
         ======================================== --}}
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- COLUMNA IZQUIERDA (2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Stats Cards Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                    {{-- Puntos Totales --}}
                    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-400 uppercase">{{ __('Puntos Totales') }}</span>
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-white">{{ $stats['total_points'] ?? 0 }}</div>
                    </div>

                    {{-- Posición --}}
                    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-400 uppercase">{{ __('Posición') }}</span>
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-white">#{{ $stats['position'] ?? '-' }}</div>
                    </div>

                    {{-- Presupuesto --}}
                    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-400 uppercase">{{ __('Presupuesto') }}</span>
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-white">${{ number_format($stats['budget'] ?? 100, 2) }}</div>
                    </div>

                    {{-- Valor del Equipo --}}
                    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-4 hover:bg-white/10 transition">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-400 uppercase">{{ __('Valor del Equipo') }}</span>
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <div class="text-3xl font-black text-white">${{ number_format($stats['team_value'] ?? 0, 2) }}</div>
                    </div>
                </div>

                {{-- Mi Equipo Card --}}
                @if($fantasyTeam)
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-white">{{ __('Mi Equipo') }}</h2>
                        @if(!$hasSquad)
                        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-400 text-xs font-bold rounded-full uppercase">
                            {{ __('Plantilla Incompleta') }}
                        </span>
                        @endif
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-white/10">
                            <span class="text-sm text-gray-400">{{ __('Nombre del equipo') }}</span>
                            <span class="font-semibold text-white">{{ $fantasyTeam->name }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-white/10">
                            <span class="text-sm text-gray-400">{{ __('Liga') }}</span>
                            <span class="font-semibold text-white">{{ $selectedLeague->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-400">{{ __('Temporada') }}</span>
                            <span class="font-semibold text-white">{{ $selectedLeague->season->name ?? '2025' }}</span>
                        </div>
                    </div>

                    <a href="{{ route('manager.lineup.index', ['locale' => app()->getLocale()]) }}" 
                       class="mt-6 w-full block text-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold rounded-lg hover:shadow-lg hover:shadow-blue-500/30 transition">
                        {{ __('Ver Alineación') }}
                    </a>
                </div>
                @endif

                {{-- Tabla de Posiciones --}}
                <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-xl p-6">
                    <h2 class="text-xl font-bold text-white mb-4">{{ __('Tabla de Posiciones') }}</h2>
                    
                    @if($standings && $standings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-white/10">
                                    <th class="text-left py-2 text-gray-400 font-medium">#</th>
                                    <th class="text-left py-2 text-gray-400 font-medium">{{ __('Equipo') }}</th>
                                    <th class="text-center py-2 text-gray-400 font-medium">{{ __('PJ') }}</th>
                                    <th class="text-center py-2 text-gray-400 font-medium">{{ __('Pts') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($standings as $standing)
                                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                    <td class="py-3 text-gray-300">{{ $standing->position }}</td>
                                    <td class="py-3 font-medium text-white">{{ $standing->fantasyTeam->name }}</td>
                                    <td class="py-3 text-center text-gray-300">{{ $standing->played }}</td>
                                    <td class="py-3 text-center font-bold text-emerald-400">{{ $standing->points }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-8 text-gray-500">
                        <p>{{ __('No hay datos disponibles') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- COLUMNA DERECHA (1/3) - EDUCATION HUB --}}
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-xl p-6 sticky top-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-white/20 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white">{{ __('Education Hub') }}</h3>
                                <p class="text-emerald-100 text-xs">🎯 {{ __('Test your knowledge & earn coins!') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('manager.education.index', ['locale' => app()->getLocale()]) }}" 
                           class="bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-lg text-white text-xs font-semibold">
                            {{ __('Play Quiz') }} 🎮
                        </a>
                    </div>

                    {{-- Stats Compactos --}}
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        {{-- Balance --}}
                        <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                            <div class="flex items-center gap-1 mb-1">
                                <svg class="w-4 h-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582z"/>
                                </svg>
                                <span class="text-emerald-100 text-[10px] font-medium uppercase">{{ __('Balance') }}</span>
                            </div>
                            <div class="text-2xl font-black text-white">{{ number_format($educationStats['current_balance'] ?? 0, 1) }}</div>
                            <div class="text-yellow-200 text-xs">CAN</div>
                        </div>

                        {{-- Earned --}}
                        <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-emerald-100 text-[10px] font-medium uppercase">{{ __('Earned') }}</span>
                            </div>
                            <div class="text-2xl font-black text-yellow-300">+{{ number_format($educationStats['total_coins_earned'] ?? 0, 1) }}</div>
                            <div class="text-emerald-100 text-xs">{{ __('Total') }}</div>
                        </div>

                        {{-- Quizzes --}}
                        <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-emerald-100 text-[10px] font-medium uppercase">{{ __('Quizzes') }}</span>
                            </div>
                            <div class="text-2xl font-black text-white">{{ $educationStats['total_quizzes'] ?? 0 }}</div>
                            <div class="text-emerald-100 text-xs">{{ __('Done') }}</div>
                        </div>

                        {{-- Points --}}
                        <div class="bg-white/15 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                            <div class="flex items-center gap-1 mb-1">
                                <span class="text-emerald-100 text-[10px] font-medium uppercase">{{ __('Points') }}</span>
                            </div>
                            <div class="text-2xl font-black text-white">{{ number_format($educationStats['total_points_earned'] ?? 0) }}</div>
                            <div class="text-emerald-100 text-xs">{{ __('Total') }}</div>
                        </div>
                    </div>

                    {{-- Features --}}
                    <div class="space-y-2 mb-4">
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-200 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ __('Quick Quizzes') }}</p>
                                <p class="text-emerald-100 text-xs">{{ __('10 questions, 30 sec each') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-yellow-200 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582z"/>
                            </svg>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ __('Earn Rewards') }}</p>
                                <p class="text-emerald-100 text-xs">{{ __('Points to CAN coins') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-blue-200 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <div>
                                <p class="text-white font-semibold text-sm">{{ __('Global Ranking') }}</p>
                                <p class="text-emerald-100 text-xs">{{ __('Compete worldwide') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- CTA --}}
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                        <p class="text-white text-sm font-semibold mb-3">
                            💡 {{ __('Ready to test your knowledge?') }}
                        </p>
                        <a href="{{ route('manager.education.index', ['locale' => app()->getLocale()]) }}" 
                           class="w-full block text-center px-4 py-3 bg-white text-emerald-600 rounded-lg font-bold hover:bg-emerald-50 transition shadow-xl">
                            {{ __('Start Quiz') }} →
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
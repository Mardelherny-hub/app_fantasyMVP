<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        
        {{-- Header con Deadline Alert --}}
        <div class="bg-slate-800/50 border-b border-slate-700/50 backdrop-blur-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-white">
                            {{ __('Arma tu Plantilla') }}
                        </h1>
                        <p class="text-sm text-gray-400 mt-1">
                            {{ __('Selecciona 23 jugadores dentro de tu presupuesto') }}
                        </p>
                    </div>

                    {{-- Deadline Countdown --}}
                    @php
                        $member = auth()->user()->leagueMembers()
                            ->whereNotNull('squad_deadline_at')
                            ->where('squad_deadline_at', '>', now())
                            ->first();
                        
                        $fantasyTeam = auth()->user()->fantasyTeams()
                            ->where('is_squad_complete', false)
                            ->first();
                    @endphp

                    @if($member && $member->squad_deadline_at && $fantasyTeam && !$fantasyTeam->is_squad_complete)
                        @php
                            $deadline = $member->squad_deadline_at;
                            $hoursRemaining = now()->diffInHours($deadline, false);
                            $minutesRemaining = now()->diffInMinutes($deadline, false) % 60;
                            
                            // Determinar color según tiempo restante
                            if ($hoursRemaining <= 6) {
                                $colorClass = 'bg-red-500/20 border-red-500/30 text-red-400';
                                $iconColor = 'text-red-400';
                            } elseif ($hoursRemaining <= 24) {
                                $colorClass = 'bg-yellow-500/20 border-yellow-500/30 text-yellow-400';
                                $iconColor = 'text-yellow-400';
                            } else {
                                $colorClass = 'bg-blue-500/20 border-blue-500/30 text-blue-400';
                                $iconColor = 'text-blue-400';
                            }
                        @endphp

                        <div class="flex items-center gap-3 {{ $colorClass }} border px-4 py-2.5 rounded-lg backdrop-blur-sm">
                            <div class="relative">
                                <svg class="w-5 h-5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                @if($hoursRemaining <= 6)
                                    <span class="absolute -top-1 -right-1 flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm font-semibold">
                                @if($hoursRemaining > 0)
                                    {{ __('Tiempo restante:') }} 
                                    <span class="font-bold">{{ $hoursRemaining }}h {{ $minutesRemaining }}m</span>
                                @else
                                    <span class="animate-pulse">{{ __('¡Deadline vencido!') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Warning Banner si el deadline está muy cerca --}}
        @if($member && $member->squad_deadline_at && $fantasyTeam && !$fantasyTeam->is_squad_complete)
            @php
                $hoursRemaining = now()->diffInHours($member->squad_deadline_at, false);
            @endphp
            
            @if($hoursRemaining <= 6 && $hoursRemaining > 0)
                <div class="bg-red-500/10 border-y border-red-500/30 backdrop-blur-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-400 flex-shrink-0 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-red-400 font-medium">
                               ⚠️ {{ __('Quedan menos de 6 horas para completar tu plantilla. Si no la completas, se asignará automáticamente.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($hoursRemaining <= 0)
                <div class="bg-red-500/10 border-y border-red-500/30 backdrop-blur-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <p class="text-sm text-red-400 font-medium">
                                {{ __('El deadline ha vencido. Tu plantilla será asignada automáticamente en las próximas horas.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Componente Livewire del Wizard --}}
        <div class="py-8">
            <livewire:manager.squad-builder.squad-builder-wizard />
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-refresh cada minuto para actualizar el countdown
        setInterval(function() {
            // Solo refresh si hay deadline activo
            const deadlineElement = document.querySelector('[class*="Tiempo restante"]');
            if (deadlineElement) {
                window.location.reload();
            } r
        }, 60000); // 60 segundos
    </script>
    @endpush
</x-app-layout>
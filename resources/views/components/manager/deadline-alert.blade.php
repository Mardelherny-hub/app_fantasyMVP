@props(['leagueMember'])

@php
    $deadline = $leagueMember->squad_deadline_at;
    $hoursRemaining = $deadline ? now()->diffInHours($deadline, false) : null;
    $isExpired = $hoursRemaining !== null && $hoursRemaining < 0;
    $isUrgent = $hoursRemaining !== null && $hoursRemaining <= 24 && $hoursRemaining > 0;
@endphp

@if($deadline && !$isExpired)
<div class="bg-gradient-to-r from-yellow-500/10 to-orange-500/10 border-l-4 border-yellow-500 rounded-lg p-6 mb-6">
    <div class="flex items-start">
        {{-- Icon --}}
        <div class="flex-shrink-0">
            <svg class="w-8 h-8 {{ $isUrgent ? 'text-red-500' : 'text-yellow-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>

        {{-- Content --}}
        <div class="ml-4 flex-1">
            <h3 class="text-lg font-bold {{ $isUrgent ? 'text-red-400' : 'text-yellow-400' }}">
                {{ __('¡Debes armar tu plantilla!') }}
            </h3>
            <p class="mt-2 text-gray-300">
                {{ __('Tienes') }} 
                <span class="font-bold {{ $isUrgent ? 'text-red-400' : 'text-yellow-400' }}">
                    {{ abs($hoursRemaining) }} {{ __('horas') }}
                </span> 
                {{ __('para completar tu equipo de 23 jugadores en la liga') }} 
                <span class="font-semibold text-white">{{ $leagueMember->league->name }}</span>.
            </p>
            <p class="mt-1 text-sm text-gray-400">
                {{ __('Deadline:') }} {{ \Carbon\Carbon::parse($deadline)->format('d/m/Y H:i') }}
            </p>

            {{-- CTA Button --}}
            <div class="mt-4">
                <a href="{{ route('manager.squad-builder.index', ['locale' => app()->getLocale()]) }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-slate-900 font-bold rounded-lg hover:shadow-lg hover:shadow-yellow-500/30 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('Armar mi plantilla ahora') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endif
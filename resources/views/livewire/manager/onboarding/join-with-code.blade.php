<div>
    <div class="max-w-2xl mx-auto">
    {{-- Card principal --}}
    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-8">
        {{-- Icon --}}
        <div class="w-16 h-16 bg-teal-500/20 rounded-xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
            </svg>
        </div>

        {{-- Título --}}
        <h2 class="text-2xl font-bold text-center text-white mb-2">
            {{ __('Unirse con Código') }}
        </h2>
        <p class="text-gray-400 text-center mb-8">
            {{ __('Ingresa el código de 6 caracteres de la liga privada') }}
        </p>

        {{-- Alertas --}}
        @if (session()->has('error'))
            <div class="bg-red-500/20 border border-red-500/50 rounded-lg p-4 flex items-start mb-6">
                <svg class="w-5 h-5 text-red-400 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="text-red-300">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Formulario --}}
        <form wire:submit.prevent="joinWithCode" class="space-y-6">
            {{-- Input Código --}}
            <div>
                <label for="code" class="block text-sm font-semibold text-gray-300 mb-2">
                    {{ __('Código de Liga') }}
                </label>
                <input 
                    type="text" 
                    id="code"
                    wire:model="code"
                    placeholder="{{ __('Ej: ABC123') }}"
                    maxlength="10"
                    class="w-full bg-white/5 border text-center text-2xl font-mono uppercase tracking-widest rounded-lg px-4 py-4 text-white placeholder-gray-500 focus:outline-none focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition
                        @error('code') border-red-500 @else border-white/20 @enderror
                    "
                >
                @error('code')
                    <p class="mt-2 text-sm text-red-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Botón Submit --}}
            <button 
                type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3.5 bg-gradient-to-r from-teal-500 to-cyan-500 text-slate-900 font-bold rounded-lg hover:shadow-lg hover:shadow-teal-500/30 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
                <span wire:loading.remove>{{ __('Unirse a Liga') }}</span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Procesando...') }}
                </span>
            </button>
        </form>
    </div>

    {{-- Información adicional --}}
    <div class="mt-6 bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-teal-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mr-4">
                <svg class="w-5 h-5 text-teal-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-teal-400 mb-2 text-sm">{{ __('¿Dónde consigo el código?') }}</h4>
                <ul class="text-sm text-gray-400 space-y-1">
                    <li class="flex items-start">
                        <span class="text-teal-500 mr-2">•</span>
                        <span>{{ __('El dueño de la liga te compartirá el código de 6 caracteres') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-teal-500 mr-2">•</span>
                        <span>{{ __('El código no distingue mayúsculas de minúsculas') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-teal-500 mr-2">•</span>
                        <span>{{ __('Las ligas privadas requieren aprobación del administrador') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Botón volver --}}
    <div class="mt-6 text-center">
        <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" 
           class="inline-flex items-center text-gray-400 hover:text-teal-400 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Volver al inicio') }}
        </a>
    </div>
</div>
</div>

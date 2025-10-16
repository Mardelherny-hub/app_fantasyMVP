<div>
    <div class="max-w-2xl mx-auto">
    {{-- Card principal --}}
    <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-8">
        {{-- Icon --}}
        <div class="w-16 h-16 bg-cyan-500/20 rounded-xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </div>

        {{-- Título --}}
        <h2 class="text-2xl font-bold text-center text-white mb-2">
            {{ __('Crear Liga Privada') }}
        </h2>
        <p class="text-gray-400 text-center mb-8">
            {{ __('Configura tu liga personalizada') }}
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
        <form wire:submit.prevent="createLeague" class="space-y-6">
            {{-- Nombre de la liga --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-300 mb-2">
                    {{ __('Nombre de la Liga') }} <span class="text-red-400">*</span>
                </label>
                <input 
                    type="text" 
                    id="name"
                    wire:model="name"
                    placeholder="{{ __('Ej: Liga de Amigos 2025') }}"
                    maxlength="100"
                    class="w-full bg-white/5 border rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition
                        @error('name') border-red-500 @else border-white/20 @enderror
                    "
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Número de participantes --}}
            <div>
                <label for="max_participants" class="block text-sm font-semibold text-gray-300 mb-2">
                    {{ __('Número de Participantes') }} <span class="text-red-400">*</span>
                </label>
                <select 
                    id="max_participants"
                    wire:model="max_participants"
                    class="w-full bg-white/5 border rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition
                        @error('max_participants') border-red-500 @else border-white/20 @enderror
                    "
                >
                    <option value="4">4 {{ __('participantes') }}</option>
                    <option value="6">6 {{ __('participantes') }}</option>
                    <option value="8">8 {{ __('participantes') }}</option>
                    <option value="10">10 {{ __('participantes') }}</option>
                    <option value="12">12 {{ __('participantes') }}</option>
                    <option value="14">14 {{ __('participantes') }}</option>
                    <option value="16">16 {{ __('participantes') }}</option>
                    <option value="18">18 {{ __('participantes') }}</option>
                    <option value="20">20 {{ __('participantes') }}</option>
                </select>
                @error('max_participants')
                    <p class="mt-2 text-sm text-red-400 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
                <p class="mt-2 text-xs text-gray-400">
                    {{ __('Los cupos vacíos se completarán automáticamente con bots') }}
                </p>
            </div>

            {{-- Idioma --}}
            <div>
                <label for="locale" class="block text-sm font-semibold text-gray-300 mb-2">
                    {{ __('Idioma de la Liga') }} <span class="text-red-400">*</span>
                </label>
                <select 
                    id="locale"
                    wire:model="locale"
                    class="w-full bg-white/5 border border-white/20 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 transition"
                >
                    <option value="es">Español</option>
                    <option value="en">English</option>
                    <option value="fr">Français</option>
                </select>
            </div>

            {{-- Botón Submit --}}
            <button 
                type="submit"
                wire:loading.attr="disabled"
                class="w-full py-3.5 bg-gradient-to-r from-cyan-500 to-blue-500 text-slate-900 font-bold rounded-lg hover:shadow-lg hover:shadow-cyan-500/30 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
            >
                <span wire:loading.remove>{{ __('Crear Liga') }}</span>
                <span wire:loading class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-slate-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ __('Creando...') }}
                </span>
            </button>
        </form>
    </div>

    {{-- Información adicional --}}
    <div class="mt-6 bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-cyan-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mr-4">
                <svg class="w-5 h-5 text-cyan-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-cyan-400 mb-2 text-sm">{{ __('¿Qué sucede después?') }}</h4>
                <ul class="text-sm text-gray-400 space-y-1">
                    <li class="flex items-start">
                        <span class="text-cyan-500 mr-2">•</span>
                        <span>{{ __('Tu liga será enviada para aprobación del administrador') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-cyan-500 mr-2">•</span>
                        <span>{{ __('Recibirás un código único para compartir con tus amigos') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-cyan-500 mr-2">•</span>
                        <span>{{ __('Una vez aprobada, podrás comenzar a competir') }}</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-cyan-500 mr-2">•</span>
                        <span>{{ __('Serás el manager (administrador) de la liga') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Botón volver --}}
    <div class="mt-6 text-center">
        <a href="{{ route('manager.onboarding.welcome', ['locale' => app()->getLocale()]) }}" 
           class="inline-flex items-center text-gray-400 hover:text-cyan-400 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('Volver al inicio') }}
        </a>
    </div>
</div>
</div>

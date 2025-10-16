<x-app-layout>
    <div class="min-h-screen bg-slate-900 text-white py-12 px-4 relative overflow-hidden">
        {{-- Background effects --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-gradient-to-br from-teal-500/15 to-transparent rounded-full blur-3xl" style="animation: pulse 6s ease-in-out infinite; animation-delay: 2s;"></div>
        </div>

        <div class="max-w-5xl mx-auto relative z-10">
            {{-- Header --}}
            <div class="text-center mb-12">
                <div class="inline-flex items-center space-x-2 bg-white/5 backdrop-blur-lg border border-white/10 px-5 py-2.5 rounded-full mb-6">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-semibold text-emerald-400">{{ __('ONBOARDING') }}</span>
                </div>
                
                <h1 class="text-4xl md:text-5xl font-black mb-4 leading-tight">
                    {{ __('¡Bienvenido a') }} <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">Fantasy Football!</span>
                </h1>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto">
                    {{ __('Para comenzar a competir, necesitas unirte o crear una liga') }}
                </p>
            </div>

            {{-- Opciones --}}
            <div class="grid md:grid-cols-3 gap-6 mb-12">
                {{-- Opción 1: Ligas Públicas --}}
                <a href="{{ route('manager.onboarding.public-leagues', ['locale' => app()->getLocale()]) }}" 
                   class="group bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6 hover:border-emerald-500/30 hover:bg-white/10 transition-all duration-300">
                    <div class="w-14 h-14 bg-emerald-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">
                        {{ __('Ligas Públicas') }}
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        {{ __('Únete a una liga abierta disponible para todos') }}
                    </p>
                    <div class="mt-4 inline-flex items-center text-emerald-400 text-sm font-semibold group-hover:translate-x-1 transition-transform">
                        <span>{{ __('Explorar') }}</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                {{-- Opción 2: Código de Liga --}}
                <a href="{{ route('manager.onboarding.join-with-code', ['locale' => app()->getLocale()]) }}" 
                   class="group bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6 hover:border-teal-500/30 hover:bg-white/10 transition-all duration-300">
                    <div class="w-14 h-14 bg-teal-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">
                        {{ __('Unirse con Código') }}
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        {{ __('Ingresa el código de una liga privada') }}
                    </p>
                    <div class="mt-4 inline-flex items-center text-teal-400 text-sm font-semibold group-hover:translate-x-1 transition-transform">
                        <span>{{ __('Ingresar código') }}</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>

                {{-- Opción 3: Crear Liga Privada --}}
                <a href="{{ route('manager.onboarding.create-private', ['locale' => app()->getLocale()]) }}" 
                   class="group bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6 hover:border-cyan-500/30 hover:bg-white/10 transition-all duration-300">
                    <div class="w-14 h-14 bg-cyan-500/20 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">
                        {{ __('Crear Liga Privada') }}
                    </h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        {{ __('Crea tu propia liga privada personalizada') }}
                    </p>
                    <div class="mt-4 inline-flex items-center text-cyan-400 text-sm font-semibold group-hover:translate-x-1 transition-transform">
                        <span>{{ __('Crear liga') }}</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                </a>
            </div>

            {{-- Info adicional --}}
            <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-6">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-emerald-500/20 rounded-lg flex items-center justify-center flex-shrink-0 mr-4">
                        <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-emerald-400 mb-3">{{ __('Información importante') }}</h4>
                        <ul class="text-sm text-gray-400 space-y-2">
                            <li class="flex items-start">
                                <span class="text-emerald-500 mr-2">•</span>
                                <span>{{ __('Las ligas públicas están disponibles de inmediato') }}</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-teal-500 mr-2">•</span>
                                <span>{{ __('Las ligas privadas requieren código de invitación') }}</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-cyan-500 mr-2">•</span>
                                <span>{{ __('Las ligas creadas por ti requieren aprobación del administrador') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
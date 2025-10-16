<x-app-layout>
    <div class="min-h-screen bg-slate-900 text-white py-12 px-4 relative overflow-hidden flex items-center">
        {{-- Background effects --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-gradient-to-br from-yellow-500/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-gradient-to-br from-orange-500/15 to-transparent rounded-full blur-3xl" style="animation: pulse 6s ease-in-out infinite; animation-delay: 2s;"></div>
        </div>

        <div class="max-w-3xl mx-auto relative z-10 w-full">
            {{-- Card principal --}}
            <div class="bg-white/5 backdrop-blur-lg border border-white/10 rounded-2xl p-12 text-center">
                {{-- Icon animado --}}
                <div class="w-24 h-24 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                    <div class="absolute inset-0 rounded-full animate-ping bg-yellow-500/30"></div>
                    <svg class="w-12 h-12 text-yellow-400 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                {{-- Badge --}}
                <div class="inline-flex items-center space-x-2 bg-yellow-500/20 border border-yellow-500/50 px-4 py-2 rounded-full mb-6">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-yellow-500"></span>
                    </span>
                    <span class="text-sm font-semibold text-yellow-400">{{ __('PENDIENTE DE APROBACIÓN') }}</span>
                </div>

                {{-- Título --}}
                <h1 class="text-3xl md:text-4xl font-black mb-4">
                    {{ __('Tu liga está en') }} <span class="bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text text-transparent">{{ __('revisión') }}</span>
                </h1>

                {{-- Descripción --}}
                <p class="text-lg text-gray-300 mb-8 leading-relaxed max-w-2xl mx-auto">
                    {{ __('Tu solicitud de liga ha sido enviada al administrador para su aprobación. Te notificaremos cuando tu liga esté lista.') }}
                </p>

                {{-- Info adicional --}}
                <div class="bg-white/5 border border-white/10 rounded-xl p-6 mb-8 text-left">
                    <h3 class="font-bold text-yellow-400 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('¿Qué sigue?') }}
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ __('El administrador revisará tu liga (normalmente en 24-48 horas)') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            <span>{{ __('Recibirás una notificación cuando sea aprobada') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>{{ __('Obtendrás un código único para invitar a tus amigos') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span>{{ __('Podrás acceder al dashboard de manager y gestionar tu liga') }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Botones --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('manager.onboarding.welcome', ['locale' => app()->getLocale()]) }}" 
                       class="px-6 py-3 bg-white/5 border border-white/20 text-white font-semibold rounded-lg hover:bg-white/10 transition inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Volver al inicio') }}
                    </a>
                    
                    <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" 
                       class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-slate-900 font-bold rounded-lg hover:shadow-lg hover:shadow-yellow-500/30 transition inline-flex items-center justify-center">
                        {{ __('Ir al Dashboard') }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Nota adicional --}}
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-400">
                    {{ __('¿Necesitas ayuda?') }} 
                    <a href="#" class="text-yellow-400 hover:text-yellow-300 transition font-semibold">
                        {{ __('Contacta al soporte') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
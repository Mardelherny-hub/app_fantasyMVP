<x-app-layout>
    <div class="min-h-screen bg-slate-900 text-white py-12 px-4 relative overflow-hidden">
        {{-- Background effects --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-gradient-to-br from-teal-500/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/3 right-1/4 w-[500px] h-[500px] bg-gradient-to-br from-cyan-500/15 to-transparent rounded-full blur-3xl" style="animation: pulse 6s ease-in-out infinite; animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto relative z-10">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center space-x-2 bg-white/5 backdrop-blur-lg border border-white/10 px-5 py-2.5 rounded-full mb-4">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-teal-500"></span>
                    </span>
                    <span class="text-sm font-semibold text-teal-400">{{ __('CÓDIGO DE LIGA') }}</span>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-black mb-3">
                    {{ __('Únete con') }} <span class="bg-gradient-to-r from-teal-400 to-cyan-400 bg-clip-text text-transparent">{{ __('Código') }}</span>
                </h1>
                <p class="text-gray-400 max-w-2xl mx-auto">
                    {{ __('Ingresa el código que te compartió el dueño de la liga') }}
                </p>
            </div>

            {{-- Componente Livewire --}}
            <livewire:manager.onboarding.join-with-code />
        </div>
    </div>
</x-app-layout>
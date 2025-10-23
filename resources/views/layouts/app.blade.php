<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Manager</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-900" x-data="{ sidebarOpen: true, mobileSidebarOpen: false }">
    <div class="min-h-screen flex">
        
        {{-- SIDEBAR --}}
        <aside 
            :class="sidebarOpen ? 'w-64' : 'w-20'" 
            class="hidden lg:flex flex-col fixed inset-y-0 left-0 bg-slate-800/50 backdrop-blur-xl border-r border-white/10 transition-all duration-300 z-40"
        >
            {{-- Logo --}}
            <div class="flex items-center h-16 px-4 border-b border-white/10">
                <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-3 group">
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg transform group-hover:rotate-6 transition-transform duration-300"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 10a1 1 0 112 0v3a1 1 0 11-2 0v-3zm2-3a1 1 0 11-2 0 1 1 0 012 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                        <div class="text-base font-bold tracking-tight text-white whitespace-nowrap">{{ __('Manager Panel') }}</div>
                        <div class="text-[9px] text-emerald-400 -mt-0.5 tracking-wider">EDU CAN SOCCER</div>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
<nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
    
    {{-- SECCIÓN: PRINCIPAL --}}
    <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" 
       class="flex items-center px-3 py-2.5 rounded-lg transition group {{ request()->routeIs('manager.dashboard') ? 
           'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Mi Liga') }}</span>
    </a>


    <div x-show="sidebarOpen" x-transition class="pt-4 pb-2">
        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Mi Equipo') }}</div>
    </div>

    {{-- Plantilla (Squad Builder) - Solo si NO completó --}}
    @if(!auth()->user()->fantasyTeams()->first()?->is_squad_complete)
        <a href="{{ route('manager.squad-builder.index', ['locale' => app()->getLocale()]) }}" 
        class="flex items-center px-3 py-2.5 rounded-lg transition group {{ request()->routeIs('manager.squad-builder.*') ? 
            'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Armar Plantilla') }}</span>
            <span x-show="sidebarOpen" x-transition class="ml-auto">
                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </span>
        </a>
    @else
        {{-- Alineación (Lineup Manager) - Solo si completó plantilla --}}
        <a href="{{ route('manager.lineup.index', ['locale' => app()->getLocale()]) }}" 
        class="flex items-center px-3 py-2.5 rounded-lg transition group {{ request()->routeIs('manager.lineup.*') ? 
            'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Gestionar Alineación') }}</span>
        </a>
    @endif

    {{-- Mis Partidos --}}
    @if(auth()->user()->hasRole('manager'))
        <a href="{{ route('manager.fixtures.index', ['locale' => app()->getLocale()]) }}" 
        class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('manager.fixtures.*') ? 
            'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Mis Partidos') }}</span>
        </a>
    @endif

    {{-- Mis Puntos --}}
    @if(auth()->user()->hasRole('manager'))
        <a href="{{ route('manager.scores.index', ['locale' => app()->getLocale()]) }}" 
        class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('manager.scores.*') ? 
            'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Mis Puntos') }}</span>
        </a>
    @endif

    {{-- MARKET --}}
    <a href="{{ route('manager.market.index', ['locale' => app()->getLocale()]) }}" 
    class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('manager.market.*') ? 'bg-emerald-600 text-white' : 'text-gray-400 hover:bg-slate-700/50 hover:text-white' }}"
    >
        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <div>
            <span class="block font-medium">{{ __('Market') }}</span>
            <span class="text-[10px] text-gray-500">{{ __('Transfer Market') }}</span>
        </div>
    </a>

    {{-- Divider --}}
    <div x-show="sidebarOpen" x-transition class="pt-4 pb-2">
        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Competición') }}</div>
    </div>

    {{-- Clasificación --}}
    <a href="#" 
       class="flex items-center px-3 py-2.5 rounded-lg transition group text-gray-300 hover:bg-white/5 hover:text-white opacity-50 cursor-not-allowed">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Clasificación') }}</span>
        <span x-show="sidebarOpen" x-transition class="ml-auto text-xs text-gray-500 bg-gray-800 px-2 py-0.5 rounded">{{ __('Próximo') }}</span>
    </a>

    {{-- Fixtures --}}
    <a href="#" 
       class="flex items-center px-3 py-2.5 rounded-lg transition group text-gray-300 hover:bg-white/5 hover:text-white opacity-50 cursor-not-allowed">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Calendario') }}</span>
        <span x-show="sidebarOpen" x-transition class="ml-auto text-xs text-gray-500 bg-gray-800 px-2 py-0.5 rounded">{{ __('Próximo') }}</span>
    </a>

    {{-- Estadísticas --}}
    <a href="#" 
       class="flex items-center px-3 py-2.5 rounded-lg transition group text-gray-300 hover:bg-white/5 hover:text-white opacity-50 cursor-not-allowed">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Estadísticas') }}</span>
        <span x-show="sidebarOpen" x-transition class="ml-auto text-xs text-gray-500 bg-gray-800 px-2 py-0.5 rounded">{{ __('Próximo') }}</span>
    </a>

    {{-- Divider --}}
    <div x-show="sidebarOpen" x-transition class="pt-4 pb-2">
        <div class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Gestión') }}</div>
    </div>

    {{-- Unirse a Liga --}}
    <a href="{{ route('manager.onboarding.welcome', ['locale' => app()->getLocale()]) }}" 
       class="flex items-center px-3 py-2.5 rounded-lg transition group {{ request()->routeIs('manager.onboarding.*') ? 
           'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
        </svg>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Unirse a Liga') }}</span>
    </a>

    {{-- Configuración --}}
    <a href="#" 
       class="flex items-center px-3 py-2.5 rounded-lg transition group text-gray-300 hover:bg-white/5 hover:text-white opacity-50 cursor-not-allowed">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span x-show="sidebarOpen" x-transition class="ml-3 font-medium">{{ __('Configuración') }}</span>
        <span x-show="sidebarOpen" x-transition class="ml-auto text-xs text-gray-500 bg-gray-800 px-2 py-0.5 rounded">{{ __('Próximo') }}</span>
    </a>

</nav>

            {{-- Sidebar Toggle --}}
            <div class="p-3 border-t border-white/10">
                <button @click="sidebarOpen = !sidebarOpen" class="w-full flex items-center justify-center px-3 py-2 rounded-lg text-gray-400 hover:bg-white/5 hover:text-white transition">
                    <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                    <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </aside>

        {{-- MOBILE SIDEBAR --}}
        <div x-show="mobileSidebarOpen" @click="mobileSidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/50 backdrop-blur-sm lg:hidden z-50">
        </div>

        <aside x-show="mobileSidebarOpen"
               x-transition:enter="transition ease-in-out duration-300 transform"
               x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0"
               x-transition:leave="transition ease-in-out duration-300 transform"
               x-transition:leave-start="translate-x-0"
               x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 w-64 bg-slate-800/95 backdrop-blur-xl border-r border-white/10 lg:hidden z-50 flex flex-col">
            
            {{-- Same content as desktop sidebar --}}
            <div class="flex items-center justify-between h-16 px-4 border-b border-white/10">
                <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-3">
                    <div class="relative flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 10a1 1 0 112 0v3a1 1 0 11-2 0v-3zm2-3a1 1 0 11-2 0 1 1 0 012 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="text-base font-bold text-white">{{ __('Manager Panel') }}</div>
                        <div class="text-[9px] text-emerald-400 -mt-0.5">EDU CAN SOCCER</div>
                    </div>
                </a>
                <button @click="mobileSidebarOpen = false" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('manager.dashboard') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="ml-3 font-medium">{{ __('Panel') }}</span>
                </a>

                <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="ml-3 font-medium">{{ __('Mi Liga') }}</span>
                </a>

                <a href="{{ route('manager.squad-builder.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg {{ request()->routeIs('manager.squad-builder.*') ? 'bg-emerald-500/20 text-emerald-400' : 'text-gray-300 hover:bg-white/5' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span class="ml-3 font-medium">{{ __('Equipo') }}</span>
                </a>

                <a href="#" class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5 opacity-50 cursor-not-allowed">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="ml-3 font-medium">{{ __('Mercado') }}</span>
                </a>

                <div class="pt-4 pb-2">
                    <div class="px-3 text-xs font-semibold text-gray-500 uppercase">{{ __('Gestión') }}</div>
                </div>

                <a href="{{ route('manager.onboarding.welcome', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2.5 rounded-lg text-gray-300 hover:bg-white/5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span class="ml-3 font-medium">{{ __('Unirse a Liga') }}</span>
                </a>
            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col" :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">
            
            {{-- TOP BAR --}}
            <header class="sticky top-0 z-30 bg-slate-800/50 backdrop-blur-xl border-b border-white/10">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center space-x-4">
                        {{-- Mobile Menu Button --}}
                        <button @click="mobileSidebarOpen = true" class="lg:hidden text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        {{-- Page Title --}}
                        @if(isset($header))
                            <h1 class="text-lg font-semibold text-white">{{ $header }}</h1>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- Language Switcher --}}
                        <x-lang-switcher />

                        {{-- User Menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-3 text-sm focus:outline-none">
                                <span class="hidden md:block text-gray-300 font-medium">{{ auth()->user()->name }}</span>
                                <div class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-slate-900 font-bold text-xs">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </button>

                            <div x-show="open" @click.away="open = false" 
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 class="absolute right-0 mt-2 w-48 bg-slate-800 border border-white/10 rounded-lg shadow-xl py-1 z-50">
                                <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5">
                                    {{ __('Perfil') }}
                                </a>
                                <form method="POST" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-white/5">
                                        {{ __('Cerrar sesión') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- PAGE CONTENT --}}
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
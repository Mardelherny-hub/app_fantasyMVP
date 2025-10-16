<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Manager</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-slate-900 text-white">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-slate-900/90 backdrop-blur-xl border-b border-white/10 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo & Brand -->
                    <div class="flex items-center">
                        <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" class="flex items-center space-x-3 group">
                            <div class="relative">
                                <div class="w-10 h-10 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg transform group-hover:rotate-6 transition-transform duration-300"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8 10a1 1 0 112 0v3a1 1 0 11-2 0v-3zm2-3a1 1 0 11-2 0 1 1 0 012 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <div class="text-lg font-bold tracking-tight">{{ __('Manager Panel') }}</div>
                                <div class="text-[9px] text-emerald-400 -mt-0.5 tracking-wider">FANTASY FOOTBALL</div>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links (Desktop) -->
                    <div class="hidden md:flex items-center space-x-6">
                        <a href="{{ route('manager.dashboard', ['locale' => app()->getLocale()]) }}" 
                           class="text-sm font-medium hover:text-emerald-400 transition {{ request()->routeIs('manager.dashboard') ? 'text-emerald-400' : 'text-gray-300' }}">
                            {{ __('Dashboard') }}
                        </a>
                        <a href="#" 
                           class="text-sm font-medium text-gray-300 hover:text-emerald-400 transition">
                            {{ __('Mi Liga') }}
                        </a>
                        <a href="#" 
                           class="text-sm font-medium text-gray-300 hover:text-emerald-400 transition">
                            {{ __('Equipo') }}
                        </a>
                        <a href="#" 
                           class="text-sm font-medium text-gray-300 hover:text-emerald-400 transition">
                            {{ __('Mercado') }}
                        </a>
                    </div>

                    <!-- User Dropdown -->
                    <div class="flex items-center space-x-4">
                        <!-- Language Switcher -->
                        <div class="hidden md:flex items-center space-x-1 bg-white/5 rounded-full px-1 py-1 border border-white/10">
                            <a href="{{ route(Route::currentRouteName(), ['locale' => 'es'] + request()->route()->parameters()) }}" 
                               class="px-2.5 py-1 rounded-full text-xs font-bold transition {{ app()->getLocale() === 'es' ? 'bg-emerald-500 text-slate-900' : 'text-gray-400 hover:text-white' }}">
                                ES
                            </a>
                            <a href="{{ route(Route::currentRouteName(), ['locale' => 'en'] + request()->route()->parameters()) }}" 
                               class="px-2.5 py-1 rounded-full text-xs font-bold transition {{ app()->getLocale() === 'en' ? 'bg-emerald-500 text-slate-900' : 'text-gray-400 hover:text-white' }}">
                                EN
                            </a>
                            <a href="{{ route(Route::currentRouteName(), ['locale' => 'fr'] + request()->route()->parameters()) }}" 
                               class="px-2.5 py-1 rounded-full text-xs font-bold transition {{ app()->getLocale() === 'fr' ? 'bg-emerald-500 text-slate-900' : 'text-gray-400 hover:text-white' }}">
                                FR
                            </a>
                        </div>

                        <!-- User Menu -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="flex items-center space-x-2 bg-white/5 border border-white/10 rounded-lg px-3 py-2 hover:bg-white/10 transition">
                                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-6 h-6 rounded-full">
                                <span class="text-sm font-medium hidden md:block">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-slate-800 border border-white/10 rounded-lg shadow-xl py-1 z-50"
                                 style="display: none;">
                                
                                <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" 
                                   class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-emerald-400 transition">
                                    {{ __('Perfil') }}
                                </a>
                                
                                @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('admin.dashboard', ['locale' => app()->getLocale()]) }}" 
                                       class="block px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-purple-400 transition">
                                        {{ __('Panel Admin') }}
                                    </a>
                                @endif

                                <div class="border-t border-white/10 my-1"></div>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-400 hover:bg-white/5 transition">
                                        {{ __('Cerrar Sesión') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
{{-- Sidebar Navigation --}}
<ul class="space-y-1">
    {{-- Dashboard --}}
    <li>
        <a href="{{ route('admin.dashboard', ['locale' => app()->getLocale()]) }}" 
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                  {{ request()->routeIs('admin.dashboard') 
                     ? 'bg-gray-800 text-white' 
                     : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="sidebarOpen">{{ __('Dashboard') }}</span>
        </a>
    </li>

    {{-- Users & Roles --}}
    <li x-data="{ open: {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                       {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') 
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Usuarios y Roles') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{ route('admin.users.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.users.*') 
                             ? 'bg-gray-800 text-white' 
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Usuarios') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.roles.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.roles.*') 
                             ? 'bg-gray-800 text-white' 
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Roles') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Fantasy --}}
    <li x-data="{ open: {{ request()->routeIs('admin.fantasy.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                       {{ request()->routeIs('admin.fantasy.*') 
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Fantasia') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{ route('admin.leagues.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Ligas') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.seasons.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Temporadas') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.gameweeks.index', ['locale' => app()->getLocale()]) }}" 
                class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                        {{ request()->routeIs('admin.gameweeks.*') 
                            ? 'bg-gray-800 text-white' 
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Jornadas') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.real-teams.index', ['locale' => app()->getLocale()]) }}" 
                    class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                            {{ request()->routeIs('admin.real-teams.*') 
                                ? 'bg-gray-800 text-white' 
                                : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                        <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                        {{ __('Equipos') }}
                    </a>
            </li>
            <li>
                <a href="{{ route('admin.football-matches.index', ['locale' => app()->getLocale()]) }}" 
                class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                        {{ request()->routeIs('admin.football-matches.*') 
                            ? 'bg-gray-800 text-white' 
                            : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Partidos') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Players --}}
    <li>
        <a href="{{ route('admin.players.index', ['locale' => app()->getLocale()]) }}" 
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                  {{ request()->routeIs('admin.players.*') 
                     ? 'bg-gray-800 text-white' 
                     : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span x-show="sidebarOpen">{{ __('Jugadores') }}</span>
        </a>
    </li>

    {{-- Matches --}}
    <li x-data="{ open: {{ request()->routeIs('admin.matches.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                       {{ request()->routeIs('admin.matches.*') 
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Partidos') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{-- route('admin.matches.fixtures', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Encuentros') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.matches.results', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Resultados') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.matches.scoring', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Reglas de Puntuación') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Quiz System --}}
    <li x-data="{ open: {{ request()->routeIs('admin.quiz.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                       {{ request()->routeIs('admin.quiz.*') 
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Sistema de Quizzes') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{-- route('admin.quiz.questions.index', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Preguntas') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.quiz.categories.index', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Categorías') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.quiz.quizzes.index', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Quizzes') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Market --}}
    <li>
        <a href="{{-- route('admin.market.index', ['locale' => app()->getLocale()]) --}}" 
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                  {{ request()->routeIs('admin.market.*') 
                     ? 'bg-gray-800 text-white' 
                     : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span x-show="sidebarOpen">{{ __('Mercado') }}</span>
        </a>
    </li>

    {{-- Economy --}}
    <li>
        <a href="{{-- route('admin.economy.rewards.index', ['locale' => app()->getLocale()]) --}}" 
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                  {{ request()->routeIs('admin.economy.*') 
                     ? 'bg-gray-800 text-white' 
                     : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="sidebarOpen">{{ __('Economía') }}</span>
        </a>
    </li>

    {{-- Reports --}}
    <li x-data="{ open: {{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                       {{ request()->routeIs('admin.reports.*') 
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Informes') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{-- route('admin.reports.users', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Usuarios') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.reports.leagues', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Ligas') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Divider --}}
    <li class="border-t border-gray-800 my-2"></li>

    {{-- Settings --}}
    <li x-data="{ open: {{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                       {{ request()->routeIs('admin.settings.*') 
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Configuración') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{-- route('admin.settings.general', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('General') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.settings.scoring', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Puntuación') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.settings.market', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Mercado') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.settings.rewards', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Recompensas') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Maintenance --}}
    <li x-data="{ open: {{ request()->routeIs('admin.maintenance.*') || request()->routeIs('admin.audit.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors
                       {{ request()->routeIs('admin.maintenance.*') || request()->routeIs('admin.audit.*')
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Mantenimiento') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{-- route('admin.maintenance.cache', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Caché') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.maintenance.logs', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Registros') }}
                </a>
            </li>
            <li>
                <a href="{{-- route('admin.audit.index', ['locale' => app()->getLocale()]) --}}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors text-gray-400 hover:bg-gray-800 hover:text-white">
                    <span class="w-2 h-2 bg-current rounded-full mr-3"></span>
                    {{ __('Registros de Auditoría') }}
                </a>
            </li>
        </ul>
    </li>
</ul>
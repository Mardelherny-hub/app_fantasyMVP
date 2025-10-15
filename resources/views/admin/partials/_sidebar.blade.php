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

    {{-- Usuarios y Roles --}}
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
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Usuarios') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.roles.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.roles.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Roles') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Fantasy Management --}}
    <li x-data="{ open: {{ request()->routeIs('admin.leagues.*') || request()->routeIs('admin.seasons.*') || request()->routeIs('admin.gameweeks.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                       {{ request()->routeIs('admin.leagues.*') || request()->routeIs('admin.seasons.*') || request()->routeIs('admin.gameweeks.*')
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Fantasy') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{ route('admin.leagues.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.leagues.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Ligas') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.seasons.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.seasons.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Temporadas') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.gameweeks.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.gameweeks.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Jornadas') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- ⭐ NUEVA SECCIÓN: Real Data (Datos de Ligas Reales Canadá) --}}
    <li x-data="{ open: {{ request()->routeIs('admin.real-competitions.*') || request()->routeIs('admin.real-fixtures.*') || request()->routeIs('admin.real-matches.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                       {{ request()->routeIs('admin.real-competitions.*') || request()->routeIs('admin.real-fixtures.*') || request()->routeIs('admin.real-matches.*')
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Datos Reales') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{ route('admin.real-competitions.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-competitions.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Competiciones') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.real-fixtures.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-fixtures.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Fixtures') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.real-matches.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-matches.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Partidos Jugados') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Real Teams & Players --}}
    <li x-data="{ open: {{ request()->routeIs('admin.real-teams.*') || request()->routeIs('admin.players.*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                       {{ request()->routeIs('admin.real-teams.*') || request()->routeIs('admin.players.*')
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Equipos y Jugadores') }}</span>
            </div>
            <svg x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
        
        <ul x-show="open && sidebarOpen" x-collapse class="mt-1 space-y-1 ml-4">
            <li>
                <a href="{{ route('admin.real-teams.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-teams.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Equipos') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.players.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.players.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                    {{ __('Jugadores') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Football Matches (Sistema Fantasy) --}}
    <li>
        <a href="{{ route('admin.football-matches.index', ['locale' => app()->getLocale()]) }}" 
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                  {{ request()->routeIs('admin.football-matches.*') 
                     ? 'bg-gray-800 text-white' 
                     : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span x-show="sidebarOpen">{{ __('Partidos Fantasy') }}</span>
        </a>
    </li>

</ul>
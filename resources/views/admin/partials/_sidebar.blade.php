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

    {{-- Real Game (Datos Reales) --}}
    <li x-data="{ open: {{ request()->routeIs('admin.real-*') ? 'true' : 'false' }} }">
        <button @click="open = !open" 
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                       {{ request()->routeIs('admin.real-*') 
                          ? 'bg-gray-800 text-white' 
                          : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <div class="flex items-center">
                <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
                <span x-show="sidebarOpen">{{ __('Real Game') }}</span>
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
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ __('Competitions') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.real-teams.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-teams.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ __('Teams') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.real-players.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-players.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ __('Real Players') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.real-fixtures.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-fixtures.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ __('Fixtures') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.real-matches.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.real-matches.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ __('Matches Played') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Fantasy --}}
<div x-data="{ open: {{ request()->is('*/admin/fantasy/*') ? 'true' : 'false' }} }">
    <button @click="open = !open" 
            class="flex items-center justify-between w-full px-4 py-2.5 text-sm font-medium rounded-lg transition-colors
                {{ request()->is('*/admin/fantasy/*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-700' }}">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
            </svg>
            <span>{{ __('Fantasy') }}</span>
        </div>
        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="ml-8 mt-2 space-y-1">
        <a href="{{ route('admin.fantasy.leagues.index', ['locale' => app()->getLocale()]) }}" 
           class="block px-4 py-2 text-sm rounded-lg transition-colors
               {{ request()->routeIs('admin.fantasy.leagues.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
            {{ __('Ligas') }}
        </a>
        <a href="{{ route('admin.fantasy.seasons.index', ['locale' => app()->getLocale()]) }}" 
           class="block px-4 py-2 text-sm rounded-lg transition-colors
               {{ request()->routeIs('admin.fantasy.seasons.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
            {{ __('Temporadas') }}
        </a>
        <a href="{{-- route('admin.fantasy.gameweeks.index', ['locale' => app()->getLocale()]) --}}#" 
           class="block px-4 py-2 text-sm rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed
               {{ request()->routeIs('admin.fantasy.gameweeks.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
            {{ __('Jornadas') }}
        </a>
        <a href="{{-- route('admin.fantasy.teams.index', ['locale' => app()->getLocale()]) --}}#" 
           class="block px-4 py-2 text-sm rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed
               {{ request()->routeIs('admin.fantasy.teams.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
            {{ __('Equipos Fantasy') }}
        </a>
    </div>
</div>

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
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ __('Usuarios') }}
                </a>
            </li>
            <li>
                <a href="{{ route('admin.roles.index', ['locale' => app()->getLocale()]) }}" 
                   class="flex items-center px-3 py-2 text-sm rounded-lg transition-colors
                          {{ request()->routeIs('admin.roles.*') 
                             ? 'bg-gray-700 text-white' 
                             : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                    {{ __('Roles') }}
                </a>
            </li>
        </ul>
    </li>

    {{-- Configuración --}}
    <li>
        <a href="{{-- route('admin.settings.index', ['locale' => app()->getLocale()]) --}}#" 
           class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors group
                  {{ request()->routeIs('admin.settings.*') 
                     ? 'bg-gray-800 text-white' 
                     : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0" :class="sidebarOpen ? 'mr-3' : 'mx-auto'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span x-show="sidebarOpen">{{ __('Configuración') }}</span>
        </a>
    </li>
</ul>
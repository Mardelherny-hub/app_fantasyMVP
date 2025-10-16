<x-guest-layout>
    <div class="min-h-screen bg-slate-900 flex items-center justify-center px-4 py-12 relative overflow-hidden">
        {{-- Background effects --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 left-1/4 w-[600px] h-[600px] bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-gradient-to-br from-teal-500/15 to-transparent rounded-full blur-3xl" style="animation: pulse 6s ease-in-out infinite; animation-delay: 2s;"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            {{-- Logo/Header --}}
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black mb-2">
                    <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">{{ config('app.name') }}</span>
                </h1>
                <p class="text-gray-400">{{ __('Inicia sesión en tu cuenta') }}</p>
            </div>

            {{-- Card --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
                {{-- Validation Errors --}}
                <x-validation-errors class="mb-4" />

                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-400 bg-green-400/10 border border-green-400/20 rounded-lg p-3">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="mb-5">
                        <label for="email" class="block font-medium text-sm text-gray-300 mb-2">{{ __('Email') }}</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus 
                               autocomplete="username"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>

                    {{-- Password --}}
                    <div class="mb-5">
                        <label for="password" class="block font-medium text-sm text-gray-300 mb-2">{{ __('Password') }}</label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between mb-6">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" 
                                   type="checkbox" 
                                   name="remember"
                                   class="rounded bg-white/5 border-white/10 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0">
                            <span class="ml-2 text-sm text-gray-400">{{ __('Remember me') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition">
                                {{ __('Forgot your password?') }}
                            </a>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" 
                            class="w-full py-3 rounded-lg font-semibold bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 hover:shadow-lg hover:shadow-emerald-500/30 hover:scale-105 transition-all duration-300">
                        {{ __('Log in') }}
                    </button>
                </form>

                {{-- Divider --}}
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/10"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-slate-900/50 text-gray-400">{{ __('Or') }}</span>
                    </div>
                </div>

                {{-- Register Link --}}
                <div class="text-center">
                    <p class="text-gray-400 text-sm">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('register') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold transition">
                            {{ __('Sign up') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
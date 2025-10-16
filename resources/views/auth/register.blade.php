<x-guest-layout>
    <div class="min-h-screen bg-slate-900 flex items-center justify-center px-4 py-12 relative overflow-hidden">
        {{-- Background effects --}}
        <div class="fixed inset-0 pointer-events-none overflow-hidden">
            <div class="absolute top-0 right-1/4 w-[600px] h-[600px] bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-0 left-1/4 w-[500px] h-[500px] bg-gradient-to-br from-teal-500/15 to-transparent rounded-full blur-3xl" style="animation: pulse 6s ease-in-out infinite; animation-delay: 2s;"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            {{-- Logo/Header --}}
            <div class="text-center mb-8">
                <h1 class="text-4xl font-black mb-2">
                    <span class="bg-gradient-to-r from-emerald-400 to-teal-400 bg-clip-text text-transparent">{{ config('app.name') }}</span>
                </h1>
                <p class="text-gray-400">{{ __('Crea tu cuenta y comienza a competir') }}</p>
            </div>

            {{-- Card --}}
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl">
                {{-- Validation Errors --}}
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-300 mb-2">{{ __('Name') }}</label>
                        <input id="name" 
                               type="text" 
                               name="name" 
                               value="{{ old('name') }}" 
                               required 
                               autofocus 
                               autocomplete="name"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>

                    {{-- Nickname (username) --}}
                    <div class="mb-4">
                        <label for="username" class="block font-medium text-sm text-gray-300 mb-2">{{ __('Nickname') }}</label>
                        <input id="username" 
                               type="text" 
                               name="username" 
                               value="{{ old('username') }}" 
                               required 
                               autocomplete="username"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="block font-medium text-sm text-gray-300 mb-2">{{ __('Email') }}</label>
                        <input id="email" 
                               type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="username"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label for="password" class="block font-medium text-sm text-gray-300 mb-2">{{ __('Password') }}</label>
                        <input id="password" 
                               type="password" 
                               name="password" 
                               required 
                               autocomplete="new-password"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>

                    {{-- Confirm Password --}}
                    <div class="mb-5">
                        <label for="password_confirmation" class="block font-medium text-sm text-gray-300 mb-2">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" 
                               type="password" 
                               name="password_confirmation" 
                               required 
                               autocomplete="new-password"
                               class="w-full rounded-lg bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>

                    {{-- Terms and Privacy Policy --}}
                    @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                        <div class="mb-6">
                            <label for="terms" class="flex items-start">
                                <input id="terms" 
                                       type="checkbox" 
                                       name="terms" 
                                       required
                                       class="rounded bg-white/5 border-white/10 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-0 mt-0.5">
                                <span class="ml-2 text-sm text-gray-400">
                                    {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                            'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-emerald-400 hover:text-emerald-300 underline">'.__('Terms of Service').'</a>',
                                            'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-emerald-400 hover:text-emerald-300 underline">'.__('Privacy Policy').'</a>',
                                    ]) !!}
                                </span>
                            </label>
                        </div>
                    @endif

                    {{-- Submit Button --}}
                    <button type="submit" 
                            class="w-full py-3 rounded-lg font-semibold bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 hover:shadow-lg hover:shadow-emerald-500/30 hover:scale-105 transition-all duration-300">
                        {{ __('Register') }}
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

                {{-- Login Link --}}
                <div class="text-center">
                    <p class="text-gray-400 text-sm">
                        {{ __('Already registered?') }}
                        <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 font-semibold transition">
                            {{ __('Log in') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
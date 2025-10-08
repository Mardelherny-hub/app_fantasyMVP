<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - EduCan Fantasy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .soft-glow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        .input-style {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
        }
        .input-style:focus {
            border-color: #38bdf8; /* Sky 400 */
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.5);
        }
        /* Ajuste de color del texto del placeholder */
        .input-style::placeholder {
            color: rgba(156, 163, 175, 0.7); /* Gray 400 */
        }
    </style>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <div class="fixed inset-0 soft-glow"></div>

    <div class="w-full max-w-md relative z-10">

        <div class="text-center mb-10">
            <a href="/" class="inline-flex items-center space-x-3 group">
                <div class="relative">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-futbol text-slate-900 text-xl"></i>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold tracking-tight">EduCan</div>
                    <div class="text-xs text-emerald-400 -mt-0.5 tracking-wider">SOCCER FANTASY</div>
                </div>
            </a>
        </div>

        <div class="glass-card rounded-2xl p-8 md:p-10">
            <h2 class="text-3xl font-bold mb-6 text-center">Iniciar Sesión</h2>

            @session('status')
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ $value }}
                </div>
            @endsession
            
            <form method="POST" action="{{ route('login') }}">
                @csrf <div class="mb-5">
                    <label for="email"  value="{{ __('Email') }} class="block font-medium text-sm text-gray-300 mb-2">Correo Electrónico</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" 
                           class="w-full rounded-lg input-style px-4 py-2.5 transition duration-150 ease-in-out">
                </div>

                <div class="mb-5">
                    <label for="password"  value="{{ __('Password') }} class="block font-medium text-sm text-gray-300 mb-2">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                           class="w-full rounded-lg input-style px-4 py-2.5 transition duration-150 ease-in-out">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label for="remember_me" class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" 
                               class="rounded border-gray-300 text-emerald-500 shadow-sm focus:ring-emerald-500 bg-gray-700 border-gray-600">
                        <span class="ml-2 text-sm text-gray-400">Recordarme</span>
                    </label>

                    <a href="/forgot-password" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors duration-200">
                        @if (Route::has('password.request'))
                            {{ __('Forgot your password?') }}
                        @endif
                    </a>
                </div>

                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="w-full px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 font-bold rounded-xl hover:shadow-lg hover:shadow-emerald-500/30 hover:scale-[1.01] transition-all duration-300">
                        <span class="flex items-center justify-center"><i class="fas fa-lock mr-2"></i>{{ __('Log in') }}</span>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm">
                <span class="text-gray-400">¿No tienes cuenta?</span>
                <a href="/register" class="ml-1 text-teal-400 font-semibold hover:text-teal-300 transition-colors duration-200">
                    Regístrate gratis
                </a>
            </div>
        </div>

    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - EduCan Fantasy</title>
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

    <!-- Fondo sutil de resplandor -->
    <div class="fixed inset-0 soft-glow"></div>

    <div class="w-full max-w-md relative z-10">

        <!-- Logo -->
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

        <!-- Tarjeta de Registro (Glass Card) -->
        <div class="glass-card rounded-2xl p-8 md:p-10">
            <h2 class="text-3xl font-bold mb-6 text-center">Crear Cuenta Gratis</h2>

            <!-- Mensajes de error y estado (Simulados de Jetstream) -->
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <!-- Nombre -->
                <div class="mb-5">
                    <label for="name" value="{{ __('Name') }}" class="block font-medium text-sm text-gray-300 mb-2">Nombre</label>
                    <input id="name" type="text" name="name":value="old('name')"  required autofocus autocomplete="name" 
                           class="w-full rounded-lg input-style px-4 py-2.5 transition duration-150 ease-in-out">
                </div>
                
                <!-- SobreNombre -->
                <div class="mb-5">
                    <label for="nickname" value="{{ __('Nickname') }}" class="block font-medium text-sm text-gray-300 mb-2">Nickname</label>
                    <input id="name" type="text" name="name":value="old('Nickname')"  required autofocus autocomplete="Nickname" 
                           class="w-full rounded-lg input-style px-4 py-2.5 transition duration-150 ease-in-out">
                </div>

                <!-- Correo Electrónico -->
                <div class="mb-5">
                    <label for="email" value="{{ __('Email') }} class="block font-medium text-sm text-gray-300 mb-2">Correo Electrónico</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" 
                           class="w-full rounded-lg input-style px-4 py-2.5 transition duration-150 ease-in-out">
                </div>

                <!-- Contraseña -->
                <div class="mb-5">
                    <label for="password"  value="{{ __('Password') }}" class="block font-medium text-sm text-gray-300 mb-2">Contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" 
                           class="w-full rounded-lg input-style px-4 py-2.5 transition duration-150 ease-in-out">
                </div>

                <!-- Confirmar Contraseña -->
                <div class="mb-5">
                    <label for="password_confirmation"  value="{{ __('Confirm Password') }}" class="block font-medium text-sm text-gray-300 mb-2">Confirmar Contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                           class="w-full rounded-lg input-style px-4 py-2.5 transition duration-150 ease-in-out">
                </div>

                <!-- Términos de Servicio (Jetstream requirement) -->
                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mb-6">
                        <label for="terms" class="flex items-center">
                            <input id="terms" type="checkbox" name="terms" required
                                class="rounded border-gray-300 text-emerald-500 shadow-sm focus:ring-emerald-500 bg-gray-700 border-gray-600">
                            <span class="ml-2 text-sm text-gray-400">
                                Acepto los <a target="_blank" href="'.route('terms.show').'" class="underline text-emerald-400 hover:text-emerald-300">Términos de Servicio</a> y la <a target="_blank" href="'.route('policy.show').'" class="underline text-emerald-400 hover:text-emerald-300">Política de Privacidad</a>
                            </span>
                        </label>
                    </div>
                @endif


                <!-- Botón de Registro -->
                <div class="flex items-center justify-end mt-4">
                    <button type="submit" class="w-full px-5 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-slate-900 font-bold rounded-xl hover:shadow-lg hover:shadow-emerald-500/30 hover:scale-[1.01] transition-all duration-300">
                        <span class="flex items-center justify-center"><i class="fas fa-user-plus mr-2"></i> Registrarme</span>
                    </button>
                </div>
            </form>

            <!-- Separador o Enlace a Login -->
            <div class="mt-8 text-center text-sm">
                <span class="text-gray-400">¿Ya estás registrado?</span>
                <a href="{{ route('login') }}" class="ml-1 text-teal-400 font-semibold hover:text-teal-300 transition-colors duration-200">
                    Iniciar Sesión
                </a>
            </div>
        </div>

    </div>

</body>
</html>

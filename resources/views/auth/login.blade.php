<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Control de Asistencias</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-800 via-blue-700 to-sky-500 flex items-center justify-center px-4">

    {{-- Contenedor principal tipo “card” grande --}}
    <div class="w-full max-w-5xl bg-white/90 rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

        {{-- PANEL IZQUIERDO (bienvenida) --}}
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-indigo-800 via-blue-700 to-sky-500 text-white p-10 flex-col justify-between">
            {{-- Logo + nombre sistema --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center border border-white/30">
                    <span class="text-sm font-bold tracking-wide">FP</span>
                </div>
                <div class="leading-tight">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Sistema</p>
                    <p class="text-sm font-semibold">Control de Asistencias</p>
                </div>
            </div>

            {{-- Mensaje central --}}
            <div class="mt-10 mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold mb-3">
                    Hola,<br> ¡bienvenido!
                </h1>
                <p class="text-sm text-blue-100 max-w-xs">
                    Administra la asistencia de los estudiantes de forma rápida, clara y segura.
                </p>
            </div>

            {{-- Botón informativo / ir a registro --}}
            <div>
                <p class="text-[11px] mt-3 text-blue-100/80">
                    Solo personal autorizado puede acceder al sistema.
                </p>
            </div>
        </div>

        {{-- PANEL DERECHO (formulario de login) --}}
        <div class="w-full md:w-1/2 bg-white px-7 py-8 md:px-10 md:py-10">
            {{-- Título móvil (cuando no se ve el panel izquierdo) --}}
            <div class="md:hidden mb-6">
                <h2 class="text-xl font-extrabold text-slate-800 text-center">
                    Control de Asistencias
                </h2>
                <p class="text-xs text-slate-500 text-center mt-1">
                    Inicia sesión para continuar
                </p>
            </div>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="flex items-start gap-2 bg-red-50 text-red-700 text-sm p-3 rounded-xl mb-4 border border-red-100">
                    <span class="mt-0.5">⚠️</span>
                    <span>{{ $errors->first('email') }}</span>
                </div>
            @endif

            {{-- Formulario --}}
            <form method="POST" action="{{ url('/login') }}" class="space-y-4">
                @csrf

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Correo electrónico
                    </label>
                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm placeholder-slate-400
                                   focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 outline-none transition"
                            placeholder="tucorreo@colegio.pe"
                            required
                        >
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Contraseña
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="passwordField"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm placeholder-slate-400
                                   focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 outline-none transition"
                            placeholder="••••••••"
                            required
                        >
                        <button type="button"
                                id="togglePassword"
                                class="absolute inset-y-0 right-0 px-3 text-[11px] text-slate-500 hover:text-slate-700">
                            Mostrar
                        </button>
                    </div>
                </div>

                {{-- Recordarme + olvidé --}}
                <div class="flex items-center justify-between text-[11px] text-slate-500">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="remember"
                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span>Recordarme</span>
                    </label>
                </div>

                {{-- Botón login --}}
                <button type="submit"
                        class="w-full mt-2 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-500 text-white text-sm font-semibold
                               shadow-lg shadow-blue-500/40 hover:brightness-110 transition">
                    Iniciar sesión
                </button>
            </form>

        </div>
    </div>

    <script>
        // Mostrar / ocultar contraseña
        const pwd = document.getElementById('passwordField');
        const btn = document.getElementById('togglePassword');

        if (pwd && btn) {
            btn.addEventListener('click', () => {
                const isHidden = pwd.type === 'password';
                pwd.type = isHidden ? 'text' : 'password';
                btn.textContent = isHidden ? 'Ocultar' : 'Mostrar';
            });
        }
    </script>

</body>
</html>

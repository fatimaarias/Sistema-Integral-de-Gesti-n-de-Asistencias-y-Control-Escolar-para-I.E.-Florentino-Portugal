<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Control de Asistencias</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-800 via-blue-700 to-sky-500 flex items-center justify-center px-4">

    {{-- CARD PRINCIPAL --}}
    <div class="w-full max-w-5xl bg-white/95 rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

        {{-- PANEL IZQUIERDO (info bonita) --}}
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-indigo-800 via-blue-700 to-sky-500 text-white p-10 flex-col justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center border border-white/30">
                    <span class="text-sm font-bold tracking-wide">FP</span>
                </div>
                <div class="leading-tight">
                    <p class="text-xs uppercase tracking-[0.2em] text-blue-100">Sistema</p>
                    <p class="text-sm font-semibold">Control de Asistencias</p>
                </div>
            </div>

            <div class="mt-10 mb-8">
                <h1 class="text-3xl md:text-4xl font-extrabold mb-3">
                    Crea una nueva cuenta
                </h1>
                <p class="text-sm text-blue-100 max-w-xs">
                    Registra usuarios del personal para administrar la asistencia de los estudiantes
                    de manera organizada y segura.
                </p>
                <ul class="mt-4 space-y-1 text-xs text-blue-100/90">
                    <li>• Control por grados y secciones</li>
                    <li>• Roles con distintos permisos</li>
                    <li>• Registro rápido y sencillo</li>
                </ul>
            </div>

            <div>
             
            </div>
        </div>

        {{-- PANEL DERECHO (formulario) --}}
        <div class="w-full md:w-1/2 bg-white px-7 py-8 md:px-10 md:py-10">
            {{-- Título móvil --}}
            <div class="md:hidden mb-6 text-center">
                <h2 class="text-xl font-extrabold text-slate-800">Crear una cuenta</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Completa los datos para registrarte
                </p>
            </div>

            {{-- Errores --}}
            @if ($errors->any())
                <div class="flex items-start gap-2 bg-red-50 text-red-700 text-sm p-3 rounded-xl mb-4 border border-red-100">
                    <span class="mt-0.5">⚠️</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                {{-- Nombre --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Nombre completo
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm
                                  focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 outline-none transition"
                           placeholder="Ej: Jimmy Amorin"
                           required>
                </div>

                {{-- Correo --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Correo electrónico
                    </label>
                    <input type="email"
                           name="email"
                           value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm
                                  focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 outline-none transition"
                           placeholder="tucorreo@colegio.pe"
                           required>
                </div>

                {{-- Contraseña --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Contraseña
                    </label>
                    <input type="password"
                           name="password"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm
                                  focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 outline-none transition"
                           placeholder="••••••••"
                           required>
                </div>

                {{-- Confirmación --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Confirmar contraseña
                    </label>
                    <input type="password"
                           name="password_confirmation"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm
                                  focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 outline-none transition"
                           placeholder="Repite la contraseña"
                           required>
                </div>

                {{-- Rol --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Asignar rol
                    </label>
                    <select name="role"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm
                                   focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/40 outline-none transition"
                            required>
                        <option value="">Selecciona un rol</option>
                        <option value="admin"      {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="auxiliar"   {{ old('role') === 'auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                        <option value="secundario" {{ old('role') === 'secundario' ? 'selected' : '' }}>Usuario secundario</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">
                        El rol controla los permisos para gestionar asistencias y usuarios.
                    </p>
                </div>

                {{-- Botones --}}
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('dashboard') }}"
                       class="w-1/2 inline-flex items-center justify-center py-2.5 rounded-xl
                              bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
                        ← Cancelar
                    </a>

                    <button type="submit"
                            class="w-1/2 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600
                                   text-white text-sm font-semibold shadow-md hover:brightness-110 transition">
                        Registrarme
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear usuario para empleado') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    {{-- Alertas --}}
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Resumen del empleado --}}
                    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="space-y-1">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $empleado->Nombres }} {{ $empleado->Apellidos }}
                            </h3>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">DNI:</span>
                                <span class="font-mono">{{ $empleado->Dni }}</span>
                            </p>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Cargo:</span>
                                {{ $empleado->cargo->DescripcionCargo ?? 'Sin cargo' }}
                            </p>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Correo empleado:</span>
                                {{ $empleado->CorreElec ?: 'No registrado' }}
                            </p>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Género:</span>
                                {{ $empleado->Genero ?? 'No especificado' }}
                            </p>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Ingreso:</span>
                                {{ $empleado->FechaIngreso ? \Carbon\Carbon::parse($empleado->FechaIngreso)->format('d/m/Y') : 'N/D' }}
                                ·
                                <span class="font-semibold">Nacimiento:</span>
                                {{ $empleado->FechaNacimiento ? \Carbon\Carbon::parse($empleado->FechaNacimiento)->format('d/m/Y') : 'N/D' }}
                            </p>
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Estado:</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $empleado->Estado ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $empleado->Estado ? 'Activo' : 'Inactivo' }}
                                </span>
                            </p>
                        </div>

                        <div class="flex flex-col items-center gap-2">
                            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-indigo-100">
                                <span class="text-xl font-bold text-indigo-600">
                                    {{ strtoupper(substr($empleado->Nombres,0,1).substr($empleado->Apellidos,0,1)) }}
                                </span>
                            </div>
                            @if($empleado->doctor)
                                <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-xs font-medium text-purple-700">
                                    👨‍⚕️ Perfil médico
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Formulario de creación de usuario --}}
                    <form method="POST" action="{{ route('empleados.store-usuario', $empleado->IdEmpleado) }}" class="space-y-6">
                        @csrf

                        <div>
                            <h4 class="text-md font-semibold text-gray-800 mb-3 border-b pb-2 flex items-center gap-2">
                                <span class="text-purple-600">👤</span>
                                <span>Datos de acceso</span>
                            </h4>

                            <p class="text-xs text-gray-500 mb-4">
                                Se creará una cuenta para que el empleado pueda ingresar al sistema según el rol asignado a su cargo.
                            </p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Email --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Email de acceso *
                                    </label>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email', $empleado->CorreElec) }}"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                    @error('email')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Se recomienda usar el mismo correo registrado en el empleado. Este será el usuario de inicio de sesión.
                                    </p>
                                </div>

                                {{-- Contraseña con ojito --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Contraseña inicial *
                                    </label>

                                    <div class="relative">
                                        <input
                                            type="password"
                                            name="password"
                                            id="password_input"
                                            value="{{ old('password', $empleado->Dni) }}"
                                            required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10"
                                        >

                                        <button
                                            type="button"
                                            id="toggle_password"
                                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600"
                                            tabindex="-1"
                                        >
                                            {{-- ojo abierto --}}
                                            <svg id="icon_eye_open" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                <rcle cx="12" cy="12" r="3" />
                                            </svg>
                                            {{-- ojo tachado --}}
                                            <svg id="icon_eye_off" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                                                 viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                      d="M3 3l18 18M10.477 10.48A3 3 0 0113.5 13.5m-1.26 3.24C11.83 16.9 11.42 17 11 17c-4.477 0-8.268-2.943-9.542-7a11.973 11.973 0 013.24-4.58M9.88 4.12A11.94 11.94 0 0112 4c4.477 0 8.268 2.943 9.542 7a11.973 11.973 0 01-1.676 3.051" />
                                            </svg>
                                        </button>
                                    </div>

                                    @error('password')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                    <p class="mt-1 text-xs text-gray-500">
                                        Por defecto se usa el DNI del empleado como contraseña inicial. El usuario deberá cambiarla al iniciar sesión.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Rol sugerido --}}
                        @if($empleado->cargo)
                            @php
                                $rolSugerido = \App\Support\CargoRoleMapper::roleForCargo($empleado->cargo->DescripcionCargo);
                            @endphp
                            <div class="mt-2 p-3 rounded-md bg-blue-50 border border-blue-100 text-xs text-blue-800">
                                <p class="font-medium mb-1">
                                    Rol sugerido para este empleado:
                                    <span class="font-semibold">
                                        {{ $rolSugerido ? ucfirst($rolSugerido) : 'Sin rol específico' }}
                                    </span>
                                </p>
                                <p class="text-[11px]">
                                    Este rol determina a qué módulos del sistema podrá acceder (citas, pacientes, configuración, etc.).
                                </p>
                            </div>
                        @endif

                        <div class="mt-6 flex gap-3">
                            <button
                                type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium transition duration-200 shadow-sm"
                            >
                                ✅ Crear usuario
                            </button>
                            <a
                                href="{{ route('empleados.index') }}"
                                class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium transition duration-200"
                            >
                                ← Volver
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

   <script>
    document.addEventListener('DOMContentLoaded', function () {
        const input  = document.getElementById('password_input');
        const btn    = document.getElementById('toggle_password');
        const eyeOn  = document.getElementById('icon_eye_open');
        const eyeOff = document.getElementById('icon_eye_off');

        if (!input || !btn || !eyeOn || !eyeOff) {
            console.warn('Elementos del toggle de contraseña no encontrados');
            return;
        }

        btn.addEventListener('click', function (e) {
            e.preventDefault(); // evita enviar el form al hacer click
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            eyeOn.classList.toggle('hidden', !isPassword);
            eyeOff.classList.toggle('hidden', isPassword);
        });
    });
</script>

</x-app-layout>

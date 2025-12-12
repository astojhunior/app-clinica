<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Empleados y Doctores') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Formulario de registro -->
                    <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">👤 Registrar Nuevo Empleado</h3>

                        <form method="POST" action="{{ route('empleados.store') }}" id="formEmpleado">
                            @csrf

                            <!-- Datos Personales -->
                            <div class="mb-6">
                                <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">📋 Datos Personales</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                                        <input type="text" name="Dni" required maxlength="45" pattern="[0-9]+" value="{{ old('Dni') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('Dni')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                        <input type="text" name="Nombres" required maxlength="45" pattern="[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+" value="{{ old('Nombres') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('Nombres')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                        <input type="text" name="Apellidos" required maxlength="45" pattern="[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+" value="{{ old('Apellidos') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('Apellidos')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                        <input type="email" name="CorreElec" maxlength="45" value="{{ old('CorreElec') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('CorreElec')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                        <input type="text" name="FechaNacimiento" id="fecha_nacimiento" required placeholder="Seleccionar fecha" value="{{ old('FechaNacimiento') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('FechaNacimiento')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                                        <select name="Genero" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Seleccionar</option>
                                            <option value="Masculino" {{ old('Genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="Femenino" {{ old('Genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                            <option value="Otro" {{ old('Genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                        @error('Genero')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>

                          <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">💼 Datos Laborales</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso *</label>
                                    <input type="text" name="FechaIngreso" id="fecha_ingreso" required placeholder="Seleccionar fecha"
                                        value="{{ old('FechaIngreso') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('FechaIngreso')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>

                               <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cargo *</label>
                                <select name="Cargo_idCargo" id="cargo_select" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar</option>
                                    @foreach($cargos as $cargo)
                                        @php
                                            $texto = strtolower($cargo->DescripcionCargo);
                                            $esDoctor = str_contains($texto, 'doctor')
                                                        || str_contains($texto, 'médico')
                                                        || str_contains($texto, 'medico');
                                        @endphp
                                        <option value="{{ $cargo->IdCargo }}"
                                                data-es-doctor="{{ $esDoctor ? 1 : 0 }}"
                                                {{ old('Cargo_idCargo') == $cargo->IdCargo ? 'selected' : '' }}>
                                            {{ $cargo->DescripcionCargo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('Cargo_idCargo')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </div>


                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                    <select name="Estado" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="1" {{ old('Estado', 1) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('Estado') == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('Estado')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            {{-- Hidden para que el backend sepa si es doctor --}}
                            <input type="hidden" name="es_doctor" id="es_doctor" value="{{ old('es_doctor', 0) }}">
                        </div>


                                            <!-- Sección: Datos de Doctor -->
                            <div id="seccion_doctor" class="mb-8 hidden">
                                <div class="bg-white border border-slate-200 shadow-sm rounded-xl">
                                    <!-- Header -->
                                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-semibold text-slate-900 tracking-wide">
                                                Datos del doctor
                                            </h4>
                                            <p class="text-xs text-slate-500 mt-1">
                                                Completa la información profesional y los horarios de atención.
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
                                            Perfil médico
                                        </span>
                                    </div>

                                    <!-- Body -->
                                    <div class="px-5 py-4 space-y-6">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <!-- Número de colegiatura -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                                                    Número de colegiatura *
                                                </label>
                                                <input
                                                    type="text"
                                                    name="Numero_Colegiatura"
                                                    id="numero_colegiatura"
                                                    maxlength="45"
                                                    value="{{ old('Numero_Colegiatura') }}"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                                        focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                                                        transition-colors"
                                                    placeholder="Ej. CMP 123456"
                                                >
                                                @error('Numero_Colegiatura')
                                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <!-- Especialidad -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                                                    Especialidad *
                                                </label>
                                                <select
                                                    name="Especialidad_idEspecialidad"
                                                    id="especialidad_select"
                                                    class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                                        focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                                                        transition-colors"
                                                >
                                                    <option value="">Seleccionar especialidad</option>
                                                    @foreach($especialidades as $especialidad)
                                                        <option value="{{ $especialidad->IdEspecialidad }}">
                                                            {{ $especialidad->DescripcionEspe }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('Especialidad_idEspecialidad')
                                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Horarios -->
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <label class="block text-xs font-medium text-slate-600">
                                                        Horarios de atención *
                                                    </label>
                                                    <p class="text-xs text-slate-500 mt-0.5">
                                                        Define uno o varios bloques de días y horas en los que el doctor atiende.
                                                    </p>
                                                </div>
                                                <button
                                                    type="button"
                                                    id="add-horario"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5
                                                        text-xs font-medium text-white hover:bg-indigo-700 focus:outline-none
                                                        focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                                >
                                                    <span class="text-base leading-none">＋</span>
                                                    Agregar horario
                                                </button>
                                            </div>

                                            <!-- Aquí se inyectan los bloques de horario -->
                                            <div id="horarios-container" class="space-y-3"></div>

                                            <!-- Plantilla de bloque de horario -->
                                            <template id="horario-template">
                                                <!-- Bloque de horario (item) -->
                                                <div class="horario-item rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 shadow-sm space-y-3">
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-[11px] font-semibold text-indigo-700">
                                                                H
                                                            </span>
                                                            <p class="text-xs font-medium text-slate-700">
                                                                Bloque de horario
                                                            </p>
                                                        </div>
                                                        <button
                                                            type="button"
                                                            class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-[11px] font-medium text-red-600 hover:bg-red-100 btn-remove-horario"
                                                        >
                                                            ✕ Quitar
                                                        </button>
                                                    </div>

                                                    <div>
                                                        <p class="text-[11px] font-medium text-slate-600 mb-1">
                                                            Días de atención *
                                                        </p>
                                                        <div class="flex flex-wrap gap-2 text-xs">
                                                            @php
                                                                $dias = [
                                                                    ['Lun','lun'],
                                                                    ['Mar','mar'],
                                                                    ['Mié','mie'],
                                                                    ['Jue','jue'],
                                                                    ['Vie','vie'],
                                                                    ['Sáb','sab'],
                                                                    ['Dom','dom'],
                                                                ];
                                                            @endphp

                                                            @foreach($dias as [$label, $value])
                                                                <label class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1 cursor-pointer hover:border-indigo-400 hover:text-indigo-700">
                                                                    <input
                                                                        type="checkbox"
                                                                        name="__NAME__DIAS__"
                                                                        value="{{ $value }}"
                                                                        class="h-3 w-3 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                                    >
                                                                    <span>{{ $label }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                                                Hora inicio *
                                                            </label>
                                                            <input
                                                                type="time"
                                                                name="__NAME__HORA_INICIO__"
                                                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm
                                                                    focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                                            >
                                                        </div>
                                                        <div>
                                                            <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                                                Hora fin *
                                                            </label>
                                                            <input
                                                                type="time"
                                                                name="__NAME__HORA_FIN__"
                                                                class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm
                                                                    focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Biografía -->
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1.5">
                                                Biografía
                                            </label>
                                            <textarea
                                                name="Biografia"
                                                rows="3"
                                                maxlength="200"
                                                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                                    focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                                                    transition-colors resize-none"
                                                placeholder="Resumen breve de experiencia, formación y enfoque de atención."
                                            >{{ old('Biografia') }}</textarea>
                                            <div class="mt-1 flex justify-between text-[11px] text-slate-400">
                                                <span>Máx. 200 caracteres</span>
                                            </div>
                                            @error('Biografia')
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Botones -->
                            <div class="mt-6 flex flex-wrap gap-3">
                                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium transition duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                    ✅ Registrar Empleado
                                </button>
                                <button type="reset" class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium transition duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                    🔄 Limpiar Campos
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Buscador -->
                    <div class="mb-6 p-4 bg-white border border-gray-200 rounded-lg">
                        <form method="GET" action="{{ route('empleados.index') }}" class="flex flex-wrap gap-2 items-end">
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                                <input type="text" name="search" placeholder="DNI, nombre o apellido" value="{{ request('search') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="min-w-[150px]">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Registros</label>
                                <select name="per_page" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 por página</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 por página</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 por página</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 por página</option>
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">🔍 Buscar</button>
                            <a href="{{ route('empleados.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-200">🔄 Limpiar</a>
                        </form>
                    </div>

                    <!-- Tabla de empleados -->
                    <div class="overflow-x-auto rounded-lg border border-gray-300">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">DNI</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nombre Completo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Cargo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Especialidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($empleados as $empleado)
                                <tr class="hover:bg-blue-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $empleado->Dni }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $empleado->Nombres }} {{ $empleado->Apellidos }}
                                        @if($empleado->doctor)
                                            <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">🩺 Doctor</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $empleado->cargo->DescripcionCargo ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($empleado->doctor)
                                            {{ $empleado->doctor->especialidad->DescripcionEspe ?? 'N/A' }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $empleado->Estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $empleado->Estado ? '✅ Activo' : '🔴 Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('empleados.edit', $empleado->IdEmpleado) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">✏️ Editar</a>
                                        @if($empleado->user)
                                                <span class="text-green-600 mr-3">
                                                    👤 Usuario creado
                                                </span>
                                            @else
                                                <a href="{{ route('empleados.crear-usuario', $empleado->IdEmpleado) }}"
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                                    👤 Crear usuario
                                                </a>
                                            @endif
                                        <form method="POST" action="{{ route('empleados.destroy', $empleado->IdEmpleado) }}" class="inline-block">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro de desactivar este empleado?')">🔴 Desactivar</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay empleados registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $empleados->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="{{ asset('js/empleados-form.js') }}"></script>
</x-app-layout>

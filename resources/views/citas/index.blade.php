<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Citas Médicas
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <!-- Select2 para búsqueda incremental -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

            <!-- LAYOUT DE 2 COLUMNAS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- ========================================== -->
                <!-- COLUMNA 1: FORMULARIO DE REGISTRO DE CITA -->
                <!-- ========================================== -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-6 text-gray-800 border-b pb-3">
                            Registrar Nueva Cita
                        </h3>

                        <form method="POST" action="{{ route('citas.store') }}" id="formCita">
                            @csrf

                            <!-- Selección de Paciente con Búsqueda Incremental -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Paciente
                                </label>
                                <select name="Paciente_idPaciente" id="paciente-select" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        style="width: 100%">
                                    <option value="">Buscar por DNI o nombre...</option>
                                    @foreach($pacientes as $paciente)
                                        <option value="{{ $paciente->IdPaciente }}"
                                                data-dni="{{ $paciente->Dni }}"
                                                {{ old('Paciente.idPaciente') == $paciente->IdPaciente ? 'selected' : '' }}>
                                            {{ $paciente->Dni }} - {{ $paciente->Nombres }} {{ $paciente->Apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('Paciente_idPaciente')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Escribe para buscar por DNI o nombre del paciente
                                </p>
                            </div>

                            <!-- Fecha de la Cita -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Fecha de la Cita
                                </label>
                                <input type="text" name="FechaCita" id="fecha-cita" required
                                       placeholder="Seleccionar fecha"
                                       value="{{ old('FechaCita') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('FechaCita')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                          <!-- Hora de Inicio -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Hora de Inicio
                                </label>
                                <input type="time" name="HoraInicio" id="hora-inicio" required
                                    value="{{ old('HoraInicio') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('HoraInicio')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                         {{-- Hora de Fin ahora se deja nula al crear --}}
                        <input type="hidden" name="HoraFin" value="">

                          <!-- Tipo de Cita -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Tipo de Cita
                                    </label>
                                    <select name="Tipo_Cita_idTipo_Cita" id="tipo-cita" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Seleccionar tipo de cita</option>
                                        @foreach($tiposCita as $tipo)
                                            <option value="{{ $tipo->IdTipo_Cita }}"
                                                    {{ old('Tipo_Cita_idTipo_Cita') == $tipo->IdTipo_Cita ? 'selected' : '' }}>
                                                {{ $tipo->Descripcion }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('Tipo_Cita_idTipo_Cita')
                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                    @enderror
                                </div>


                            <!-- Modalidad (NUEVO) -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Modalidad
                                </label>
                                <select name="Modalidad" id="modalidad" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar modalidad</option>
                                    <option value="Presencial" {{ old('Modalidad') == 'Presencial' ? 'selected' : '' }}>
                                        🏥 Presencial
                                    </option>
                                    <option value="Virtual" {{ old('Modalidad') == 'Virtual' ? 'selected' : '' }}>
                                        💻 Virtual
                                    </option>
                                </select>
                                @error('Modalidad')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Doctor Seleccionado (Hidden) -->
                            <input type="hidden" name="Doctor_idDoctores" id="doctor-seleccionado"
                                   value="{{ old('Doctor_idDoctores') }}" required>

                            <!-- Campos temporales para persistir nombre y especialidad -->
                            <input type="hidden" name="nombredoctortemp" id="nombre-doctor-temp"
                                   value="{{ old('nombredoctortemp') }}">
                            <input type="hidden" name="especialidaddoctortemp" id="especialidad-doctor-temp"
                                   value="{{ old('especialidaddoctortemp') }}">

                            <!-- Cuadro de Doctor Seleccionado -->
                            <div id="doctor-info" class="{{ old('Doctor_idDoctores') ? '' : 'hidden' }} mb-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Doctor Seleccionado</p>
                                        <p class="text-lg font-semibold text-green-800" id="nombre-doctor-text">
                                            {{ old('nombredoctortemp') }}
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1" id="especialidad-doctor-text">
                                            {{ old('especialidaddoctortemp') }}
                                        </p>
                                    </div>
                                    <button type="button" onclick="limpiarSeleccionDoctor()"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Cambiar
                                    </button>
                                </div>
                            </div>

                            <!-- Warning cuando no hay doctor seleccionado -->
                            <div id="doctor-warning" class="{{ old('Doctor_idDoctores') ? 'hidden' : '' }} mb-6 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <p class="text-sm text-yellow-800">
                                    ⚠️ Por favor, seleccione un doctor de la lista de la derecha
                                </p>
                            </div>

                            @error('Doctor_idDoctores')
                                <span class="text-red-500 text-xs mt-1 block mb-4">{{ $message }}</span>
                            @enderror

                            <!-- Motivo de Cita (RENOMBRADO de Observaciones) -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Motivo de la Cita
                                </label>
                                <textarea name="MotivoCita" id="motivo-cita" maxlength="200" rows="3"
                                          placeholder="Describa el motivo de la consulta..."
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('MotivoCita') }}</textarea>
                                @error('MotivoCita')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">
                                    Máximo 200 caracteres
                                </p>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="flex gap-3">
                                <button type="submit"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                    ✓ Registrar Cita
                                </button>
                                <button type="reset" onclick="resetearFormulario()"
                                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition-all shadow-md hover:shadow-lg">
                                    ✗ Limpiar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- COLUMNA 2: LISTA DE DOCTORES DISPONIBLES  -->
                <!-- ========================================== -->
               <div class="bg-white shadow rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800">
                        Doctores Disponibles
                    </h3>
                    {{-- Botón Ver horarios --}}
                            <a href="{{ route('horarios.index') }}"
                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium
                                    text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200
                                    transition-colors duration-150">
                                {{-- Icono de ojito con SVG --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <span>Ver horarios</span>
                            </a>
                        </div>
                                            <!-- Filtros -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <p class="text-sm font-semibold text-blue-800 mb-3">🔍 Filtrar Doctores</p>

                            <div class="grid grid-cols-1 gap-3">
                                <!-- Filtro por Especialidad -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Especialidad</label>
                                    <select id="filtro-especialidad"
                                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Todas las especialidades</option>
                                        @foreach($doctores->unique('especialidad.IdEspecialidad')->filter(fn($d) => $d->especialidad) as $doctor)
                                            <option value="{{ $doctor->especialidad->IdEspecialidad }}">
                                                {{ $doctor->especialidad->DescripcionEspe }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filtro por Día -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Día de Atención</label>
                                    <select id="filtro-dia"
                                            class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Todos los días</option>
                                        <option value="Lun">Lunes</option>
                                        <option value="Mar">Martes</option>
                                        <option value="Mié">Miércoles</option>
                                        <option value="Jue">Jueves</option>
                                        <option value="Vie">Viernes</option>
                                        <option value="Sáb">Sábado</option>
                                        <option value="Dom">Domingo</option>
                                    </select>
                                </div>
                            </div>

                            <button type="button" onclick="filtrarDoctores()"
                                    class="mt-3 w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                                Aplicar Filtros
                            </button>
                        </div>

                        <!-- Lista de Doctores con Paginación -->
                        <div id="doctores-container" class="space-y-3 max-h-[600px] overflow-y-auto pr-2">
                            @foreach($doctores as $doctor)
                                <div class="doctor-card border-2 rounded-lg p-4 hover:shadow-lg transition-all cursor-pointer bg-white hover:bg-blue-50"
                                     data-doctor-id="{{ $doctor->IdDoctores }}"
                                     data-empleado-id="{{ $doctor->empleado->IdEmpleado }}"
                                     data-especialidad="{{ $doctor->especialidad->IdEspecialidad ?? '' }}"
                                     data-dias="{{ $doctor->horarios->pluck('DiaSemana')->flatten()->unique()->implode(',') }}"
                                     onclick="seleccionarDoctor('{{ $doctor->IdDoctores }}', 'Dr. {{ $doctor->empleado->Nombres }} {{ $doctor->empleado->Apellidos }}', '{{ $doctor->especialidad->DescripcionEspe ?? 'Sin especialidad' }}', '{{ $doctor->empleado->IdEmpleado }}')">

                                    <!-- Cabecera -->
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex items-center flex-1">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-lg font-bold mr-3">
                                                {{ substr($doctor->empleado->Nombres, 0, 1) }}{{ substr($doctor->empleado->Apellidos, 0, 1) }}
                                            </div>
                                            <div class="flex-1">
                                                <h5 class="font-bold text-gray-900 text-sm leading-tight">
                                                    Dr. {{ $doctor->empleado->Nombres }} {{ $doctor->empleado->Apellidos }}
                                                </h5>
                                                @if($doctor->especialidad)
                                                    <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full inline-block mt-1">
                                                        {{ $doctor->especialidad->DescripcionEspe }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Indicador de selección -->
                                        <div class="selected-indicator hidden">
                                            <span class="text-green-600 text-2xl">✓</span>
                                        </div>
                                    </div>

                                    <!-- CMP -->
                                    @if($doctor->NumeroColegiatura)
                                        <div class="text-xs text-gray-600 mb-3 flex items-center">
                                            <span class="font-medium">CMP:</span>
                                            <span class="ml-1">{{ $doctor->NumeroColegiatura }}</span>
                                        </div>
                                    @endif

                                    <!-- Horarios del Doctor -->
                                    <div class="border-t pt-3">
                                        <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center">
                                            <span class="mr-1">🕐</span> Horarios de Atención
                                        </p>

                                        @if($doctor->horarios && $doctor->horarios->count() > 0)
                                            <div class="space-y-2">
                                                @foreach($doctor->horarios as $horario)
                                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-3 rounded-lg border border-blue-200">
                                                        <!-- Días de atención - MÁS VISIBLES -->
                                                        <div class="mb-2">
                                                            <p class="text-xs font-semibold text-gray-600 mb-1">Días</p>
                                                            <div class="flex flex-wrap gap-1">
                                                                @php
                                                                    $dias = explode(',', $horario->DiaSemana);
                                                                @endphp
                                                                @foreach($dias as $dia)
                                                                    <span class="px-2.5 py-1 bg-indigo-600 text-white rounded-md text-xs font-bold shadow-sm">
                                                                        {{ trim($dia) }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <!-- Horario -->
                                                        <div class="flex items-center text-sm">
                                                            <span class="text-lg mr-1">🕐</span>
                                                            <span class="font-bold text-gray-800">
                                                                {{ \Carbon\Carbon::parse($horario->HoraInicio)->format('h:i A') }}
                                                            </span>
                                                            <span class="mx-1 text-gray-500">-</span>
                                                            <span class="font-bold text-gray-800">
                                                                {{ \Carbon\Carbon::parse($horario->HoraFin)->format('h:i A') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="bg-gray-100 p-3 rounded text-center">
                                                <p class="text-xs text-gray-500 italic">Sin horarios configurados</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Biografía si existe -->
                                    @if($doctor->Biografia)
                                        <div class="mt-3 pt-3 border-t">
                                            <p class="text-xs text-gray-600 line-clamp-2">
                                                {{ $doctor->Biografia }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <!-- Mensaje cuando no hay resultados -->
                            <div id="mensaje-no-doctores" class="hidden text-center py-8 text-gray-500">
                                <p class="text-sm">No se encontraron doctores con los filtros seleccionados</p>
                            </div>
                        </div>

                        <!-- Contador de doctores -->
                        <div class="mt-4 pt-4 border-t">
                            <div class="text-xs text-gray-600 text-center">
                                Mostrando {{ $doctores->count() }} doctores disponibles
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================== -->
            <!-- TABLA DE CITAS REGISTRADAS                -->
            <!-- ========================================== -->
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <!-- Filtros de búsqueda -->
                    <form method="GET" action="{{ route('citas.index') }}" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-md font-semibold text-gray-700 mb-3">🔍 Buscar Citas Registradas</h4>

                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Búsqueda general -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                                <input type="text" name="search"
                                       placeholder="Paciente o doctor..."
                                       value="{{ request('search') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Filtro por estado (ACTUALIZADO) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                <select name="estado"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos</option>
                                    <option value="Programada" {{ request('estado') == 'Programada' ? 'selected' : '' }}>Programada</option>
                                    <option value="Confirmada" {{ request('estado') == 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                                    <option value="Cancelada" {{ request('estado') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                                    <option value="Completada" {{ request('estado') == 'Completada' ? 'selected' : '' }}>Completada</option>
                                </select>
                            </div>

                            <!-- Filtro por fecha -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                <input type="date" name="fecha"
                                       value="{{ request('fecha') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Filtro por doctor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                                <select name="empleado"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Todos</option>
                                    @foreach($doctores as $doctor)
                                        <option value="{{ $doctor->IdDoctores }}"
                                                {{ request('empleado') == $doctor->IdDoctores ? 'selected' : '' }}>
                                            Dr. {{ $doctor->empleado->Nombres }} {{ $doctor->empleado->Apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Registros por página -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Registros</label>
                                <select name="perpage"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="10" {{ request('perpage') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('perpage') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('perpage') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('perpage') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 flex gap-4">
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition-colors">
                                🔍 Buscar
                            </button>
                            <a href="{{ route('citas.index') }}"
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-md transition-colors">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <!-- Tabla de citas -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Doctor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Cita</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modalidad</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Pago</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($citas as $cita)
                                    <tr class="hover:bg-gray-50">
                                        <!-- Fecha y Hora (ACTUALIZADO con HoraInicio y HoraFin) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">
                                                {{ \Carbon\Carbon::parse($cita->FechaCita)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ \Carbon\Carbon::parse($cita->HoraInicio)->format('h:i A') }} -
                                                {{ \Carbon\Carbon::parse($cita->HoraFin)->format('h:i A') }}
                                            </div>
                                        </td>

                                        <!-- Paciente -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">
                                                {{ $cita->paciente->Nombres }} {{ $cita->paciente->Apellidos }}
                                            </div>
                                            <div class="text-gray-500 text-xs">
                                                {{ $cita->paciente->Dni }}
                                            </div>
                                        </td>

                                        <!-- Doctor -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-medium">
                                                Dr. {{ $cita->doctor->empleado->Nombres }} {{ $cita->doctor->empleado->Apellidos }}
                                            </div>
                                            @if($cita->doctor->especialidad)
                                                <div class="text-gray-500 text-xs">
                                                    {{ $cita->doctor->especialidad->DescripcionEspe }}
                                                </div>
                                            @endif
                                        </td>

                                        <!-- Tipo de Cita -->
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $cita->tipoCita->Descripcion ?? '-' }}
                                        </td>

                                        <!-- Modalidad (NUEVO) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($cita->Modalidad == 'Presencial')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    🏥 Presencial
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    💻 Virtual
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Costo (NUEVO) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <div class="font-bold text-green-700">
                                                S/ {{ number_format($cita->CostoTotal, 2) }}
                                            </div>
                                        </td>

                                        <!-- Estado de Pago (NUEVO) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($cita->EstadoPago == 'Pagado')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Pagado
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    ⏳ Pendiente
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Estado (ACTUALIZADO con nuevos valores) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($cita->Estado == 'Programada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    📅 Programada
                                                </span>
                                            @elseif($cita->Estado == 'Confirmada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Confirmada
                                                </span>
                                            @elseif($cita->Estado == 'Cancelada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    ✗ Cancelada
                                                </span>
                                            @elseif($cita->Estado == 'Completada')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    ✔ Completada
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    {{ $cita->Estado }}
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex gap-2 flex-wrap">
                                                <!-- Ver Detalles -->
                                                <a href="{{ route('citas.show', $cita->IdCita) }}"
                                                   class="text-blue-600 hover:text-blue-900"
                                                   title="Ver detalles">
                                                    👁️
                                                </a>

                                                <!-- Editar -->
                                                @if($cita->Estado != 'Completada' && $cita->Estado != 'Cancelada')
                                                    <a href="{{ route('citas.edit', $cita->IdCita) }}"
                                                       class="text-indigo-600 hover:text-indigo-900"
                                                       title="Editar">
                                                        ✏️
                                                    </a>
                                                @endif

                                                <!-- Confirmar -->
                                                @if($cita->Estado == 'Programada')
                                                    <form method="POST" action="{{ route('citas.confirmar', $cita->IdCita) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit"
                                                                class="text-green-600 hover:text-green-900"
                                                                title="Confirmar cita"
                                                                onclick="return confirm('¿Confirmar esta cita?')">
                                                            ✓
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Cancelar -->
                                                @if($cita->Estado != 'Cancelada' && $cita->Estado != 'Completada')
                                                    <form method="POST" action="{{ route('citas.cancelar', $cita->IdCita) }}" class="inline-block">
                                                        @csrf
                                                        <button type="submit"
                                                                onclick="return confirm('¿Está seguro de cancelar esta cita?')"
                                                                class="text-red-600 hover:text-red-900"
                                                                title="Cancelar">
                                                            🚫
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Eliminar -->
                                                <form method="POST" action="{{ route('citas.destroy', $cita->IdCita) }}" class="inline-block">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit"
                                                            onclick="return confirm('¿Está seguro de eliminar esta cita? Esta acción no se puede deshacer.')"
                                                            class="text-red-600 hover:text-red-900"
                                                            title="Eliminar">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                            No hay citas registradas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $citas->appends([
                            'search' => request('search'),
                            'estado' => request('estado'),
                            'fecha' => request('fecha'),
                            'empleado' => request('empleado'),
                            'perpage' => request('perpage')
                        ])->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts externos -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- JavaScript externo -->
    <script src="{{ asset('js/citas-scripts.js') }}"></script>

    <style>
        /* Estilos para las tarjetas de doctores */
        .doctor-card {
            transition: all 0.3s ease;
            border-width: 2px;
        }

        .doctor-card:hover {
            transform: translateY(-2px);
            border-color: #6366f1 !important;
        }

        .doctor-card.border-green-500 {
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Estilos para Select2 */
        .select2-container--default .select2-selection--single {
            height: 42px;
            border-color: #d1d5db;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            padding-left: 12px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }

        /* Scroll personalizado para la lista de doctores */
        #doctores-container::-webkit-scrollbar {
            width: 8px;
        }

        #doctores-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        #doctores-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        #doctores-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

</x-app-layout>


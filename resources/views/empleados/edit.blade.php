<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Empleado') }}
        </h2>
    </x-slot>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
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

                    <form method="POST" action="{{ route('empleados.update', $empleado->IdEmpleado) }}" id="formEmpleado">
                        @csrf
                        @method('PUT')
                        <!-- Datos personales -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">📋 Datos Personales</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div><!-- DNI -->
                                    <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                                    <input type="text" name="Dni" required maxlength="45" pattern="[0-9]+" value="{{ old('Dni', $empleado->Dni) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                                    @error('Dni')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div><!-- Nombres -->
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                    <input type="text" name="Nombres" required maxlength="45" pattern="[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+" value="{{ old('Nombres', $empleado->Nombres) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                                    @error('Nombres')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div><!-- Apellidos -->
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                    <input type="text" name="Apellidos" required maxlength="45" pattern="[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+" value="{{ old('Apellidos', $empleado->Apellidos) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                                    @error('Apellidos')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div><!-- Correo -->
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                    <input type="email" name="CorreElec" maxlength="45" value="{{ old('CorreElec', $empleado->CorreElec) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                                    @error('CorreElec')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div><!-- Fecha de Nacimiento -->
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                    <input type="text" name="FechaNacimiento" id="fecha_nacimiento" required value="{{ old('FechaNacimiento', date('Y-m-d', strtotime($empleado->FechaNacimiento))) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                                    @error('FechaNacimiento')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div><!-- Género -->
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                                    <select name="Genero" required class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">Seleccionar</option>
                                        <option value="Masculino" {{ old('Genero', $empleado->Genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Femenino" {{ old('Genero', $empleado->Genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                        <option value="Otro" {{ old('Genero', $empleado->Genero) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('Genero')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Datos Laborales -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">💼 Datos Laborales</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso *</label>
                                    <input type="text" name="FechaIngreso" id="fecha_ingreso" required value="{{ old('FechaIngreso', date('Y-m-d', strtotime($empleado->FechaIngreso))) }}" class="w-full rounded-md border-gray-300 shadow-sm">
                                    @error('FechaIngreso')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cargo *</label>
                                    <select name="Cargo_idCargo" id="cargo_select" required class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">Seleccionar</option>
                                        @foreach($cargos as $cargo)
                                            <option value="{{ $cargo->IdCargo }}" {{ old('Cargo_idCargo', $empleado->Cargo_idCargo) == $cargo->IdCargo ? 'selected' : '' }}>{{ $cargo->DescripcionCargo }}</option>
                                        @endforeach
                                    </select>
                                    @error('Cargo_idCargo')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                    <select name="Estado" required class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="1" {{ old('Estado', $empleado->Estado) == 1 ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ old('Estado', $empleado->Estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                    </select>
                                    @error('Estado')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>
                        {{-- Datos de Doctor (solo si es doctor) --}}
                        @if($empleado->doctor)
                            <div class="mb-8 bg-white border border-slate-200 shadow-sm rounded-xl">
                                {{-- Header --}}
                                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-900 tracking-wide">
                                            Datos del doctor
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-1">
                                            Actualiza la información profesional y los horarios de atención.
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700">
                                        Perfil médico
                                    </span>
                                </div>

                                {{-- Body --}}
                                <div class="px-5 py-4 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        {{-- Número de colegiatura --}}
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1.5">
                                                Número de colegiatura *
                                            </label>
                                            <input
                                                type="text"
                                                name="Numero_Colegiatura"
                                                maxlength="45"
                                                value="{{ old('Numero_Colegiatura', $empleado->doctor->Numero_Colegiatura) }}"
                                                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                                    focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                                                    transition-colors"
                                            >
                                            @error('Numero_Colegiatura')
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Especialidad --}}
                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1.5">
                                                Especialidad *
                                            </label>
                                            <select
                                                name="Especialidad_idEspecialidad"
                                                class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm
                                                    focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500
                                                    transition-colors"
                                            >
                                                <option value="">Seleccionar especialidad</option>
                                                @foreach($especialidades as $especialidad)
                                                    <option value="{{ $especialidad->IdEspecialidad }}"
                                                        {{ old('Especialidad_idEspecialidad', $empleado->doctor->Especialidad_idEspecialidad) == $especialidad->IdEspecialidad ? 'selected' : '' }}>
                                                        {{ $especialidad->DescripcionEspe }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('Especialidad_idEspecialidad')
                                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Horarios actuales --}}
                                    <div class="space-y-3">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-600">
                                                    Horarios actuales
                                                </label>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    Edita o elimina los bloques de horario ya registrados para este médico.
                                                </p>
                                            </div>
                                        </div>

                                        @if($empleado->doctor->horarios && $empleado->doctor->horarios->count())
                                            <div id="horarios-edit-container" class="space-y-3">
                                                @foreach($empleado->doctor->horarios as $horario)
                                                    @php
                                                        $diasSeleccionados = collect(explode(',', $horario->DiaSemana))
                                                            ->map(function ($d) { return trim($d); })
                                                            ->filter()
                                                            ->values()
                                                            ->all();

                                                        $diasConfig = [
                                                            ['Lun','lun'],
                                                            ['Mar','mar'],
                                                            ['Mié','mie'],
                                                            ['Jue','jue'],
                                                            ['Vie','vie'],
                                                            ['Sáb','sab'],
                                                            ['Dom','dom'],
                                                        ];
                                                    @endphp

                                                    <div class="horario-edit-item rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 shadow-sm space-y-3">
                                                        <input type="hidden" name="horarios_existentes[{{ $horario->IdHorario }}][id]" value="{{ $horario->IdHorario }}">

                                                        <div class="flex items-center justify-between">
                                                            <p class="text-xs font-medium text-slate-700">
                                                                Bloque de horario existente
                                                            </p>
                                                            <button type="button"
                                                                    class="eliminar-horario-existente inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-[11px] font-medium text-red-600 hover:bg-red-100">
                                                                ✕ Eliminar
                                                            </button>
                                                        </div>

                                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                            {{-- Días --}}
                                                            <div class="md:col-span-1">
                                                                <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                                                    Días
                                                                </label>
                                                                <div class="flex flex-wrap gap-2 text-xs">
                                                                    @foreach($diasConfig as [$label, $value])
                                                                        <label class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1 cursor-pointer hover:border-indigo-400 hover:text-indigo-700">
                                                                            <input
                                                                                type="checkbox"
                                                                                name="horarios_existentes[{{ $horario->IdHorario }}][dias][]"
                                                                                value="{{ $value }}"
                                                                                class="h-3 w-3 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                                                                {{ in_array($value, $diasSeleccionados) ? 'checked' : '' }}
                                                                            >
                                                                            <span>{{ $label }}</span>
                                                                        </label>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            {{-- Hora inicio --}}
                                                            <div>
                                                                <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                                                    Hora inicio
                                                                </label>
                                                                <input
                                                                    type="time"
                                                                    name="horarios_existentes[{{ $horario->IdHorario }}][hora_inicio]"
                                                                    value="{{ \Carbon\Carbon::parse($horario->HoraInicio)->format('H:i') }}"
                                                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm
                                                                        focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                                                />
                                                            </div>

                                                            {{-- Hora fin --}}
                                                            <div>
                                                                <label class="block text-[11px] font-medium text-slate-600 mb-1">
                                                                    Hora fin
                                                                </label>
                                                                <input
                                                                    type="time"
                                                                    name="horarios_existentes[{{ $horario->IdHorario }}][hora_fin]"
                                                                    value="{{ \Carbon\Carbon::parse($horario->HoraFin)->format('H:i') }}"
                                                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm
                                                                        focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-xs text-slate-400 mt-1">
                                                Este médico aún no tiene horarios registrados.
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Agregar nuevos horarios --}}
                                    <div class="space-y-3 mt-4">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-xs font-medium text-slate-600">
                                                Agregar nuevo horario
                                            </label>
                                            <button
                                                id="btn-agregar-horario"
                                                type="button"
                                                class="inline-flex items-center gap-1 rounded-lg bg-indigo-600 px-3 py-1.5
                                                    text-xs font-medium text-white hover:bg-indigo-700 focus:outline-none
                                                    focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                            >
                                                <span class="text-base leading-none">＋</span>
                                                Nuevo horario
                                            </button>
                                        </div>

                                        <div id="horarios-nuevos-container" class="space-y-3"></div>
                                        <input type="hidden" name="horarios_eliminados" id="horarios_eliminados">
                                    </div>

                                    {{-- Biografía --}}
                                    <div class="mt-5">
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
                                        >{{ old('Biografia', $empleado->doctor->Biografia) }}</textarea>
                                        <div class="mt-1 flex justify-between text-[11px] text-slate-400">
                                            <span>Máx. 200 caracteres</span>
                                        </div>
                                        @error('Biografia')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-6 p-4 bg-gray-100 border border-gray-300 rounded-lg text-center">
                                <p class="text-gray-600">ℹ️ Este empleado no tiene perfil de doctor asociado.</p>
                            </div>
                        @endif


                        <div class="mt-6 flex gap-3">
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition duration-200">
                                ✅ Actualizar Empleado
                            </button>
                            <a href="{{ route('empleados.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium transition duration-200">← Volver</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts (coloca todos aquí al final) -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="{{ asset('js/empleados-form.js') }}"></script>
</x-app-layout>

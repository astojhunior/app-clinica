<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nueva Cita') }}
        </h2>
    </x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <div class="mb-4">
                        <a href="{{ route('citas.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Volver al listado
                        </a>
                    </div>

                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-semibold mb-6 text-gray-800">📅 Nueva Cita Médica</h3>

                    <form method="POST" action="{{ route('citas.store') }}" id="formCita">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Paciente -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Paciente *</label>
                                <select name="Paciente_IdPaciente" id="paciente_select" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar paciente</option>
                                    @foreach($pacientes as $paciente)
                                        <option value="{{ $paciente->IdPaciente }}" {{ old('Paciente_IdPaciente') == $paciente->IdPaciente ? 'selected' : '' }}>
                                            {{ $paciente->Dni }} - {{ $paciente->Nombres }} {{ $paciente->Apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('Paciente_IdPaciente')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Seleccione el paciente que asistirá a la cita</p>
                            </div>

                            <!-- Doctor -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Doctor *</label>
                                <select name="Doctor_IdDoctor" id="doctor_select" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar doctor</option>
                                    @foreach($doctores as $doctor)
                                        <option value="{{ $doctor->IdDoctor }}" {{ old('Doctor_IdDoctor') == $doctor->IdDoctor ? 'selected' : '' }}>
                                            Dr. {{ $doctor->empleado->Nombres }} {{ $doctor->empleado->Apellidos }}
                                            @if($doctor->especialidad)
                                                - {{ $doctor->especialidad->NombreEspecialidad }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('Doctor_IdDoctor')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Seleccione el médico que atenderá</p>
                            </div>

                            <!-- Fecha -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                                <input type="text" name="Fecha" id="fecha_cita" required placeholder="Seleccionar fecha" value="{{ old('Fecha') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Fecha')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Solo fechas desde hoy en adelante</p>
                            </div>

                            <!-- Hora -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                                <input type="time" name="Hora" id="hora_cita" required value="{{ old('Hora') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Hora')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Formato 24 horas (ej: 14:30)</p>
                            </div>

                            <!-- Duración -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Duración (minutos) *</label>
                                <select name="Duracion" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="15" {{ old('Duracion') == 15 ? 'selected' : '' }}>15 minutos</option>
                                    <option value="30" {{ old('Duracion', 30) == 30 ? 'selected' : '' }}>30 minutos</option>
                                    <option value="45" {{ old('Duracion') == 45 ? 'selected' : '' }}>45 minutos</option>
                                    <option value="60" {{ old('Duracion') == 60 ? 'selected' : '' }}>60 minutos</option>
                                    <option value="90" {{ old('Duracion') == 90 ? 'selected' : '' }}>90 minutos</option>
                                    <option value="120" {{ old('Duracion') == 120 ? 'selected' : '' }}>120 minutos</option>
                                </select>
                                @error('Duracion')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Tiempo estimado de la consulta</p>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                <select name="Estado" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Pendiente" {{ old('Estado', 'Pendiente') == 'Pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                    <option value="Confirmada" {{ old('Estado') == 'Confirmada' ? 'selected' : '' }}>✅ Confirmada</option>
                                    <option value="En curso" {{ old('Estado') == 'En curso' ? 'selected' : '' }}>🟢 En curso</option>
                                    <option value="Completada" {{ old('Estado') == 'Completada' ? 'selected' : '' }}>✔️ Completada</option>
                                    <option value="Cancelada" {{ old('Estado') == 'Cancelada' ? 'selected' : '' }}>❌ Cancelada</option>
                                </select>
                                @error('Estado')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Motivo -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la Cita</label>
                                <input type="text" name="Motivo" maxlength="200" value="{{ old('Motivo') }}" placeholder="Ej: Consulta general, Seguimiento, Control, Chequeo..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Motivo')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Razón principal de la consulta (opcional)</p>
                            </div>

                            <!-- Observaciones -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea name="Observaciones" rows="4" placeholder="Notas adicionales, síntomas previos, medicamentos actuales, alergias, etc..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('Observaciones') }}</textarea>
                                @error('Observaciones')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Información adicional relevante para la cita (opcional)</p>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-8 flex gap-4">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-md transition-colors shadow-sm">
                                ✅ Registrar Cita
                            </button>
                            <button type="reset" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-3 px-6 rounded-md transition-colors">
                                🔄 Limpiar Campos
                            </button>
                            <a href="{{ route('citas.index') }}" class="bg-red-500 hover:bg-red-600 text-white font-medium py-3 px-6 rounded-md transition-colors">
                                ❌ Cancelar
                            </a>
                        </div>
                    </form>

                    <!-- Información adicional -->
                    <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-blue-800 mb-2">💡 Información Importante</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li>• Verifique la disponibilidad del doctor antes de agendar</li>
                            <li>• El sistema validará conflictos de horario automáticamente</li>
                            <li>• Las citas solo pueden agendarse desde hoy en adelante</li>
                            <li>• Recuerde confirmar la cita con el paciente</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
    <script>
        // Inicializar Flatpickr para fecha
        flatpickr("#fecha_cita", {
            dateFormat: "Y-m-d",
            minDate: "today",
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            theme: "material_blue"
        });

        // Validación adicional del formulario
        document.getElementById('formCita').addEventListener('submit', function(e) {
            const paciente = document.getElementById('paciente_select').value;
            const doctor = document.getElementById('doctor_select').value;
            const fecha = document.getElementById('fecha_cita').value;
            const hora = document.getElementById('hora_cita').value;

            if (!paciente || !doctor || !fecha || !hora) {
                e.preventDefault();
                alert('Por favor complete todos los campos obligatorios marcados con *');
                return false;
            }
        });
    </script>
</x-app-layout>

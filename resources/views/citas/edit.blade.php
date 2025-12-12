<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Cita
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto">
            <form method="POST" action="{{ route('citas.update', $cita->IdCita) }}" class="bg-white rounded-xl shadow-sm p-8 border border-gray-200">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Paciente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paciente *</label>
                        <select name="Paciente_idPaciente" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                            <option value="">Seleccionar paciente</option>
                            @foreach($pacientes as $paciente)
                                <option value="{{ $paciente->IdPaciente }}" {{ $cita->Paciente_idPaciente == $paciente->IdPaciente ? 'selected' : '' }}>
                                    {{ $paciente->Dni }} - {{ $paciente->Nombres }} {{ $paciente->Apellidos }}
                                </option>
                            @endforeach
                        </select>
                        @error('Paciente_idPaciente') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Doctor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Doctor *</label>
                        <select name="Doctor_idDoctores" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                            <option value="">Seleccionar doctor</option>
                            @foreach($doctores as $doctor)
                                <option value="{{ $doctor->IdDoctores }}" {{ $cita->Doctor_idDoctores == $doctor->IdDoctores ? 'selected' : '' }}>
                                    Dr. {{ $doctor->empleado->Nombres }} {{ $doctor->empleado->Apellidos }}
                                    @if($doctor->especialidad) - {{ $doctor->especialidad->DescripcionEspe }} @endif
                                </option>
                            @endforeach
                        </select>
                        @error('Doctor_idDoctores') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Fecha de la Cita -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date"
                               name="FechaCita"
                               required
                               value="{{ old('FechaCita', $cita->FechaCita ? \Carbon\Carbon::parse($cita->FechaCita)->format('Y-m-d') : '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                        @error('FechaCita') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Hora de Inicio -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora de Inicio *</label>
                        <input type="time"
                               name="HoraInicio"
                               required
                               value="{{ old('HoraInicio', $cita->HoraInicio ? \Carbon\Carbon::parse($cita->HoraInicio)->format('H:i') : '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                        @error('HoraInicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Hora de Fin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora Fin</label>
                        <input type="time"
                               name="HoraFin"
                               value="{{ old('HoraFin', $cita->HoraFin ? \Carbon\Carbon::parse($cita->HoraFin)->format('H:i') : '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                        @error('HoraFin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Modalidad -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad *</label>
                        <select name="Modalidad" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                            <option value="Presencial" {{ $cita->Modalidad == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="Virtual" {{ $cita->Modalidad == 'Virtual' ? 'selected' : '' }}>Virtual</option>
                        </select>
                        @error('Modalidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Tipo de cita -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cita *</label>
                        <select name="Tipo_Cita_idTipo_Cita" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                            <option value="">Seleccionar tipo de cita</option>
                            @foreach($tiposCita as $tipo)
                                <option value="{{ $tipo->IdTipo_Cita }}" {{ $cita->Tipo_Cita_idTipo_Cita == $tipo->IdTipo_Cita ? 'selected' : '' }}>
                                    {{ $tipo->Descripcion }}
                                </option>
                            @endforeach
                        </select>
                        @error('Tipo_Cita_idTipo_Cita') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select name="Estado" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                            @foreach(['Programada','Confirmada','Completada','Cancelada'] as $estado)
                                <option value="{{ $estado }}" {{ $cita->Estado == $estado ? 'selected' : '' }}>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                        @error('Estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Motivo de la Cita -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la Cita</label>
                    <input type="text"
                        name="MotivoCita"
                        maxlength="200"
                        value="{{ old('MotivoCita', $cita->MotivoCita) }}"
                        placeholder="Ej: Consulta general, Seguimiento, Control..."
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                    @error('MotivoCita') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Observaciones -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                    <textarea name="Observaciones"
                              rows="2"
                              placeholder="Notas adicionales sobre la cita..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">{{ old('Observaciones', $cita->Observaciones) }}</textarea>
                    @error('Observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-4 mt-8">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg">
                        Actualizar Cita
                    </button>
                    <a href="{{ route('citas.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white font-semibold py-2 px-6 rounded-lg">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

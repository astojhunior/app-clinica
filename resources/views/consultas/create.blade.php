<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Atender consulta – {{ $cita->paciente->Nombres }} {{ $cita->paciente->Apellidos }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Mensajes --}}
            @if(session('success'))
                <div class="p-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 rounded bg-red-100 text-red-800 text-sm">
                    <ul class="list-disc ms-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('consultas.store', $cita->IdCita) }}">
                @csrf

                {{-- Panel datos de la cita --}}
                <div class="bg-white shadow-sm rounded-lg p-6 mb-4">
                    <h3 class="font-semibold text-lg mb-3">Datos de la cita</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium">Paciente:</span><br>
                            {{ $cita->paciente->Nombres }} {{ $cita->paciente->Apellidos }}
                        </div>
                        <div>
                            <span class="font-medium">Doctor:</span><br>
                            {{ $cita->doctor->empleado->Nombres ?? '' }}
                            {{ $cita->doctor->empleado->Apellidos ?? '' }}
                        </div>
                        <div>
                            <span class="font-medium">Fecha / Hora:</span><br>
                            {{ $cita->FechaCita }} – {{ $cita->HoraInicio }}
                        </div>
                        <div>
                            <span class="font-medium">Tipo de cita:</span><br>
                            {{ $cita->tipoCita->Descripcion ?? '' }}
                        </div>
                        <div>
                            <span class="font-medium">Motivo:</span><br>
                            {{ $cita->MotivoCita }}
                        </div>
                    </div>
                </div>

                {{-- Panel información clínica --}}
                <div class="bg-white shadow-sm rounded-lg p-6 mb-4 space-y-6">
                    <h3 class="font-semibold text-lg">Información clínica</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Fecha de consulta
                            </label>
                            <input type="date"
                                   name="FechaConsulta"
                                   value="{{ old('FechaConsulta', now()->toDateString()) }}"
                                   class="w-full border rounded-md px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Hora de fin
                            </label>
                            <input type="time"
                                   name="HoraFin"
                                   value="{{ old('HoraFin') }}"
                                   class="w-full border rounded-md px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Exploración física
                        </label>
                        <textarea name="ExploracionFisica"
                                  rows="4"
                                  class="w-full border rounded-md px-3 py-2 text-sm">{{ old('ExploracionFisica') }}</textarea>
                    </div>

                    <div>
                    <label class="block text-sm font-medium mb-2">
                        Síntomas
                    </label>

                    <div class="border rounded-md overflow-hidden">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 w-10 text-center">Sel.</th>
                                    <th class="px-3 py-2 text-left">Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sintomas as $s)
                                    <tr class="border-t">
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox"
                                                name="Sintomas[]"
                                                value="{{ $s->IdSintomas }}"
                                                {{ collect(old('Sintomas', []))->contains($s->IdSintomas) ? 'checked' : '' }}>
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $s->Descripcion }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-3 py-3 text-center text-gray-500">
                                            No hay síntomas registrados. Pídale al administrador que los cree en el catálogo.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

                {{-- Panel diagnóstico --}}
                <div class="bg-white shadow-sm rounded-lg p-6 mb-4 space-y-4">
                    <h3 class="font-semibold text-lg">Diagnóstico</h3>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Descripción del diagnóstico
                        </label>
                        <textarea name="diagnostico"
                                  rows="3"
                                  class="w-full border rounded-md px-3 py-2 text-sm">{{ old('diagnostico') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Tipo de diagnóstico
                            </label>
                            <select name="tipo_diagnostico"
                                    class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="">Seleccionar</option>
                                <option value="Presuntivo" {{ old('tipo_diagnostico')=='Presuntivo' ? 'selected' : '' }}>Presuntivo</option>
                                <option value="Definitivo" {{ old('tipo_diagnostico')=='Definitivo' ? 'selected' : '' }}>Definitivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Panel tratamientos y receta --}}
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6 space-y-4">
                    <h3 class="font-semibold text-lg">Tratamiento y receta</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">
                            Medicamento
                        </label>

                        <div class="border rounded-md overflow-hidden">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-3 py-2 w-10 text-center">Sel.</th>
                                        <th class="px-3 py-2 text-left">Nombre</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($medicinas as $m)
                                        <tr class="border-t">
                                            <td class="px-3 py-2 text-center">
                                                <input type="radio"
                                                    name="medicina_id"
                                                    value="{{ $m->IdMedicina }}"
                                                    {{ old('medicina_id') == $m->IdMedicina ? 'checked' : '' }}>
                                            </td>
                                            <td class="px-3 py-2">
                                                {{ $m->Nombre }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-3 py-3 text-center text-gray-500">
                                                No hay medicinas registradas. Pídale al administrador que las cree en el catálogo.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>


                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Descripción del tratamiento
                            </label>
                            <input type="text"
                                   name="tratamiento_descripcion"
                                   value="{{ old('tratamiento_descripcion') }}"
                                   class="w-full border rounded-md px-3 py-2 text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Indicaciones del tratamiento
                        </label>
                        <textarea name="tratamiento_indicaciones"
                                  rows="3"
                                  class="w-full border rounded-md px-3 py-2 text-sm">{{ old('tratamiento_indicaciones') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Fecha de receta
                            </label>
                            <input type="date"
                                   name="FechaReceta"
                                   value="{{ old('FechaReceta', now()->toDateString()) }}"
                                   class="w-full border rounded-md px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">
                                Instrucciones de la receta
                            </label>
                            <textarea name="Instrucciones"
                                      rows="2"
                                      class="w-full border rounded-md px-3 py-2 text-sm">{{ old('Instrucciones') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('consultas.index') }}"
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">
                        Guardar consulta
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pacientes') }}
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">


                   <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">📝 Registrar Nuevo Paciente</h3>

                    <form method="POST" action="{{ route('pacientes.store') }}" id="formPaciente">
                        @csrf

                        <h4 class="text-md font-semibold text-gray-700 mb-3 border-b pb-2">📋 Datos Personales</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- DNI -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                                <input type="text" name="Dni" required maxlength="45" pattern="[0-9]+" title="Solo números"
                                    value="{{ old('Dni') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Dni')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nombres -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                <input type="text" name="Nombres" required maxlength="45"
                                    pattern="[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+" title="Solo letras y espacios"
                                    value="{{ old('Nombres') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Nombres')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Apellidos -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                <input type="text" name="Apellidos" required maxlength="45"
                                    pattern="[a-záéíóúñA-ZÁÉÍÓÚÑ\s]+" title="Solo letras y espacios"
                                    value="{{ old('Apellidos') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Apellidos')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Correo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <input type="email" name="CorreoElectronico" maxlength="45"
                                    value="{{ old('CorreoElectronico') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('CorreoElectronico')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input type="tel" name="Telefono" maxlength="45"
                                    pattern="[0-9\-\+\(\)\s]+"
                                    title="Solo números, guiones, paréntesis y espacios"
                                    value="{{ old('Telefono') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Telefono')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Fecha de Nacimiento -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                <input type="text" name="FechaNacimiento" id="fecha_nacimiento" required placeholder="Seleccionar fecha"
                                    value="{{ old('FechaNacimiento') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('FechaNacimiento')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Género -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                                <select name="Genero" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Seleccionar</option>
                                    <option value="Masculino" {{ old('Genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('Genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('Genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('Genero')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                <select name="Estado" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" {{ old('Estado', 1) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('Estado') == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('Estado')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                <input type="text" name="Direccion" maxlength="200"
                                    value="{{ old('Direccion') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('Direccion')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="mt-6 flex flex-wrap gap-3">
                            <button
                                type="submit"
                                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium transition duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                            >
                                ✅ Registrar Paciente
                            </button>

                            <button
                                type="button"
                                onclick="limpiarConNotificacion()"
                                class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium transition duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                            >
                                🔄 Limpiar Campos
                            </button>
                        </div>
                    </form>
                </div>
            </div>


                    <div class="mb-6 p-4 bg-white border border-gray-200 rounded-lg">
                        <form method="GET" action="{{ route('pacientes.index') }}" class="flex flex-wrap gap-2 items-end">
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
                            <a href="{{ route('pacientes.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-200">🔄 Limpiar</a>
                        </form>
                    </div>


                    <div class="overflow-x-auto rounded-lg border border-gray-300">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">DNI</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Nombre Completo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Correo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($pacientes as $paciente)
                                <tr class="hover:bg-blue-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $paciente->Dni }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $paciente->Nombres }} {{ $paciente->Apellidos }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $paciente->CorreoElectronico }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $paciente->Telefono }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $paciente->Estado ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $paciente->Estado ? '✅ Activo' : '🔴 Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('pacientes.edit', $paciente->IdPaciente) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">✏️ Editar</a>
                                        <form method="POST" action="{{ route('pacientes.destroy', $paciente->IdPaciente) }}" class="inline-block">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Está seguro de desactivar este paciente?')">🔴 Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay pacientes registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    <div class="mt-4">
                        {{ $pacientes->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
                    </div>


                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="{{ asset('js/pacientes-scripts.js') }}"></script>

    <script>
        flatpickr("#fecha_nacimiento", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            theme: "material_blue"
        });
    </script>
</x-app-layout>

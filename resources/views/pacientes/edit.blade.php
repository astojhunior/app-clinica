<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Paciente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form method="POST" action="{{ route('pacientes.update', $paciente->IdPaciente) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                            <!-- DNI -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">DNI *</label>
                                <input
                                    type="text"
                                    name="Dni"
                                    value="{{ old('Dni', $paciente->Dni) }}"
                                    required
                                    maxlength="45"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('Dni')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nombres -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                <input
                                    type="text"
                                    name="Nombres"
                                    value="{{ old('Nombres', $paciente->Nombres) }}"
                                    required
                                    maxlength="45"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('Nombres')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Apellidos -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                <input
                                    type="text"
                                    name="Apellidos"
                                    value="{{ old('Apellidos', $paciente->Apellidos) }}"
                                    required
                                    maxlength="45"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('Apellidos')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Correo Electrónico -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <input
                                    type="email"
                                    name="CorreoElectronico"
                                    value="{{ old('CorreoElectronico', $paciente->CorreoElectronico) }}"
                                    maxlength="45"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('CorreoElectronico')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input
                                    type="tel"
                                    name="Telefono"
                                    value="{{ old('Telefono', $paciente->Telefono) }}"
                                    maxlength="45"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('Telefono')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Fecha de Nacimiento -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                <input
                                    type="date"
                                    name="FechaNacimiento"
                                    value="{{ old('FechaNacimiento', date('Y-m-d', strtotime($paciente->FechaNacimiento))) }}"
                                    required
                                    max="{{ date('Y-m-d') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('FechaNacimiento')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Género -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                                <select
                                    name="Genero"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Seleccionar</option>
                                    <option value="Masculino" {{ old('Genero', $paciente->Genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ old('Genero', $paciente->Genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="Otro" {{ old('Genero', $paciente->Genero) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('Genero')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                                <select
                                    name="Estado"
                                    required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="1" {{ old('Estado', $paciente->Estado) == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('Estado', $paciente->Estado) == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('Estado')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                <input
                                    type="text"
                                    name="Direccion"
                                    value="{{ old('Direccion', $paciente->Direccion) }}"
                                    maxlength="200"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                @error('Direccion')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Botones -->
                        <div class="mt-6 flex gap-2">
                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium"
                            >
                                Actualizar Paciente
                            </button>
                            <a
                                href="{{ route('pacientes.index') }}"
                                class="px-6 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 font-medium"
                            >
                                Cancelar
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

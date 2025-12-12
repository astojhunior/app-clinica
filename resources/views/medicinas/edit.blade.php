<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Editar medicina</h2>
    </x-slot>

    <div class="py-6 max-w-lg mx-auto">
        <form method="POST"
              action="{{ route('medicinas.update', $medicina) }}"
              class="bg-white p-6 rounded shadow-sm space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" name="Nombre"
                       value="{{ old('Nombre', $medicina->Nombre) }}"
                       class="w-full border rounded px-3 py-2 text-sm">
                @error('Nombre')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Estado</label>
                <select name="Estado"
                        class="w-full border rounded px-3 py-2 text-sm">
                    <option value="1" {{ $medicina->Estado ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !$medicina->Estado ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('medicinas.index') }}"
                   class="px-4 py-2 border rounded text-sm">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded">
                    Actualizar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

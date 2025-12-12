<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nueva medicina</h2>
    </x-slot>

    <div class="py-6 max-w-lg mx-auto">
        <form method="POST"
              action="{{ route('medicinas.store') }}"
              class="bg-white p-6 rounded shadow-sm space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Nombre</label>
                <input type="text" name="Nombre" value="{{ old('Nombre') }}"
                       class="w-full border rounded px-3 py-2 text-sm">
                @error('Nombre')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('medicinas.index') }}"
                   class="px-4 py-2 border rounded text-sm">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

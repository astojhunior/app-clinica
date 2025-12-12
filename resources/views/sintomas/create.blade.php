{{-- create --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Nuevo síntoma</h2>
    </x-slot>

    <div class="py-6 max-w-lg mx-auto">
        <form method="POST" action="{{ route('sintomas.store') }}" class="bg-white p-6 rounded shadow-sm space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <input type="text" name="Descripcion" value="{{ old('Descripcion') }}"
                       class="w-full border rounded px-3 py-2 text-sm">
                @error('Descripcion')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('sintomas.index') }}" class="px-4 py-2 border rounded text-sm">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded">Guardar</button>
            </div>
        </form>
    </div>
</x-app-layout>

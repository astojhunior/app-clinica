<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Catálogo de medicinas</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex justify-end">
            <a href="{{ route('medicinas.create') }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm rounded">
                Nueva medicina
            </a>
        </div>

        <div class="bg-white shadow-sm rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($medicinas as $medicina)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $medicina->Nombre }}</td>
                            <td class="px-4 py-2">
                                {{ $medicina->Estado ? 'Activo' : 'Inactivo' }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('medicinas.edit', $medicina) }}"
                                   class="text-blue-600 text-sm">Editar</a>
                                <form action="{{ route('medicinas.destroy', $medicina) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('¿Eliminar medicina?')"
                                            class="text-red-600 text-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-center text-gray-500">
                                No hay medicinas registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

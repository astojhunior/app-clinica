<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Catálogo de síntomas</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        @if (session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 text-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex justify-end">
            <a href="{{ route('sintomas.create') }}"
               class="px-4 py-2 bg-blue-600 text-white text-sm rounded">
                Nuevo síntoma
            </a>
        </div>

        <div class="bg-white shadow-sm rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Descripción</th>
                        <th class="px-4 py-2 text-left">Estado</th>
                        <th class="px-4 py-2 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sintomas as $sintoma)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $sintoma->Descripcion }}</td>
                            <td class="px-4 py-2">
                                {{ $sintoma->Estado ? 'Activo' : 'Inactivo' }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('sintomas.edit', $sintoma) }}"
                                   class="text-blue-600 text-sm">Editar</a>
                                <form action="{{ route('sintomas.destroy', $sintoma) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('¿Eliminar síntoma?')"
                                            class="text-red-600 text-sm">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-3 text-center text-gray-500">
                            No hay síntomas registrados.
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

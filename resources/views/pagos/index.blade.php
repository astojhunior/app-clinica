<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pagos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Botón Nuevo Pago -->
            <div class="mb-6 flex justify-end">
                <a href="{{ route('pagos.create') }}"
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition mr-2">
                    + Nuevo Pago
                </a>
            </div>

            <!-- Mensajes de éxito/error -->
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

            <!-- Filtros y búsqueda -->
            <div class="mb-6 p-4 bg-white border border-gray-200 rounded-lg">
                <form method="GET" action="{{ route('pagos.index') }}" class="flex flex-wrap gap-2 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <input
                            type="text"
                            name="search"
                            id="buscar-pagos-live"   {{-- ID para JS --}}
                            placeholder="DNI, ID Cita, N° Pago..."
                            value="{{ request('search') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div class="min-w-[150px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registros</label>
                        <select name="per_page"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="10"  {{ request('per_page') == 10  ? 'selected' : '' }}>10 por página</option>
                            <option value="25"  {{ request('per_page') == 25  ? 'selected' : '' }}>25 por página</option>
                            <option value="50"  {{ request('per_page') == 50  ? 'selected' : '' }}>50 por página</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 por página</option>
                        </select>
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        🔍 Buscar
                    </button>
                    <a href="{{ route('pagos.index') }}"
                       class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition duration-200">
                        🔄 Limpiar
                    </a>
                </form>
            </div>

            <div class="mt-8 bg-white border border-gray-200 rounded-lg p-2 shadow">
                <h4 class="font-semibold text-gray-700 mb-2">Historial de Pagos</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2">N° Pago</th>
                            <th class="px-4 py-2">Fecha y Hora</th>
                            <th class="px-4 py-2">Monto</th>
                            <th class="px-4 py-2">Método</th>
                            <th class="px-4 py-2">Estado</th>
                            <th class="px-4 py-2">Saldo Pendiente</th>
                            <th class="px-4 py-2">Detalle</th>
                            <th class="px-4 py-2">Acciones</th>
                        </tr>
                        </thead>
                        <tbody id="tabla-pagos">
                        @forelse($pagos as $pago)
                            @php
                                $cita          = $pago->cita;
                                $paciente      = $cita?->paciente;
                                $dni           = $paciente->Dni ?? '';
                                $costoCita     = $cita->CostoTotal ?? 0;
                                // solo pagos NO anulados (EstadoPago != 2)
                                $pagosCita     = $cita
                                    ? $cita->pagos()->where('EstadoPago','!=',2)->get()
                                    : collect([]);
                                $totalAbonado  = $pagosCita->sum('MontoTotal');
                                $saldoPendiente = $costoCita - $totalAbonado;
                            @endphp

                            <tr class="hover:bg-blue-50 border-b last:border-b-0"
                                data-dni="{{ $dni }}"
                                data-idcita="{{ $cita->IdCita ?? '' }}"
                            >
                                <td class="px-4 py-2 whitespace-nowrap font-medium text-gray-800">
                                    {{ $pago->IdPago }}
                                </td>

                                <td class="px-4 py-2 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($pago->FechaPago)->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-4 py-2 font-bold text-green-700 whitespace-nowrap">
                                    S/ {{ number_format($pago->MontoTotal,2) }}
                                </td>

                                <td class="px-4 py-2">
                                    {{ $pago->tipoPago->TipoDescripcion ?? '-' }}
                                </td>

                                <td class="px-4 py-2">
                                    @if($pago->EstadoPago == 1)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                            Pagado
                                        </span>
                                    @elseif($pago->EstadoPago == 0)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                            Pendiente
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                            Anulado
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-2">
                                    @if($saldoPendiente > 0)
                                        <span class="text-red-700 font-bold">
                                            S/ {{ number_format($saldoPendiente,2) }}
                                        </span>
                                    @else
                                        <span class="text-green-700 font-bold">
                                            S/ 0.00 (Cancelada)
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-2">
                                    @if($pago->detalles && $pago->detalles->count())
                                        <span class="text-gray-700">Con desglose</span>
                                    @else
                                        <span class="italic text-gray-500">Sin desglose</span>
                                    @endif
                                </td>

                                <td class="px-4 py-2">
                                    <div class="flex flex-wrap gap-2">
                                        {{-- Ver --}}
                                        <a href="{{ route('pagos.show', $pago->IdPago) }}"
                                        class="px-2 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                            Ver
                                        </a>

                                        {{-- Anular solo si no está anulado --}}
                                        @if($pago->EstadoPago != 2)
                                            <form method="POST"
                                                action="{{ route('pagos.anular', $pago->IdPago) }}"
                                                onsubmit="return confirm('¿Seguro que deseas anular este pago?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit"
                                                        class="px-2 py-1 text-xs bg-red-600 text-white rounded hover:bg-red-700">
                                                    Anular
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-500 italic">Anulado</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    class="text-center py-4 text-gray-400 italic">
                                    No hay pagos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $pagos->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <script src="{{ asset('js/pagos-index.js') }}"></script>

</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Realizar Pago de Cita</h2>
    </x-slot>

    <div class="py-8">
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow border border-gray-200">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md text-center">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md text-center">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Selector de cita --}}
            <div class="mb-10">
                <form method="GET" action="{{ route('pagos.create') }}" class="mb-3 grid grid-cols-1 md:grid-cols-3 gap-3">

                    {{-- Búsqueda incremental por DNI o ID de Cita (NO se envía al servidor) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Buscar en la tabla (DNI o ID Cita)
                        </label>
                        <input
                            type="text"
                            id="buscar-cita-live"
                            placeholder="Escribe DNI o ID de cita…"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <p class="text-xs text-gray-400 mt-1">
                            La tabla se filtra mientras escribes.
                        </p>
                    </div>

                    {{-- Filtro por fecha --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Cita</label>
                        <input type="date" name="fecha"
                            value="{{ request('fecha') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- Registros por página --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registros</label>
                        <select name="per_page"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="5"  {{ request('per_page') == 5  ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page',10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex gap-3 mt-1">
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">
                            🔍 Buscar
                        </button>
                        <a href="{{ route('pagos.create') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                            Limpiar
                        </a>
                    </div>
                </form>

                <div class="overflow-x-auto border rounded">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2">Fecha</th>
                            <th class="px-4 py-2">Paciente</th>
                            <th class="px-4 py-2">Doctor</th>
                            <th class="px-4 py-2">Costo</th>
                            <th class="px-4 py-2">Seleccionar</th>
                        </tr>
                        </thead>
                        <tbody id="tabla-citas">
                        @forelse($citas as $cita)
                            @php
                                $pagadoNoAnulado = $cita->pagos->sum('MontoTotal');
                                $dni = $cita->paciente->Dni ?? '';
                            @endphp
                            <tr
                                data-dni="{{ $dni }}"
                                data-idcita="{{ $cita->IdCita }}"
                            >
                                <td class="px-4 py-2">
                                    {{ \Carbon\Carbon::parse($cita->FechaCita)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $dni }} - {{ $cita->paciente->Nombres ?? '' }} {{ $cita->paciente->Apellidos ?? '' }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $cita->doctor->empleado->Nombres ?? '' }} {{ $cita->doctor->empleado->Apellidos ?? '' }}
                                </td>
                                <td class="px-4 py-2">
                                    S/ {{ number_format($cita->CostoTotal, 2) }}
                                </td>
                                <td class="px-4 py-2">
                                    <button
                                        type="button"
                                        class="bg-indigo-600 text-white rounded px-3 py-1 select-cita"
                                        data-id="{{ $cita->IdCita }}"
                                        data-monto="{{ $cita->CostoTotal }}"
                                        data-abonado="{{ $pagadoNoAnulado }}"
                                        data-estado="{{ $cita->EstadoPago }}"
                                        data-paciente="{{ $cita->paciente->Nombres }} {{ $cita->paciente->Apellidos }}"
                                        data-fecha="{{ $cita->FechaCita }}"
                                    >
                                        Seleccionar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-gray-400 italic">
                                    No hay citas pendientes para pago.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $citas->appends([
                        'fecha'   => request('fecha'),
                        'per_page'=> request('per_page')
                    ])->links() }}
                </div>
            </div>


            <form method="POST" action="{{ route('pagos.store') }}" class="mt-8">
                @csrf

                {{-- Info de la cita seleccionada + hidden para el ID --}}
                <div id="info-cita" class="mb-4 hidden">
                    <span class="block text-gray-700 font-medium">Cita seleccionada:</span>
                    <span id="desc-cita" class="text-indigo-700"></span>
                    <input type="hidden" name="CitaidCita" id="input-id-cita">
                    @error('CitaidCita')
                        <span class="text-red-500 text-xs block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Saldo pendiente (lo llena el JS) --}}
                <span id="saldo-pendiente" class="font-semibold text-blue-700 block mb-2"></span>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Monto total, solo lectura -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Monto de la cita (no editable)
                        </label>
                        <input
                            type="number"
                            name="MontoTotal"
                            id="input-monto-cita"
                            step="0.01"
                            readonly
                            class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-800 shadow-sm focus:outline-none">
                    </div>

                    <!-- Monto a pagar -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto a pagar</label>
                        <input
                            type="number"
                            id="monto-pago"
                            name="MontoPago"
                            required
                            step="0.01"
                            min="0.01"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                    </div>

                    <!-- Método de pago -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                        <select name="TipoPago_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                            <option value="">Seleccionar método...</option>
                            @foreach($tiposPagos as $tipo)
                                @if($tipo->TipoEstado)
                                    <option value="{{ $tipo->IDtipos }}">{{ $tipo->TipoDescripcion }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('TipoPago_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Estado Pago -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Pago (depende del saldo)</label>
                        <input type="text"
                               name="EstadoPago"
                               id="estado-pago"
                               readonly
                               class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-800 shadow-sm focus:outline-none">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium transition shadow">
                        Registrar Pago
                    </button>
                    <a href="{{ route('pagos.index') }}"
                       class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700 transition shadow">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/pagos-scripts.js') }}"></script>
</x-app-layout>

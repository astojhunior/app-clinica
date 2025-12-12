<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle del Pago</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto bg-white p-8 rounded shadow border border-gray-200" id="area-impresion">

            {{-- Datos principales del pago --}}
            <div class="mb-2">
                <span class="font-bold">ID Pago:</span>
                <span id="num_recibo">{{ $pago->IdPago }}</span>
            </div>

            <div class="mb-2">
                <span class="font-bold">Fecha Pago:</span>
                <span id="fecha_pago">{{ \Carbon\Carbon::parse($pago->FechaPago)->format('d/m/Y H:i') }}</span>
            </div>

            <div class="mb-2">
                <span class="font-bold">Método:</span>
                <span id="metodo_pago">{{ $pago->tipoPago->TipoDescripcion ?? '-' }}</span>
            </div>

            <div class="mb-2">
                <span class="font-bold">Monto abonado:</span>
                S/ <span id="total_pago">{{ number_format($pago->MontoTotal,2) }}</span>
            </div>

            <div class="mb-2">
                <span class="font-bold">Sub total:</span>
                S/ <span id="subtotal">{{ number_format($pago->SubTotal ?? 0,2) }}</span>
            </div>

            <div class="mb-2">
                <span class="font-bold">IGV (18%):</span>
                S/ <span id="igv">{{ number_format($pago->MontoIGV ?? 0,2) }}</span>
            </div>

            <div class="mb-2">
                <span class="font-bold">Estado:</span>
                <span id="estado_pago">
                    @if($pago->EstadoPago === 1)
                        Pagado
                    @elseif($pago->EstadoPago === 2)
                        Anulado
                    @else
                        Pendiente
                    @endif
                </span>
            </div>

            {{-- Paciente / Cita asociada --}}
            <div class="mb-2">
                <span class="font-bold">Paciente:</span><br>
                <span class="font-medium" id="paciente_nombre">
                    {{ $pago->cita->paciente->Nombres ?? '-' }} {{ $pago->cita->paciente->Apellidos ?? '' }}
                </span><br>

                <span class="text-sm">
                    DNI:
                    <span id="paciente_dni">{{ $pago->cita->paciente->Dni ?? '-' }}</span>
                </span><br>

                @php
                    $edad = $pago->cita->paciente?->FechaNacimiento
                        ? \Carbon\Carbon::parse($pago->cita->paciente->FechaNacimiento)->age
                        : null;
                @endphp

                @if($edad !== null)
                    <span class="text-sm">
                        Edad:
                        <span id="paciente_edad">{{ $edad }}</span> años
                    </span><br>
                @endif

                <span class="text-xs text-gray-500">
                    Cita N° <span id="num_pedido">{{ $pago->Cita_idCita }}</span>
                </span>
            </div>

            {{-- Dirección (oculta para impresión JS) --}}
            <span id="paciente_direccion" class="hidden">
                {{ $pago->cita->paciente->Direccion ?? '' }}
            </span>

            {{-- Doctor y especialidad (visibles y con ids para JS) --}}
            <div class="mb-2 mt-2">
                <span class="font-bold">Doctor:</span>
                <span id="doctor_nombre">
                    {{ $pago->cita->doctor->empleado->Nombres ?? '-' }}
                    {{ $pago->cita->doctor->empleado->Apellidos ?? '' }}
                </span>
            </div>

            <div class="mb-2">
                <span class="font-bold">Especialidad:</span>
                <span id="especialidad_nombre">
                    {{ $pago->cita->doctor->especialidad->DescripcionEspe ?? '-' }}
                </span>
            </div>

            <div class="mb-2">
                <span class="font-bold">Costo de la cita:</span>
                S/
                <span id="costo_especialidad">
                    {{ number_format($pago->cita->CostoTotal ?? 0, 2) }}
                </span>
            </div>

            {{-- Tabla oculta para que JS lea las líneas --}}
            @if($pago->detalles && $pago->detalles->count())
                <table id="tabla-items" class="hidden">
                    <tbody>
                    @foreach($pago->detalles as $detalle)
                        <tr>
                            <td class="col-cant">{{ $detalle->Cantidad }}</td>
                            <td class="col-desc">{{ $detalle->Descripcion }}</td>
                            <td class="col-precio">{{ number_format($detalle->Monto,2) }}</td>
                            <td class="col-importe">{{ number_format($detalle->Monto * $detalle->Cantidad,2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif

            {{-- Detalle visible en pantalla --}}
            @if($pago->detalles && $pago->detalles->count())
                <div class="mb-2 mt-4">
                    <span class="font-bold">Detalle del Pago:</span>
                    <ul class="list-disc pl-5 text-sm mt-1">
                        @foreach($pago->detalles as $detalle)
                            <li>
                                {{ $detalle->Descripcion }} -
                                S/ {{ number_format($detalle->Monto,2) }} x {{ $detalle->Cantidad }}
                                <span class="text-gray-400">
                                    = S/ {{ number_format($detalle->Monto*$detalle->Cantidad,2) }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex gap-3 mt-6">
                <a href="{{ route('pagos.index') }}"
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">Volver</a>

                <button onclick="window.printArea()"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-800">
                    Imprimir comprobante
                </button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/pagos-scripts.js') }}"></script>

    <style>
        @media print {
            body * { visibility: hidden; }
            #area-impresion, #area-impresion * { visibility: visible; }
            #area-impresion { margin:0; padding:0; box-shadow:none; border:none; }
            .bg-white { background: white !important; }
            .rounded, .shadow { border-radius: 0 !important; box-shadow: none !important; }
            a, button { display: none !important; }
            .mt-6, .mb-2, .mb-5, .mt-5 { margin: 5px 0 !important; }
        }
    </style>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalles de la Cita
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Botones superiores -->
            <div class="mb-4 flex gap-3">
                <a href="{{ route('citas.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-all">
                    ← Volver a Citas
                </a>

                <!-- Botón Imprimir -->
                <button onclick="imprimirCita()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all">
                    🖨️ Imprimir
                </button>


            </div>

            <!-- Card Principal -->

<div id="contenido-imprimible" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200" style="max-width:700px; margin:auto;">
    <div class="p-8 border-b border-gray-300 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Comprobante de Cita Médica</h2>
        <div class="text-lg text-gray-700 mb-1 font-semibold">Código de cita: <span class="text-blue-900">{{ $cita->IdCita }}</span></div>
        <span class="inline-block text-sm font-semibold px-4 py-2 rounded-full"
              style="background:{{ $cita->Estado == 'Confirmada' ? '#DEF7EC' : ($cita->Estado == 'Cancelada' ? '#fee2e2' : '#E0E7FF') }};
                     color:{{ $cita->Estado == 'Confirmada' ? '#065F46' : ($cita->Estado == 'Cancelada' ? '#991B1B' : '#3730A3') }};">
              {{ $cita->Estado }}
        </span>
    </div>
    <div class="p-8 space-y-8">
        <!-- Datos Paciente -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Datos del Paciente</h3>
            <table class="w-full mb-2">
                <tr>
                    <td class="font-semibold text-gray-600 w-32">Nombre:</td>
                    <td class="text-gray-900">{{ $cita->paciente->Nombres }} {{ $cita->paciente->Apellidos }}</td>
                </tr>
                <tr>
                    <td class="font-semibold text-gray-600">DNI:</td>
                    <td class="text-gray-900">{{ $cita->paciente->Dni }}</td>
                </tr>
                @if($cita->paciente->FechaNacimiento)
                <tr>
                    <td class="font-semibold text-gray-600">Edad:</td>
                    <td class="text-gray-900">
                        {{ $cita->paciente->FechaNacimiento ? \Carbon\Carbon::parse($cita->paciente->FechaNacimiento)->age : '-' }} años
                        (Nac: {{ \Carbon\Carbon::parse($cita->paciente->FechaNacimiento)->format('d/m/Y') }})
                    </td>
                </tr>
                @endif
                @if($cita->paciente->Telefono)
                <tr>
                    <td class="font-semibold text-gray-600">Teléfono:</td>
                    <td class="text-gray-900">{{ $cita->paciente->Telefono }}</td>
                </tr>
                @endif
                @if($cita->paciente->Email)
                <tr>
                    <td class="font-semibold text-gray-600">Email:</td>
                    <td class="text-gray-900">{{ $cita->paciente->Email }}</td>
                </tr>
                @endif
            </table>
        </div>
        <!-- Datos Doctor -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Datos del Doctor</h3>
            <table class="w-full mb-2">
                <tr>
                    <td class="font-semibold text-gray-600 w-32">Nombre:</td>
                    <td class="text-gray-900">Dr. {{ $cita->doctor->empleado->Nombres }} {{ $cita->doctor->empleado->Apellidos }}</td>
                </tr>
                @if($cita->doctor->especialidad)
                <tr>
                    <td class="font-semibold text-gray-600">Especialidad:</td>
                    <td class="text-gray-900">{{ $cita->doctor->especialidad->DescripcionEspe }}</td>
                </tr>
                @endif
                @if($cita->doctor->NumeroColegiatura)
                <tr>
                    <td class="font-semibold text-gray-600">CMP:</td>
                    <td class="text-gray-900">{{ $cita->doctor->NumeroColegiatura }}</td>
                </tr>
                @endif
            </table>
        </div>
        <!-- Detalles de la cita -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Detalles de la Cita</h3>
            <table class="w-full mb-2">
                <tr>
                    <td class="font-semibold text-gray-600 w-32">Fecha:</td>
                    <td class="text-gray-900">{{ \Carbon\Carbon::parse($cita->FechaCita)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td class="font-semibold text-gray-600">Hora:</td>
                    <td class="text-gray-900">{{ \Carbon\Carbon::parse($cita->HoraInicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($cita->HoraFin)->format('h:i A') }}</td>
                </tr>
                <tr>
                    <td class="font-semibold text-gray-600">Tipo:</td>
                    <td class="text-gray-900">{{ $cita->tipoCita->Descripcion ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="font-semibold text-gray-600">Modalidad:</td>
                    <td class="text-gray-900">{{ $cita->Modalidad }}</td>
                </tr>
                @if($cita->MotivoCita)
                <tr>
                    <td class="font-semibold text-gray-600">Motivo:</td>
                    <td class="text-gray-900">{{ $cita->MotivoCita }}</td>
                </tr>
                @endif
            </table>
        </div>
        <!-- Información de Pago -->
        <div>
            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Información de Pago</h3>
            <table class="w-full mb-2">
                <tr>
                    <td class="font-semibold text-gray-600 w-32">Costo Total:</td>
                    <td class="text-gray-900 font-bold">S/ {{ number_format($cita->CostoTotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="font-semibold text-gray-600">Estado de Pago:</td>
                    <td class="text-gray-900">
                        <span class="px-2 py-1 rounded-full
                            {{ $cita->EstadoPago == 'Pagado' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $cita->EstadoPago == 'Pagado' ? 'Pagado' : 'Pendiente' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="py-4 px-8 border-t border-gray-200 text-right text-sm text-gray-500">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y, H:i') }}
    </div>
</div>

                    </div>
    </div>

    <!-- JavaScript para imprimir -->
   <script>
        function imprimirCita() {
            // 1. Tomar los datos visibles en la vista (usamos ids para no depender de Blade dentro del JS)
            const codigoCita    = "{{ $cita->IdCita }}";
            const estadoCita    = "{{ $cita->Estado }}";

            const nombrePaciente= "{{ $cita->paciente->Nombres }} {{ $cita->paciente->Apellidos }}";
            const dniPaciente   = "{{ $cita->paciente->Dni }}";
            const edadPaciente  = @json($cita->paciente->FechaNacimiento ? \Carbon\Carbon::parse($cita->paciente->FechaNacimiento)->age . ' años' : '-');
            const telPaciente   = @json($cita->paciente->Telefono ?? '-');
            const emailPaciente = @json($cita->paciente->Email ?? '-');

            const nombreDoctor  = "Dr. {{ $cita->doctor->empleado->Nombres }} {{ $cita->doctor->empleado->Apellidos }}";
            const especialidad  = @json($cita->doctor->especialidad->DescripcionEspe ?? '-');
            const cmpDoctor     = @json($cita->doctor->NumeroColegiatura ?? '-');

            const fechaCita     = "{{ \Carbon\Carbon::parse($cita->FechaCita)->format('d/m/Y') }}";
            const horaCita      = "{{ \Carbon\Carbon::parse($cita->HoraInicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($cita->HoraFin)->format('h:i A') }}";
            const tipoCita      = @json($cita->tipoCita->Descripcion ?? '-');
            const modalidad     = "{{ $cita->Modalidad }}";
            const motivo        = @json($cita->MotivoCita ?? '-');

            const costoTotal    = "S/ {{ number_format($cita->CostoTotal, 2) }}";
            const estadoPago    = "{{ $cita->EstadoPago == 'Pagado' ? 'Pagado' : 'Pendiente' }}";

            const generadoEl    = "{{ \Carbon\Carbon::now()->format('d/m/Y, H:i') }}";

            // 2. Plantilla tipo constancia (similar a tu imagen)
            const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Cita Programada</title>
            <style>
                body{
                    font-family: "Times New Roman", serif;
                    margin:40px 60px;
                    color:#000;
                    font-size:13px;
                }
                h1{
                    text-align:center;
                    margin-bottom:25px;
                    font-size:22px;
                    text-transform:uppercase;
                }
                .fila{
                    display:flex;
                    justify-content:space-between;
                    margin-bottom:8px;
                }
                .etiqueta{
                    font-weight:bold;
                }
                .seccion{
                    margin-top:18px;
                }
                .seccion-title{
                    font-weight:bold;
                    margin-bottom:5px;
                }
                .bloque-texto{
                    margin-top:18px;
                    text-align:justify;
                    line-height:1.4;
                }
                hr{
                    border:none;
                    border-top:1px solid #c00;
                    margin:25px 0;
                }
                .pie{
                    margin-top:15px;
                    font-size:11px;
                }
                .codigo{
                    margin-top:15px;
                    font-weight:bold;
                }
                @media print{
                    @page{ margin:15mm 20mm; }
                }
            </style>
        </head>
        <body>
            <h1>CITA PROGRAMADA</h1>

            <div class="fila">
                <div>
                    <span class="etiqueta">Clínica:</span> CLÍNICA CALIDAD<br>
                    <span class="etiqueta">Oficina:</span> Consultorios Externos<br>
                    <span class="etiqueta">Sede:</span> Dirección completa de la sede
                </div>
                <div style="text-align:right;">
                    <span class="etiqueta">Fecha de la Cita:</span> ${fechaCita}<br>
                    <span class="etiqueta">Hora de la Cita:</span> ${horaCita}<br>
                    <span class="etiqueta">Código de Cita:</span> ${codigoCita}
                </div>
            </div>

            <div class="seccion">
                <div class="seccion-title">Datos del Paciente</div>
                <div><span class="etiqueta">Nombre y Apellidos:</span> ${nombrePaciente}</div>
                <div><span class="etiqueta">Documento de Identidad:</span> ${dniPaciente}</div>
                <div><span class="etiqueta">Edad:</span> ${edadPaciente}</div>
                <div><span class="etiqueta">Teléfono:</span> ${telPaciente}</div>
                <div><span class="etiqueta">Email:</span> ${emailPaciente}</div>
            </div>

            <div class="seccion">
                <div class="seccion-title">Datos del Doctor</div>
                <div><span class="etiqueta">Médico:</span> ${nombreDoctor}</div>
                <div><span class="etiqueta">Especialidad:</span> ${especialidad}</div>
            </div>

            <div class="seccion">
                <div class="seccion-title">Detalles de la Cita</div>
                <div><span class="etiqueta">Tipo de Cita:</span> ${tipoCita}</div>
                <div><span class="etiqueta">Modalidad:</span> ${modalidad}</div>
                <div><span class="etiqueta">Motivo:</span> ${motivo}</div>
                <div><span class="etiqueta">Estado de la Cita:</span> ${estadoCita}</div>
            </div>

            <div class="seccion">
                <div class="seccion-title">Información de Pago</div>
                <div><span class="etiqueta">Costo Total:</span> ${costoTotal}</div>
                <div><span class="etiqueta">Estado de Pago:</span> ${estadoPago}</div>
            </div>

            <div class="bloque-texto">
                <span class="etiqueta">Nota:</span> La asignación de esta cita es personal e intransferible. El paciente deberá presentarse en la clínica en la fecha y hora indicadas, portando su documento de identidad. En caso de no asistir o llegar fuera del horario establecido, la cita podrá ser reprogramada según la disponibilidad.
            </div>

            <div class="codigo">
                Código de seguridad: ${codigoCita}-${estadoCita}
            </div>

            <hr>

            <div class="pie">
                Documento generado electrónicamente el ${generadoEl}. No requiere firma manuscrita.
            </div>
        </body>
        </html>`;

            // 3. Abrir ventana solo para imprimir
            const win = window.open('', '_blank', 'width=800,height=900');
            win.document.open();
            win.document.write(html);
            win.document.close();
            win.focus();
            win.print();
            win.close();
        }
        </script>

    <!-- Estilos para impresión -->
    <style>
        @media print {
            /* Ocultar elementos que no deben imprimirse */
            .no-print,
            header,
            nav,
            .py-12 > div > div:first-child {
                display: none !important;
            }

            /* Ajustes para la impresión */
            body {
                background: white;
            }

            #contenido-imprimible {
                box-shadow: none;
                border: none;
            }

            /* Asegurar que los colores se impriman */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</x-app-layout>

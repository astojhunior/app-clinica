<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Citas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Tarjetas de estadísticas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Pacientes registrados</p>
                        <p class="mt-2 text-2xl font-bold text-gray-800">
                            {{ $totalPacientes }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Doctores</p>
                        <p class="mt-2 text-2xl font-bold text-gray-800">
                            {{ $totalDoctores }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Citas de hoy</p>
                        <p class="mt-2 text-2xl font-bold text-gray-800">
                            {{ $citasHoy }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <p class="text-sm text-gray-500">Citas futuras</p>
                        <p class="mt-2 text-2xl font-bold text-gray-800">
                            {{ $citasFuturas }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Pagos del día (resumen numérico) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total pagado hoy</p>
                        <p class="mt-2 text-2xl font-bold text-gray-800">
                            S/ {{ number_format($pagosHoy, 2) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Filtro y gráfico de pagos (línea) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-lg text-gray-800">
                            Pagos realizados ({{ $rango }})
                        </h3>
                        <form method="GET" action="{{ route('dashboard') }}">
                            <select name="rango"
                                    onchange="this.form.submit()"
                                    class="border-gray-300 rounded-md text-sm">
                                <option value="dia"  {{ $rango=='dia' ? 'selected' : '' }}>Día</option>
                                <option value="mes"  {{ $rango=='mes' ? 'selected' : '' }}>Mes</option>
                                <option value="anio" {{ $rango=='anio' ? 'selected' : '' }}>Año</option>
                            </select>
                        </form>
                    </div>

                    {{-- Canvas para gráfico de línea de pagos --}}
                    <div class="relative" style="height:260px;">
                        anvas id="pagosChart"
                                data-labels='@json($labelsPagos)'
                                data-values='@json($dataPagos)'>
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- Gráfico pastel de pacientes activos/inactivos --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">
                        Estado de pacientes
                    </h3>

                    <div class="flex items-center justify-center" style="height:260px;">
                        anvas id="pacientesChart"
                                data-activos="{{ $activos }}"
                                data-inactivos="{{ $inactivos }}">
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- Próximas citas --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg text-gray-800 mb-4">
                        Próximas citas
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="border-b text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="py-2 pr-4">Fecha</th>
                                    <th class="py-2 pr-4">Hora</th>
                                    <th class="py-2 pr-4">Paciente</th>
                                    <th class="py-2 pr-4">Doctor</th>
                                    <th class="py-2 pr-4">Estado</th>
                                    <th class="py-2 pr-4">Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($proximasCitas as $cita)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">
                                            {{ optional($cita->FechaCita)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            {{ \Carbon\Carbon::parse($cita->HoraInicio)->format('H:i') }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            {{ $cita->nombre_paciente ?? $cita->NombrePaciente ?? 'N/A' }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            {{ $cita->nombre_doctor ?? $cita->NombreDoctor ?? 'N/D' }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            {{ $cita->Estado }}
                                        </td>
                                        <td class="py-2 pr-4">
                                            {{ $cita->EstadoPago ?? 'Pendiente' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-gray-500">
                                            No hay citas futuras registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
</x-app-layout>


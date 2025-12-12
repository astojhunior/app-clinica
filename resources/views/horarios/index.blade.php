<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Horario de Atención Médica
        </h2>
    </x-slot>
        <div class="mb-6 flex justify-end">
                <a href="{{ route('citas.index') }}"
                   class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition mr-2">
                    Volver
                </a>
            </div>
    <div class="py-8 horario-wrapper">
        <div class="max-w-6xl mx-auto bg-white p-6 rounded-2xl shadow border border-slate-200">

            {{-- Filtro por especialidad --}}
            <div class="mb-4 flex items-center gap-3">
                <label for="filtro-especialidad" class="text-sm font-medium text-gray-700">
                    Especialidad:
                </label>
                <select id="filtro-especialidad"
                        class="rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    <option value="todos">Todas</option>
                    @foreach($especialidades as $esp)
                        <option value="esp-{{ $esp->IdEspecialidad }}">{{ $esp->DescripcionEspe }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tabla tipo horario --}}
            <div class="overflow-x-auto">
                <table class="horario-table min-w-full text-xs border-separate border-spacing-y-1">
                    <thead class="bg-slate-50">
                    <tr>
                        <th class="px-3 py-2 w-32 text-left text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                            Hora
                        </th>
                        @foreach($dias as $dia)
                            <th class="px-3 py-2 text-center text-[11px] font-semibold text-slate-500 uppercase tracking-wide">
                                {{ $dia }}
                            </th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($horas as $franja)
                        <tr>
                            {{-- Columna de hora --}}
                            <td class="horario-hora px-3 py-2 text-[12px] font-semibold text-slate-600">
                                {{ $franja }}
                            </td>

                            {{-- Columnas de días --}}
                            @foreach($dias as $dia)
                                <td class="horario-cell px-2 py-2 rounded-r-lg border border-slate-100">
                                    @foreach($especialidades as $esp)
                                        @php
                                            $doctoresCelda = $horario[$esp->IdEspecialidad][$dia][$franja] ?? collect();
                                        @endphp

                                        @if($doctoresCelda->count())
                                            <div class="bloque-horario esp-{{ $esp->IdEspecialidad }}
                                                        inline-flex items-center justify-center
                                                        mb-1 px-3 py-1.5 text-[11px] font-semibold
                                                        text-white bg-indigo-600
                                                        hover:bg-indigo-700 transition-colors duration-150">
                                                @foreach($doctoresCelda as $doc)
                                                    <span class="whitespace-nowrap">
                                                        {{ $doc->empleado->Nombres }} {{ $doc->empleado->Apellidos }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const select = document.getElementById('filtro-especialidad');

            select.addEventListener('change', () => {
                const value = select.value; // 'todos' o 'esp-XX'

                document.querySelectorAll('.bloque-horario').forEach(div => {
                    div.classList.add('hidden');
                });

                if (value === 'todos') {
                    document.querySelectorAll('.bloque-horario').forEach(div => {
                        div.classList.remove('hidden');
                    });
                } else {
                    document.querySelectorAll('.' + value).forEach(div => {
                        div.classList.remove('hidden');
                    });
                }
            });
        });
    </script>
</x-app-layout>

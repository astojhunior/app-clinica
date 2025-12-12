<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ Auth::user()->isAdmin() ? 'Consultas - Panel Administrativo' : 'Mis Consultas' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 rounded">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filtros (solo para admin) --}}
            @if(Auth::user()->isAdmin())
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                    <div class="p-6">
                        <form method="GET" action="{{ route('consultas.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                {{-- Fecha --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                                    <input type="date" name="fecha" value="{{ request('fecha') }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                                </div>

                                {{-- Estado --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                    <select name="estado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                                        <option value="">Todos</option>
                                        <option value="Programada" {{ request('estado') == 'Programada' ? 'selected' : '' }}>Programada</option>
                                        <option value="Confirmada" {{ request('estado') == 'Confirmada' ? 'selected' : '' }}>Confirmada</option>
                                        <option value="En Progreso" {{ request('estado') == 'En Progreso' ? 'selected' : '' }}>En Progreso</option>
                                    </select>
                                </div>

                                {{-- Doctor (solo admin) --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Doctor</label>
                                    <select name="doctor" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                                        <option value="">Todos</option>
                                        @foreach($doctores as $doctor)
                                            <option value="{{ $doctor->IdDoctores }}"
                                                    {{ request('doctor') == $doctor->IdDoctores ? 'selected' : '' }}>
                                                Dr. {{ $doctor->empleado->Nombres }} {{ $doctor->empleado->Apellidos }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Botón buscar --}}
                                <div class="flex items-end">
                                    <button type="submit"
                                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md">
                                        Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Contador --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg mb-6">
                <div class="p-6 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Consultas {{ Auth::user()->isAdmin() ? 'Disponibles' : 'Programadas' }}
                    </h3>
                    <div class="text-sm text-gray-600">
                        {{ $citas->count() }} consulta{{ $citas->count() !== 1 ? 's' : '' }}
                        @if(!Auth::user()->isAdmin())
                            para hoy
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tarjetas de Citas --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6">
                    @if($citas->isEmpty())
                        <div class="text-center py-12">
                            <div class="text-gray-400 mb-4">
                                @if(Auth::user()->isAdmin())
                                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                @else
                                    <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">
                                @if(Auth::user()->isAdmin())
                                    No hay consultas disponibles
                                @else
                                    No tienes consultas programadas
                                @endif
                            </h3>
                            @if(!Auth::user()->isAdmin())
                                <p class="text-gray-500">
                                    Tus próximas consultas aparecerán aquí.
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($citas as $cita)
                                <div class="cita-card bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                                    {{-- Header con paciente --}}
                                    <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-blue-600 font-medium text-sm">
                                                        {{ substr($cita->paciente->Nombres, 0, 1) }}{{ substr($cita->paciente->Apellidos, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 text-sm">
                                                        {{ $cita->paciente->Nombres }} {{ $cita->paciente->Apellidos }}
                                                    </h4>
                                                    <p class="text-xs text-gray-500">{{ $cita->paciente->Dni }}</p>
                                                </div>
                                            </div>

                                            {{-- Estado --}}
                                            @php
                                                $estadoClass = match($cita->Estado) {
                                                    'Programada' => 'bg-blue-100 text-blue-800',
                                                    'Confirmada' => 'bg-green-100 text-green-800',
                                                    'En Progreso' => 'bg-yellow-100 text-yellow-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $estadoClass }}">
                                                {{ $cita->Estado }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Contenido principal --}}
                                    <div class="p-4">
                                        {{-- Fecha y Hora --}}
                                        <div class="mb-3">
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600">Fecha</span>
                                                <span class="font-medium text-gray-900">
                                                    {{ \Carbon\Carbon::parse($cita->FechaCita)->format('d/m/Y') }}
                                                </span>
                                            </div>
                                            <div class="flex items-center justify-between text-sm">
                                                <span class="text-gray-600">Hora</span>
                                                <span class="font-semibold text-indigo-600">
                                                    {{ \Carbon\Carbon::parse($cita->HoraInicio)->format('H:i') }}
                                                    @if($cita->HoraFin)
                                                        - {{ \Carbon\Carbon::parse($cita->HoraFin)->format('H:i') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Tipo de cita y modalidad --}}
                                        <div class="grid grid-cols-2 gap-2 mb-3">
                                            <div class="text-xs">
                                                <span class="text-gray-500">Tipo:</span>
                                                <span class="font-medium text-gray-900 block">
                                                    {{ $cita->tipoCita->Descripcion ?? 'Consulta' }}
                                                </span>
                                            </div>
                                            <div class="text-xs">
                                                <span class="text-gray-500">Modalidad:</span>
                                                <span class="font-medium {{ $cita->Modalidad === 'Presencial' ? 'text-green-600' : 'text-purple-600' }} block">
                                                    {{ $cita->Modalidad }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Motivo --}}
                                        @if($cita->MotivoCita)
                                            <div class="mb-4">
                                                <span class="text-xs font-semibold text-gray-600">Motivo:</span>
                                                <p class="mt-1 text-sm text-gray-600 line-clamp-3">{{ $cita->MotivoCita }}</p>
                                            </div>
                                        @endif

                                        {{-- Acciones --}}
                                        {{-- Acciones --}}
                                        <div class="flex flex-col space-y-2 pt-3 border-t">
                                            @if($cita->Estado === 'Programada' || $cita->Estado === 'Confirmada')
                                                {{-- Iniciar Consulta --}}
                                                <form method="POST" action="{{ route('consultas.iniciar', $cita->IdCita) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                                                        Iniciar consulta
                                                    </button>
                                                </form>

                                                @if(Auth::user()->isAdmin())
                                                    <a href="{{ route('citas.show', $cita->IdCita) }}"
                                                    class="text-center text-indigo-600 hover:text-indigo-800 text-sm font-medium py-2 px-4 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                                        Ver detalles
                                                    </a>
                                                @endif

                                            @elseif($cita->Estado === 'En Progreso')
                                                {{-- Ir a la ficha clínica --}}
                                                <a href="{{ route('consultas.create', $cita->IdCita) }}"
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg text-center transition-colors">
                                                    Ver / editar consulta
                                                </a>

                                              {{-- Finalizar consulta --}}
                                                <form method="POST"
                                                    action="{{ route('consultas.finalizar', $cita->IdCita) }}"
                                                    class="space-y-2 mt-1">
                                                    @csrf

                                                    <div class="text-xs text-gray-600">
                                                        <label class="block mb-1" for="hora-fin-{{ $cita->IdCita }}">
                                                            Hora de término
                                                        </label>
                                                        <input type="time"
                                                            id="hora-fin-{{ $cita->IdCita }}"
                                                            name="HoraFin"
                                                            value="{{ old('HoraFin', $cita->HoraFin ?? \Carbon\Carbon::now()->format('H:i')) }}"
                                                            required
                                                            class="w-full rounded-md border-gray-300 text-sm">
                                                    </div>

                                                    <button type="submit"
                                                            class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                                                        Finalizar consulta
                                                    </button>
                                                </form>


                                                @if(Auth::user()->isAdmin())
                                                    <a href="{{ route('citas.edit', $cita->IdCita) }}"
                                                    class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg hover:bg-yellow-200 text-sm font-medium transition-colors">
                                                        Editar cita
                                                    </a>
                                                @endif

                                            @elseif(Auth::user()->isAdmin())
                                                {{-- Solo admin para completadas/canceladas --}}
                                                <a href="{{ route('citas.show', $cita->IdCita) }}"
                                                class="text-center text-gray-600 hover:text-gray-800 text-sm font-medium py-2 px-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                    Ver registro
                                                </a>
                                            @endif
                                        </div>

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('form[action*="/consultas/iniciar"]').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        if (!confirm('¿Iniciar esta consulta?')) {
                            e.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>

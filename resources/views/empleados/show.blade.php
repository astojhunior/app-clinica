<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle del Empleado
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white p-6 rounded shadow">
            <p><strong>DNI:</strong> {{ $empleado->Dni }}</p>
            <p><strong>Nombre:</strong> {{ $empleado->Nombres }} {{ $empleado->Apellidos }}</p>
            <p><strong>Cargo:</strong> {{ $empleado->cargo->DescripcionCargo ?? 'N/A' }}</p>
        </div>
    </div>
</x-app-layout>

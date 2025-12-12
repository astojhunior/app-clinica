<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Pago</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded shadow border border-gray-200">
            <form method="POST" action="{{ route('pagos.update', $pago->IdPago) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Fecha de Pago (solo lectura) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Pago</label>
                        <input type="text"
                            value="{{ \Carbon\Carbon::parse($pago->FechaPago)->format('d/m/Y H:i') }}"
                            readonly
                            class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-800 shadow-sm focus:outline-none">
                    </div>
                    <!-- Monto del pago -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Monto Pagado</label>
                        <input type="number"
                               name="MontoTotal"
                               step="0.01"
                               min="0.01"
                               required
                               value="{{ $pago->MontoTotal }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('MontoTotal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Método -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Método de Pago</label>
                        <select name="MetodoPago" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Efectivo" {{ $pago->MetodoPago == 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="Tarjeta" {{ $pago->MetodoPago == 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                            <option value="Transferencia" {{ $pago->MetodoPago == 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                        </select>
                        @error('MetodoPago') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <!-- Estado de Pago (readonly o editable) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Pago</label>
                        <select name="EstadoPago" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="1" {{ $pago->EstadoPago == 1 ? 'selected' : '' }}>Pagado</option>
                            <option value="0" {{ $pago->EstadoPago == 0 ? 'selected' : '' }}>Pendiente</option>
                        </select>
                        @error('EstadoPago') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Observaciones o notas manuales si deseas -->
                <div class="mt-6 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition shadow">
                        Actualizar Pago
                    </button>
                    <form method="POST" action="{{ route('pagos.destroy', $pago->IdPago) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition shadow"
                                onclick="return confirm('¿Seguro que deseas anular/ELIMINAR este pago?')">
                            Anular/Eliminar Pago
                        </button>
                    </form>
                    <a href="{{ route('pagos.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-700 transition shadow">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

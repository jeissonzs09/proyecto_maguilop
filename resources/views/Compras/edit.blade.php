x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">✏ Editar Compra #{{ $compra->CompraID }}</h2>
    </x-slot>

    <div class="p-6 max-w-3xl mx-auto bg-white shadow-md rounded-md">
        <form action="{{ route('compras.update', $compra->CompraID) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="ProveedorID" class="block text-gray-700 font-semibold mb-2">Proveedor</label>
                <select name="ProveedorID" id="ProveedorID" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled {{ old('ProveedorID', $compra->ProveedorID) ? '' : 'selected' }}>Seleccionar proveedor</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->ProveedorID }}"
                            {{ old('ProveedorID', $compra->ProveedorID) == $proveedor->ProveedorID ? 'selected' : '' }}>
                            {{ $proveedor->Descripcion }}
                        </option>
                    @endforeach
                </select>
                @error('ProveedorID') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="EmpleadoID" class="block text-gray-700 font-semibold mb-2">Empleado</label>
                <select name="EmpleadoID" id="EmpleadoID" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="" disabled {{ old('EmpleadoID', $compra->EmpleadoID) ? '' : 'selected' }}>Seleccionar empleado</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->EmpleadoID }}"
                            {{ old('EmpleadoID', $compra->EmpleadoID) == $empleado->EmpleadoID ? 'selected' : '' }}>
                            {{ $empleado->persona->NombreCompleto ?? 'Sin Nombre' }}
                        </option>
                    @endforeach
                </select>
                @error('EmpleadoID') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="FechaCompra" class="block text-gray-700 font-semibold mb-2">Fecha de Compra</label>
                <input type="datetime-local" name="FechaCompra" id="FechaCompra" required
                       value="{{ old('FechaCompra', \Carbon\Carbon::parse($compra->FechaCompra)->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                @error('FechaCompra') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="TotalCompra" class="block text-gray-700 font-semibold mb-2">Total de Compra (Lps.)</label>
                <input type="number" step="0.01" name="TotalCompra" id="TotalCompra" required
                       value="{{ old('TotalCompra', $compra->TotalCompra) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                @error('TotalCompra') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="Estado" class="block text-gray-700 font-semibold mb-2">Estado</label>
                <select name="Estado" id="Estado" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="Recibido" {{ old('Estado', $compra->Estado) == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                    <option value="Pendiente" {{ old('Estado', $compra->Estado) == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="Cancelado" {{ old('Estado', $compra->Estado) == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
                @error('Estado') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 text-end">
                <a href="{{ route('compras.index') }}"
                   class="bg-red-600 text-white font-bold py-2 px-4 rounded inline-flex items-center mr-2">❌ Cancelar</a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center gap-2">
                    💾 Actualizar Compra
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
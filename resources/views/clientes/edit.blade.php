<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">✏ Editar Cliente #{{ $cliente->ClienteID }}</h2>
    </x-slot>

    <div class="p-6 max-w-3xl mx-auto bg-white shadow-md rounded-md">
        <form action="{{ route('clientes.update', $cliente->ClienteID) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            

            <div>
                <label for="NombreCliente" class="block text-gray-700 font-semibold mb-2">Nombre Cliente</label>
                <input type="text" name="NombreCliente" id="NombreCliente" value="{{ old('NombreCliente', $cliente->NombreCliente) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                @error('NombreCliente') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="PersonaID" class="block text-gray-700 font-semibold mb-2">Persona ID</label>
                <input type="number" name="PersonaID" id="PersonaID" value="{{ old('PersonaID', $cliente->PersonaID) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                @error('PersonaID') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
    <label for="Categoria" class="block text-gray-700 font-semibold mb-2">Categoría</label>
    <select name="Categoria" id="Categoria"
        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <option value="" disabled {{ old('Categoria', $cliente->Categoria ?? '') == '' ? 'selected' : '' }}>Selecciona una categoría</option>
        <option value="Regular" {{ old('Categoria', $cliente->Categoria ?? '') == 'Regular' ? 'selected' : '' }}>Regular</option>
        <option value="Premium" {{ old('Categoria', $cliente->Categoria ?? '') == 'Premium' ? 'selected' : '' }}>Premium</option>
        <option value="VIP" {{ old('Categoria', $cliente->Categoria ?? '') == 'VIP' ? 'selected' : '' }}>VIP</option>
    </select>
    @error('Categoria') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
</div>

            <div>
                <label for="FechaRegistro" class="block text-gray-700 font-semibold mb-2">Fecha de Registro</label>
                <input type="date" name="FechaRegistro" id="FechaRegistro" value="{{ old('FechaRegistro', \Carbon\Carbon::parse($cliente->FechaRegistro)->format('Y-m-d')) }}" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                @error('FechaRegistro') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="Estado" class="block text-gray-700 font-semibold mb-2">Estado</label>
                <select name="Estado" id="Estado" required
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="Activo" {{ old('Estado', $cliente->Estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ old('Estado', $cliente->Estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('Estado') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="Notas" class="block text-gray-700 font-semibold mb-2">Notas</label>
                <textarea name="Notas" id="Notas" rows="3"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('Notas', $cliente->Notas) }}</textarea>
                @error('Notas') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mt-6 text-end">
                <a href="{{ route('clientes.index') }}"
                   style="background-color: #dc2626; color: white; font-weight: bold; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                   ❌ Cancelar
                </a>
                <button type="submit"
                        style="background-color: #2563eb; color: white; font-weight: bold; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; border: none;">
                    💾 Actualizar Cliente
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

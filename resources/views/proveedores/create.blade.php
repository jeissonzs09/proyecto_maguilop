<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold">📦 Nuevo Proveedor</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form action="{{ route('proveedores.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Selector de Persona --}}
<div>
    <label for="PersonaID" class="block text-sm font-medium text-gray-700 mb-1">Persona</label>
    <select name="PersonaID" id="PersonaID"
            class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
            required>
        <option value="">Seleccione una persona</option>
        @foreach ($personas as $persona)
            <option value="{{ $persona->PersonaID }}"
                {{ old('PersonaID') == $persona->PersonaID ? 'selected' : '' }}>
                {{ $persona->NombreCompleto }}
            </option>
        @endforeach
    </select>
</div>

{{-- Selector de Empresa --}}
<div class="mt-4">
    <label for="EmpresaID" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
    <select name="EmpresaID" id="EmpresaID"
            class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
        <option value="">Seleccione una empresa</option>
        @foreach ($empresas as $empresa)
            <option value="{{ $empresa->EmpresaID }}"
                {{ old('EmpresaID') == $empresa->EmpresaID ? 'selected' : '' }}>
                {{ $empresa->NombreEmpresa }}
            </option>
        @endforeach
    </select>
</div>


            <div class="mb-4">
                <label for="RTN" class="block text-gray-700 font-bold mb-2">RTN</label>
                <input type="text" name="RTN" id="RTN" placeholder="Ej: 08011985123960"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('RTN') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="Descripcion" class="block text-gray-700 font-bold mb-2">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Descripción del proveedor"></textarea>
                @error('Descripcion') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="URL_Website" class="block text-gray-700 font-bold mb-2">Sitio Web</label>
                <input type="url" name="URL_Website" id="URL_Website" placeholder="https://ejemplo.com"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('URL_Website') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="Ubicacion" class="block text-gray-700 font-bold mb-2">Ubicación</label>
                <input type="text" name="Ubicacion" id="Ubicacion" placeholder="Ej: Ciudad, País"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('Ubicacion') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="Telefono" class="block text-gray-700 font-bold mb-2">Teléfono</label>
                <input type="text" name="Telefono" id="Telefono" placeholder="Ej: +504 1234-5678"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('Telefono') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="CorreoElectronico" class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                <input type="email" name="CorreoElectronico" id="CorreoElectronico" placeholder="correo@ejemplo.com"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('CorreoElectronico') <p class="text-red-600 mt-1 text-sm">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label for="TipoProveedor" class="block text-gray-700 font-bold mb-2">Tipo Proveedor</label>
                <select name="TipoProveedor" id="TipoProveedor" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="Local">Local</option>
                    <option value="Internacional">Internacional</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="FechaRegistro" class="block text-gray-700 font-bold mb-2">Fecha de Registro</label>
                <input type="date" name="FechaRegistro" id="FechaRegistro" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>

            <div class="mb-4">
                <label for="Estado" class="block text-gray-700 font-bold mb-2">Estado</label>
                <select name="Estado" id="Estado" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="Activo">Activo</option>
                    <option value="Inactivo">Inactivo</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="Notas" class="block text-gray-700 font-bold mb-2">Notas</label>
                <textarea name="Notas" id="Notas" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Notas adicionales"></textarea>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('proveedores.index') }}"
                   class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    ❌ Cancelar
                     </a>
                <button type="submit"
                        style="background-color: #2563eb; color: white; font-weight: bold; padding: 10px 20px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); border: none;">
                    💾 Guardar Proveedor
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
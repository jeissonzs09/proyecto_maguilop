<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-industry"></i> Editar Proveedor
        </h2>
    </x-slot>

    <div class="p-6 max-w-4xl mx-auto bg-white rounded-md shadow-md">
        {{-- Mensajes de error --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded shadow text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Formulario --}}
        <form method="POST" action="{{ route('proveedores.update', $proveedor->ProveedorID) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Persona --}}
            <div>
                <label for="PersonaID" class="block text-sm font-medium text-gray-700 mb-1">Persona</label>
                <select name="PersonaID" id="PersonaID" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required>
                    <option value="">Seleccione una persona</option>
                    @foreach ($personas as $persona)
                        <option value="{{ $persona->PersonaID }}"
                            {{ old('PersonaID', $proveedor->PersonaID) == $persona->PersonaID ? 'selected' : '' }}>
                            {{ $persona->NombreCompleto }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Empresa --}}
            <div>
                <label for="EmpresaID" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                <select name="EmpresaID" id="EmpresaID" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
                    <option value="">Seleccione una empresa</option>
                    @foreach ($empresas as $empresa)
                        <option value="{{ $empresa->EmpresaID }}"
                            {{ old('EmpresaID', $proveedor->EmpresaID) == $empresa->EmpresaID ? 'selected' : '' }}>
                            {{ $empresa->NombreEmpresa }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Otros campos --}}
            <div>
                <label for="RTN" class="block text-sm font-medium text-gray-700 mb-1">RTN</label>
                <input type="text" name="RTN" id="RTN" value="{{ old('RTN', $proveedor->RTN) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">{{ old('Descripcion', $proveedor->Descripcion) }}</textarea>
            </div>

            <div>
                <label for="URL_Website" class="block text-sm font-medium text-gray-700 mb-1">Sitio Web</label>
                <input type="text" name="URL_Website" id="URL_Website" value="{{ old('URL_Website', $proveedor->URL_Website) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                <input type="text" name="Ubicacion" id="Ubicacion" value="{{ old('Ubicacion', $proveedor->Ubicacion) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="Telefono" id="Telefono" value="{{ old('Telefono', $proveedor->Telefono) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="CorreoElectronico" class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                <input type="email" name="CorreoElectronico" id="CorreoElectronico" value="{{ old('CorreoElectronico', $proveedor->CorreoElectronico) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="TipoProveedor" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                <input type="text" name="TipoProveedor" id="TipoProveedor" value="{{ old('TipoProveedor', $proveedor->TipoProveedor) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <input type="text" name="Estado" id="Estado" value="{{ old('Estado', $proveedor->Estado) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="FechaRegistro" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                <input type="date" name="FechaRegistro" id="FechaRegistro"
                    value="{{ old('FechaRegistro', $proveedor->FechaRegistro) }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            {{-- Botones --}}
            <div class="pt-4 flex justify-end space-x-4">
                <a href="{{ route('proveedores.index') }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition">
                    Cancelar
                </a>

                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition">
                    Actualizar proveedor
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

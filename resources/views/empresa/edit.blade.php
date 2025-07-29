<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-building"></i> Editar Empresa
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
        <form method="POST" action="{{ route('empresa.update', $empresa->EmpresaID) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="NombreEmpresa" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" id="NombreEmpresa" name="NombreEmpresa"
                       value="{{ old('NombreEmpresa', $empresa->NombreEmpresa) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                       required>
            </div>

            <div>
                <label for="Website" class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="text" id="Website" name="Website"
                       value="{{ old('Website', $empresa->Website) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                <input type="text" id="Telefono" name="Telefono"
                       value="{{ old('Telefono', $empresa->Telefono) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Direccion" class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                <input type="text" id="Direccion" name="Direccion"
                       value="{{ old('Direccion', $empresa->Direccion) }}"
                       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                <textarea id="Descripcion" name="Descripcion" rows="3"
                          class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">{{ old('Descripcion', $empresa->Descripcion) }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-4">
                <a href="{{ route('empresa.index') }}"
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition">
                    Cancelar
                </a>

                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition">
                    Actualizar Empresa
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

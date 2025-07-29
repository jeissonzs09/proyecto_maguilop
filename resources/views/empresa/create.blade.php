<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800">➕ Registrar Empresa</h2>
    </x-slot>

    <div class="p-6 max-w-4xl mx-auto bg-white rounded-md shadow-md">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded shadow">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('empresa.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="NombreEmpresa" class="block font-semibold text-gray-700 mb-1">Nombre</label>
                <input type="text" name="NombreEmpresa" id="NombreEmpresa" value="{{ old('NombreEmpresa') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required>
            </div>

            <div>
                <label for="Website" class="block font-semibold text-gray-700 mb-1">Website</label>
                <input type="text" name="Website" id="Website" value="{{ old('Website') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Telefono" class="block font-semibold text-gray-700 mb-1">Teléfono</label>
                <input type="text" name="Telefono" id="Telefono" value="{{ old('Telefono') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Direccion" class="block font-semibold text-gray-700 mb-1">Dirección</label>
                <input type="text" name="Direccion" id="Direccion" value="{{ old('Direccion') }}"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="Descripcion" class="block font-semibold text-gray-700 mb-1">Descripción</label>
                <textarea name="Descripcion" id="Descripcion" rows="3"
                    class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">{{ old('Descripcion') }}</textarea>
            </div>

            <div class="pt-4 flex justify-end space-x-4">
                <a href="{{ route('empresa.index') }}"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition">
                    Cancelar
                </a>

                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition">
                    Registrar Empresa
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

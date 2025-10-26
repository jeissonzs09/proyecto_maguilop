<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Crear Rol
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto mt-6 bg-white p-6 rounded-lg shadow">
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            {{-- Descripción --}}
            <div class="mb-4 relative">
                <label for="Descripcion" class="block text-sm font-medium text-gray-700">Descripción del Rol</label>
                <input type="text" name="Descripcion" id="Descripcion"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500"
                       value="{{ old('Descripcion') }}" maxlength="255" required>
                <p id="charCount" class="absolute right-0 mt-1 text-xs text-gray-500">255 caracteres restantes</p>
                <p id="errorDescripcion" class="text-sm text-red-600 mt-1 hidden"></p>
                @error('Descripcion')
                    <p data-error-backend class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div class="mb-4">
                <label for="Estado" class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="Estado" id="Estado"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Activo" {{ old('Estado') == 'Activo' ? 'selected' : '' }}>Activo</option>
                    <option value="Inactivo" {{ old('Estado') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
                @error('Estado')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botones --}}
            <div class="flex justify-between mt-6">
                <a href="{{ route('roles.index') }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts --}}
    <script>
        const descripcionInput = document.getElementById('Descripcion');
        const errorDescripcion = document.getElementById('errorDescripcion');
        const charCount = document.getElementById('charCount');
        const backendError = document.querySelector('[data-error-backend]');
        const maxLength = 255;

        descripcionInput.addEventListener('input', function () {
            // Normalizar texto
            this.value = this.value
                .toUpperCase()
                .replace(/[^A-ZÁÉÍÓÚÑ\s]/g, '') // Solo letras y espacios
                .replace(/^\s+/, '');           // Quitar espacios iniciales

            const value = this.value.trim();
            const remaining = maxLength - value.length;
            charCount.textContent = ${remaining} caracteres restantes;

            // --- ocultar error backend cuando el usuario escribe ---
            if (backendError) {
                backendError.style.display = 'none';
            }

            // --- limpiar mensaje de error dinámico ---
            errorDescripcion.textContent = "";
            errorDescripcion.classList.add('hidden');

            // --- validaciones ---
            if (value === "") {
                return; // no validar si está vacío
            }

            if (value.length < 4) {
                errorDescripcion.textContent = "El rol debe tener al menos 4 caracteres.";
                errorDescripcion.classList.remove('hidden');
                return;
            }

            // Lista estática de duplicados (ejemplo)
            const duplicados = ["ADMIN", "USUARIO"];
            if (duplicados.includes(value)) {
                errorDescripcion.textContent = "Este rol ya existe.";
                errorDescripcion.classList.remove('hidden');
                return;
            }
        });

        // Extra: al perder foco, si el campo está vacío, ocultar mensajes
        descripcionInput.addEventListener('blur', function () {
            if (this.value.trim() === "") {
                errorDescripcion.textContent = "";
                errorDescripcion.classList.add('hidden');
                if (backendError) backendError.style.display = 'none';
            }
        });
    </script>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-users"></i> Usuarios
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div x-data="usuarioModales()" x-init="initUsuarios()">
        {{-- Barra de búsqueda y botón crear --}}
        <div class="flex justify-between items-center mb-4">
            {{-- Búsqueda --}}
            <div class="flex-1 max-w-md">
                <form method="GET" action="{{ route('usuarios.index') }}" class="flex gap-2">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Buscar por usuario, correo o rol..."
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button type="submit" 
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('usuarios.index') }}" 
                           class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            {{-- Botón de crear usuario --}}
            @if($permisos::tienePermiso('Usuarios', 'crear'))
                <button @click="openModal"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
                    <i class="fas fa-user-plus"></i> Nuevo usuario
                </button>
            @endif
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Usuario</th>
                        <th class="px-4 py-3 text-left">Rol</th>
                        <th class="px-4 py-3 text-left">Correo</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $usuario->UsuarioID }}</td>
                            <td class="px-4 py-2">{{ $usuario->NombreUsuario }}</td>
                            <td class="px-4 py-2">{{ strtoupper($usuario->TipoUsuario) }}</td>
                            <td class="px-4 py-2">{{ $usuario->CorreoElectronico }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Editar --}}
                                    @if($permisos::tienePermiso('Usuarios', 'editar'))
                                        <a href="#"
                                           @click.prevent="openEditModal({{ Js::from($usuario) }})"
                                           class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif

                                    {{-- Eliminar --}}
                                    @if($permisos::tienePermiso('Usuarios', 'eliminar'))
                                        <form action="{{ route('usuarios.destroy', $usuario->UsuarioID) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full"
                                                    title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                @if(request('search'))
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-search text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-lg font-medium">No se encontraron usuarios</p>
                                        <p class="text-sm">No hay resultados para "{{ request('search') }}"</p>
                                        <a href="{{ route('usuarios.index') }}" 
                                           class="mt-2 text-blue-600 hover:text-blue-800 underline">
                                            Ver todos los usuarios
                                        </a>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users text-4xl text-gray-300 mb-2"></i>
                                        <p class="text-lg font-medium">No hay usuarios registrados</p>
                                        <p class="text-sm">Comienza creando tu primer usuario</p>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            
        </div>
        <div class="mt-4">
            {{ $usuarios->appends(request()->query())->links() }}
        </div>


        {{-- Modal de Crear Usuario --}}
        <div
            x-show="show"
            style="display: none"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.window.escape="closeModal()"
        >
            <div class="bg-white w-full max-w-lg rounded-lg p-6 relative">
                <button @click="closeModal"
                        class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-xl font-bold"
                        aria-label="Cerrar modal">
                    &times;
                </button>

                <h2 class="text-xl font-bold mb-4">👤 Crear Usuario</h2>

                <form action="{{ route('usuarios.store') }}" method="POST">
                    @csrf

                    {{-- Nombre de Usuario --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nombre de Usuario</label>
                        <input type="text" name="NombreUsuario" required
                               minlength="3" maxlength="30" 
                               pattern="^[A-Z0-9_]+$"
                               title="Solo letras mayúsculas, números y guiones bajos (3-30 caracteres)"
                               class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200 uppercase"
                               oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, '')" />
                        <p class="text-xs text-gray-500 mt-1">Solo letras mayúsculas, números y guiones bajos (3-30 caracteres)</p>
                    </div>

                    {{-- Rol --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                        <select name="TipoUsuario" required
                                class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200">
                            <option value="">Seleccione un rol</option>
                            @foreach($roles as $id => $descripcion)
                                <option value="{{ $descripcion }}">{{ $descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Correo --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="correo" required
                               minlength="5" maxlength="100"
                               pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                               title="Ingrese un correo válido sin espacios"
                               class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200 lowercase"
                               oninput="this.value = this.value.toLowerCase().replace(/\s/g, '')" />
                        <p class="text-xs text-gray-500 mt-1">Correo válido sin espacios (5-100 caracteres)</p>
                    </div>
                    {{-- Empleado --}}
                    <div class="mb-4">
                        <label for="EmpleadoID" class="block text-sm font-medium text-gray-700">Empleado</label>
                        <select name="EmpleadoID" class="w-full border rounded px-3 py-2">
                            <option value="">Seleccione un empleado</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->EmpleadoID }}">
                                    {{ $empleado->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Fecha de Vencimiento --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                        <input type="date" name="FechaVencimiento" 
                               value="{{ now()->addDays(365)->format('Y-m-d') }}" 
                               min="{{ now()->format('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200" />
                        <p class="text-xs text-gray-500 mt-1">Fecha de vencimiento del usuario (mínimo: hoy)</p>
                    </div>

                    {{-- Botones --}}
                    <div class="flex justify-end mt-4 gap-3">
                        <button type="button" @click="closeModal"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Guardar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Editar Usuario --}}
        <div
            x-show="showEdit"
            x-cloak
            style="display: none"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @keydown.window.escape="closeEditModal()"
        >
            <div class="bg-white w-full max-w-xl rounded-lg p-6 relative max-h-[90vh] overflow-auto">
                <button
                    @click="closeEditModal()"
                    class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-xl font-bold"
                    aria-label="Cerrar modal"
                >&times;</button>

                <form :action="`/usuarios/${usuarioEditar.UsuarioID}`" method="POST" x-transition>
                    @csrf
                    @method('PUT')

                    <h2 class="text-xl font-bold mb-4">✏️ Editar Usuario</h2>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nombre de Usuario</label>
                        <input type="text" name="nombre_usuario" required
                               minlength="3" maxlength="30" 
                               pattern="^[A-Z0-9_]+$"
                               title="Solo letras mayúsculas, números y guiones bajos (3-30 caracteres)"
                               class="w-full border rounded px-3 py-2 uppercase"
                               x-model="usuarioEditar.NombreUsuario"
                               @input="$event.target.value = $event.target.value.toUpperCase().replace(/[^A-Z0-9_]/g, '')">
                        <p class="text-xs text-gray-500 mt-1">Solo letras mayúsculas, números y guiones bajos (3-30 caracteres)</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="correo" required
                               minlength="5" maxlength="100"
                               pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                               title="Ingrese un correo válido sin espacios"
                               class="w-full border rounded px-3 py-2 lowercase"
                               x-model="usuarioEditar.CorreoElectronico"
                               @input="$event.target.value = $event.target.value.toLowerCase().replace(/\s/g, '')">
                        <p class="text-xs text-gray-500 mt-1">Correo válido sin espacios (5-100 caracteres)</p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Rol</label>
                        <select name="rol" required class="w-full border rounded px-3 py-2" x-model="usuarioEditar.TipoUsuario">
                            <option value="">Seleccione un rol</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol }}">{{ $rol }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Empleado</label>
                        <select name="empleado" required class="w-full border rounded px-3 py-2" x-model="usuarioEditar.EmpleadoID">
                            <option value="">Seleccione un empleado</option>
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->EmpleadoID }}">
                                    {{ $empleado->nombre_completo ?? 'Empleado #' . $empleado->EmpleadoID }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                        <input type="date" name="FechaVencimiento" 
                               min="{{ now()->format('Y-m-d') }}"
                               class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200"
                               x-model="usuarioEditar.FechaVencimiento" />
                        <p class="text-xs text-gray-500 mt-1">Fecha de vencimiento del usuario (mínimo: hoy)</p>
                    </div>

                    <div class="flex justify-between mt-6">
                        <button type="button" @click="closeEditModal()"
                                class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded">❌ Cancelar</button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded">💾 Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function usuarioModales() {
        return {
            show: false,
            showEdit: false,
            usuarioEditar: null,

            openModal() {
                this.show = true;
            },

            closeModal() {
                this.show = false;
            },

            openEditModal(usuario) {
                this.usuarioEditar = { ...usuario };
                this.showEdit = true;
            },

            closeEditModal() {
                this.showEdit = false;
                this.usuarioEditar = null;
            },

            initUsuarios() {
                @if(session('success'))
                    Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
                @endif
                @if(session('error'))
                    Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
                @endif
                
            }
        };
    }

    </script>

    @php
    $toastType = session('error') ? 'error' : (session('success') ? 'success' : null);
    $toastMsg  = session('error') ?: session('success');
@endphp

@if($toastType)
    <div
        id="toast-persona"
        role="status" aria-live="polite"
        class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
               text-white px-10 py-6 rounded-full shadow-2xl flex items-center gap-5
               z-50 animate-fadeIn text-xl font-semibold ring-1 ring-white/20
               max-w-[90vw]"
        style="min-width: 420px; background-color: {{ $toastType === 'error' ? '#dc2626' : '#16a34a' }};"
        onclick="this.remove()"
    >
        @if($toastType === 'error')
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <line x1="15" y1="9" x2="9" y2="15" />
                <line x1="9" y1="9" x2="15" y2="15" />
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10" />
                <path d="M9 12l2 2l4-4" />
            </svg>
        @endif

        <span class="leading-snug break-words">{{ $toastMsg }}</span>
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-persona');
            if (toast) {
                toast.style.transition = 'opacity .5s ease, transform .5s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translate(-50%, -50%) scale(.95)';
                setTimeout(() => toast.remove(), 500);
            }
        }, 3200);
    </script>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -48%) scale(.97); }
            to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }
        .animate-fadeIn { animation: fadeIn .28s ease forwards; }
    </style>
@endif

</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-industry"></i> Proveedores
        </h2>
    </x-slot>

  @php
        $personaLogueada = auth()->user()->persona;
    @endphp

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4" x-data="proveedoresModal()" x-init="init()">
        {{-- Buscador + Exportar + Nuevo --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500="buscar()"
                        placeholder="Buscar proveedores..."
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none text-sm"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M21 21l-4.35-4.35m1.44-5.4a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <a :href="`{{ route('proveedores.exportarPDF') }}?search=${search}`"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            @if($permisos::tienePermiso('Proveedores', 'crear'))
                <button
                    @click="abrirModalCrear()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </button>
            @endif
        </div>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3 text-center">ID</th>
                        <th class="px-4 py-3 text-center">Persona</th>
                        <th class="px-4 py-3 text-center">Empresa</th>
                        <th class="px-4 py-3 text-center">RTN</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-left">Sitio Web</th>
                        <th class="px-4 py-3 text-left">Ubicación</th>
                        <th class="px-4 py-3 text-left">Teléfono</th>
                        <th class="px-4 py-3 text-left">Correo</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-center">Registro</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-center">Notas</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($proveedores as $proveedor)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $proveedor->ProveedorID }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->persona->NombreCompleto ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->empresa->NombreEmpresa ?? '—' }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->RTN }}</td>
                            <td class="px-4 py-2">{{ $proveedor->Descripcion }}</td>
                            <td class="px-4 py-2">{{ $proveedor->URL_Website }}</td>
                            <td class="px-4 py-2">{{ $proveedor->Ubicacion }}</td>
                            <td class="px-4 py-2">{{ $proveedor->Telefono }}</td>
                            <td class="px-4 py-2">{{ $proveedor->CorreoElectronico }}</td>
                            <td class="px-4 py-2">{{ $proveedor->TipoProveedor }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->FechaRegistro }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->Estado }}</td>
                            <td class="px-4 py-2 text-center">{{ $proveedor->Notas }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Proveedores', 'editar'))
                                        <button
                                            @click="abrirModalEditar({
                                                ProveedorID: {{ $proveedor->ProveedorID }},
                                                PersonaID: {{ $proveedor->PersonaID ?? 'null' }},
                                                EmpresaID: {{ $proveedor->EmpresaID ?? 'null' }},
                                                RTN: '{{ $proveedor->RTN }}',
                                                Descripcion: `{{ addslashes($proveedor->Descripcion) }}`,
                                                URL_Website: '{{ $proveedor->URL_Website }}',
                                                Ubicacion: '{{ $proveedor->Ubicacion }}',
                                                Telefono: '{{ $proveedor->Telefono }}',
                                                CorreoElectronico: '{{ $proveedor->CorreoElectronico }}',
                                                TipoProveedor: '{{ $proveedor->TipoProveedor }}',
                                                FechaRegistro: '{{ \Carbon\Carbon::parse($proveedor->FechaRegistro)->format('Y-m-d') }}',
                                                Estado: '{{ $proveedor->Estado }}',
                                                Notas: `{{ addslashes($proveedor->Notas ?? '') }}`
                                            })"
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                    @if($permisos::tienePermiso('Proveedores', 'eliminar'))
                                        <form action="{{ route('proveedores.destroy', $proveedor->ProveedorID) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')">
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
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        <div class="mt-4">
            {{ $proveedores->appends(['search' => request('search')])->links() }}
        </div>

        {{-- Modal Crear --}}
        <div
            x-show="modalCrear"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display:none;"
        >
            <div @click.away="modalCrear = false"
                 class="bg-white rounded-lg shadow-lg max-w-4xl w-full p-6 relative overflow-auto max-h-[90vh]">
                <button @click="modalCrear = false" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-xl font-bold mb-4">➕ Nuevo Proveedor</h2>

                <form action="{{ route('proveedores.store') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Mostrar el nombre del usuario --}}
@php
    $usuario = auth()->user();
    $nombre = $usuario->persona->NombreCompleto ?? $usuario->name ?? 'Usuario sin nombre';
@endphp

<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Persona</label>
    <input type="text" value="{{ $nombre }}" disabled
           class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 bg-gray-100 text-gray-700" />
    <input type="hidden" name="PersonaID"
           value="{{ $usuario->persona->PersonaID ?? '' }}">
</div>



                    <div>
                        <label for="EmpresaID" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <select name="EmpresaID" id="EmpresaID" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
                            <option value="">Seleccione una empresa</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->EmpresaID }}">{{ $empresa->NombreEmpresa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="RTN" class="block text-sm font-medium text-gray-700 mb-1">RTN</label>
                        <input type="text" name="RTN" id="RTN" required
       minlength="14" maxlength="14"
       pattern="^\d{14}$"
       title="Debe contener exactamente 14 dígitos numéricos"
       oninput="this.value = this.value.replace(/\D/g, '')"
       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">

                    </div>

                    <div>
                        <label for="Descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="Descripcion" id="Descripcion" rows="3" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"></textarea>
                    </div>

                    <div>
                        <label for="URL_Website" class="block text-sm font-medium text-gray-700 mb-1">Sitio Web</label>
                        <input type="url" name="URL_Website" id="URL_Website" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
                    </div>

                    <div>
                        <label for="Ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <input type="text" name="Ubicacion" id="Ubicacion" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
                    </div>

                    <div>
                        <label for="Telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="Telefono" id="Telefono" required
       minlength="8" maxlength="10"
       pattern="^\d{8,10}$"
       title="Ingrese solo números (mínimo 8 y máximo 10 dígitos)"
       oninput="this.value = this.value.replace(/\D/g, '')"
       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
                    </div>

                    <div>
                        <label for="CorreoElectronico" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="CorreoElectronico" id="CorreoElectronico" required
       title="Ingrese un correo válido (ej. ejemplo@dominio.com)"
       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
                    </div>

                    <div>
                        <label for="TipoProveedor" class="block text-sm font-medium text-gray-700 mb-1">Tipo Proveedor</label>
                        <select name="TipoProveedor" id="TipoProveedor" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required>
                            <option value="">Seleccione tipo</option>
                            <option value="Local">Local</option>
                            <option value="Internacional">Internacional</option>
                        </select>
                    </div>

                    <div>
                        <label for="FechaRegistro" class="block text-sm font-medium text-gray-700 mb-1">Fecha Registro</label>
                        <input type="date" name="FechaRegistro" id="FechaRegistro" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required>
                    </div>

                    <div>
                        <label for="Estado" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="Estado" id="Estado" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required>
                            <option value="">Seleccione estado</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div>
                        <label for="Notas" class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea name="Notas" id="Notas" rows="3" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="modalCrear = false" class="px-4 py-2 border rounded">Cancelar</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Guardar</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Editar --}}
        <div
            x-show="modalEditar"
            x-transition.opacity
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display:none;"
        >
            <div @click.away="modalEditar = false"
                 class="bg-white rounded-lg shadow-lg max-w-4xl w-full p-6 relative overflow-auto max-h-[90vh]">
                <button @click="modalEditar = false" class="absolute top-3 right-3 text-gray-600 hover:text-gray-900" title="Cerrar">
                    <i class="fas fa-times"></i>
                </button>

                <h2 class="text-xl font-bold mb-4">✏️ Editar Proveedor #<span x-text="proveedorEditar ? proveedorEditar.ProveedorID : ''"></span></h2>

                <form :action="proveedorEditar ? `/proveedores/${proveedorEditar.ProveedorID}` : '#'" method="POST" x-show="proveedorEditar" x-cloak class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="PersonaID_edit" class="block text-sm font-medium text-gray-700 mb-1">Persona</label>
                        <select name="PersonaID" id="PersonaID_edit" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required x-model="proveedorEditar.PersonaID">
                            <option value="">Seleccione una persona</option>
                            @foreach ($personas as $persona)
                                <option value="{{ $persona->PersonaID }}">{{ $persona->NombreCompleto }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="EmpresaID_edit" class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                        <select name="EmpresaID" id="EmpresaID_edit" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" x-model="proveedorEditar.EmpresaID">
                            <option value="">Seleccione una empresa</option>
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->EmpresaID }}">{{ $empresa->NombreEmpresa }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="RTN_edit" class="block text-sm font-medium text-gray-700 mb-1">RTN</label>
                        <input type="text" name="RTN" id="RTN_edit" required
       minlength="14" maxlength="14"
       pattern="^\d{14}$"
       title="Debe contener exactamente 14 dígitos numéricos"
       oninput="this.value = this.value.replace(/\D/g, '')"
       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
       x-model="proveedorEditar.RTN">
                    </div>

                    <div>
                        <label for="Descripcion_edit" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="Descripcion" id="Descripcion_edit" rows="3" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" x-model="proveedorEditar.Descripcion"></textarea>
                    </div>

                    <div>
                        <label for="URL_Website_edit" class="block text-sm font-medium text-gray-700 mb-1">Sitio Web</label>
                        <input type="url" name="URL_Website" id="URL_Website_edit" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" x-model="proveedorEditar.URL_Website">
                    </div>

                    <div>
                        <label for="Ubicacion_edit" class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                        <input type="text" name="Ubicacion" id="Ubicacion_edit" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" x-model="proveedorEditar.Ubicacion">
                    </div>

                    <div>
                        <label for="Telefono_edit" class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="Telefono" id="Telefono_edit" required
       minlength="8" maxlength="10"
       pattern="^\d{8,10}$"
       title="Ingrese solo números (mínimo 8 y máximo 10 dígitos)"
       oninput="this.value = this.value.replace(/\D/g, '')"
       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
       x-model="proveedorEditar.Telefono">
                    </div>

                    <div>
                        <label for="CorreoElectronico_edit" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                        <input type="email" name="CorreoElectronico" id="CorreoElectronico_edit" required
       title="Ingrese un correo válido (ej. ejemplo@dominio.com)"
       class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
       x-model="proveedorEditar.CorreoElectronico">
                    </div>

                    <div>
                        <label for="TipoProveedor_edit" class="block text-sm font-medium text-gray-700 mb-1">Tipo Proveedor</label>
                        <select name="TipoProveedor" id="TipoProveedor_edit" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required x-model="proveedorEditar.TipoProveedor">
                            <option value="">Seleccione tipo</option>
                            <option value="Local">Local</option>
                            <option value="Internacional">Internacional</option>
                        </select>
                    </div>

                    <div>
                        <label for="FechaRegistro_edit" class="block text-sm font-medium text-gray-700 mb-1">Fecha Registro</label>
                        <input type="date" name="FechaRegistro" id="FechaRegistro_edit" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required x-model="proveedorEditar.FechaRegistro">
                    </div>

                    <div>
                        <label for="Estado_edit" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="Estado" id="Estado_edit" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" required x-model="proveedorEditar.Estado">
                            <option value="">Seleccione estado</option>
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>

                    <div>
                        <label for="Notas_edit" class="block text-sm font-medium text-gray-700 mb-1">Notas</label>
                        <textarea name="Notas" id="Notas_edit" rows="3" class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200" x-model="proveedorEditar.Notas"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" @click="modalEditar = false" class="px-4 py-2 border rounded">Cancelar</button>
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Alpine.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function proveedoresModal() {
            return {
                search: '{{ request("search") }}',
                modalCrear: false,
                modalEditar: false,
                proveedorEditar: null,

                init() {
                    // Opcional: puedes colocar lógica al iniciar
                },

                buscar() {
                    window.location.href = '?search=' + encodeURIComponent(this.search);
                },

                abrirModalCrear() {
                    this.modalCrear = true;
                },

                abrirModalEditar(proveedor) {
                    this.proveedorEditar = proveedor;
                    this.modalEditar = true;
                },
            }
        }
    </script>
</x-app-layout>
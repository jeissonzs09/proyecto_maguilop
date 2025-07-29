<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-box"></i> Productos
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div x-data="productosModal()" class="">

        <div class="flex justify-between items-center mb-6 flex-wrap gap-3">

            {{-- Buscador y PDF --}}
            <div class="flex items-center gap-3 flex-wrap">
                <div class="relative max-w-xs w-full sm:w-64">
                    <input
                        type="text"
                        x-data="{ search: '{{ request('search') }}' }"
                        x-model="search"
                        @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                        placeholder="Buscar producto..."
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none text-sm"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m1.44-5.4a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <a href="{{ route('producto.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            {{-- Botón Nuevo producto para abrir modal --}}
            @if($permisos::tienePermiso('Productos', 'crear'))
                <button
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nuevo producto
                </button>
            @endif

        </div>

        {{-- Tabla productos --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nombre Producto</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-right">Precio Compra</th>
                        <th class="px-4 py-3 text-right">Precio Venta</th>
                        <th class="px-4 py-3 text-center">Stock</th>
                        <th class="px-4 py-3 text-center">Proveedor</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($productos as $producto)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $producto->ProductoID }}</td>
                            <td class="px-4 py-2">{{ $producto->NombreProducto }}</td>
                            <td class="px-4 py-2">{{ $producto->Descripcion }}</td>
                            <td class="px-4 py-2 text-right">L. {{ number_format($producto->PrecioCompra, 2) }}</td>
                            <td class="px-4 py-2 text-right">L. {{ number_format($producto->PrecioVenta, 2) }}</td>
                            <td class="px-4 py-2 text-center">{{ $producto->Stock }}</td>
                            <td class="px-4 py-2 text-center">{{ $producto->proveedor->Descripcion ?? 'Sin proveedor' }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Productos', 'editar'))
                                        <button
                                            @click="openEditModal({
                                                ProductoID: {{ $producto->ProductoID }},
                                                NombreProducto: '{{ addslashes($producto->NombreProducto) }}',
                                                Descripcion: '{{ addslashes($producto->Descripcion) }}',
                                                PrecioCompra: {{ $producto->PrecioCompra }},
                                                PrecioVenta: {{ $producto->PrecioVenta }},
                                                Stock: {{ $producto->Stock }},
                                                ProveedorID: {{ $producto->ProveedorID ?? 'null' }}
                                            })"
                                            class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif

                                    @if($permisos::tienePermiso('Productos', 'eliminar'))
                                        <form action="{{ route('producto.destroy', $producto->ProductoID) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este producto?')">
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

        <div class="mt-4">
            {{ $productos->appends(['search' => request('search')])->links() }}
        </div>

        <!-- Modal Crear Producto -->
        <div
            x-show="showCreate"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display: none;"
            x-transition
        >
            <div
                @click.away="closeCreateModal()"
                class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative"
                x-transition
            >
                <button
                    @click="closeCreateModal()"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold"
                    title="Cerrar"
                >&times;</button>

                <h3 class="text-xl font-semibold mb-4">➕ Registrar Producto</h3>

                <form method="POST" action="{{ route('producto.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="NombreProductoCreate" class="block text-sm font-medium text-gray-700 mb-1">Nombre del producto</label>
                        <input type="text" name="NombreProducto" id="NombreProductoCreate" required
                               class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring focus:ring-indigo-200"
                               x-model="createForm.NombreProducto">
                    </div>

                    <div>
                        <label for="DescripcionCreate" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="Descripcion" id="DescripcionCreate" rows="3" required
                                  class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring focus:ring-indigo-200"
                                  x-model="createForm.Descripcion"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="PrecioCompraCreate" class="block text-sm font-medium text-gray-700 mb-1">Precio compra (Lps.)</label>
                            <input type="number" step="0.01" name="PrecioCompra" id="PrecioCompraCreate" required
                                   class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                   x-model="createForm.PrecioCompra">
                        </div>

                        <div>
                            <label for="PrecioVentaCreate" class="block text-sm font-medium text-gray-700 mb-1">Precio venta (Lps.)</label>
                            <input type="number" step="0.01" name="PrecioVenta" id="PrecioVentaCreate" required
                                   class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                   x-model="createForm.PrecioVenta">
                        </div>

                        <div>
                            <label for="StockCreate" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="Stock" id="StockCreate" required
                                   class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                   x-model="createForm.Stock">
                        </div>
                    </div>

                    <div>
                        <label for="ProveedorIDCreate" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select name="ProveedorID" id="ProveedorIDCreate" required
                                class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                x-model="createForm.ProveedorID">
                            <option value="">Selecciona un proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->ProveedorID }}">{{ $proveedor->Descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 flex justify-end space-x-4">
                        <button type="button"
                                @click="closeCreateModal()"
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition">
                            Cancelar
                        </button>

                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition">
                            Registrar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Editar Producto -->
        <div
            x-show="showEdit"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            style="display: none;"
            x-transition
        >
            <div
                @click.away="closeEditModal()"
                class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative"
                x-transition
            >
                <button
                    @click="closeEditModal()"
                    class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold"
                    title="Cerrar"
                >&times;</button>

                <h3 class="text-xl font-semibold mb-4">✏️ Editar Producto</h3>

                <form
                    method="POST"
                    :action="`{{ url('producto') }}/${editForm.ProductoID}`"
                    class="space-y-6"
                >
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="NombreProductoEdit" class="block text-sm font-medium text-gray-700 mb-1">Nombre del producto</label>
                        <input type="text" name="NombreProducto" id="NombreProductoEdit" required
                               class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                               x-model="editForm.NombreProducto">
                    </div>

                    <div>
                        <label for="DescripcionEdit" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="Descripcion" id="DescripcionEdit" rows="3" required
                                  class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                  x-model="editForm.Descripcion"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="PrecioCompraEdit" class="block text-sm font-medium text-gray-700 mb-1">Precio compra (Lps.)</label>
                            <input type="number" step="0.01" name="PrecioCompra" id="PrecioCompraEdit" required
                                   class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                   x-model="editForm.PrecioCompra">
                        </div>

                        <div>
                            <label for="PrecioVentaEdit" class="block text-sm font-medium text-gray-700 mb-1">Precio venta (Lps.)</label>
                            <input type="number" step="0.01" name="PrecioVenta" id="PrecioVentaEdit" required
                                   class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                   x-model="editForm.PrecioVenta">
                        </div>

                        <div>
                            <label for="StockEdit" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="Stock" id="StockEdit" required
                                   class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                   x-model="editForm.Stock">
                        </div>
                    </div>

                    <div>
                        <label for="ProveedorIDEdit" class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <select name="ProveedorID" id="ProveedorIDEdit" required
                                class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                                x-model="editForm.ProveedorID">
                            <option value="">Selecciona un proveedor</option>
                            @foreach ($proveedores as $proveedor)
                                <option value="{{ $proveedor->ProveedorID }}"
                                    :selected="editForm.ProveedorID == {{ $proveedor->ProveedorID }}">
                                    {{ $proveedor->Descripcion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4 flex justify-end space-x-4">
                        <button type="button"
                                @click="closeEditModal()"
                                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition">
                            Cancelar
                        </button>

                        <button type="submit"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function productosModal() {
            return {
                showCreate: false,
                showEdit: false,
                search: '',
                createForm: {
                    NombreProducto: '',
                    Descripcion: '',
                    PrecioCompra: '',
                    PrecioVenta: '',
                    Stock: '',
                    ProveedorID: '',
                },
                editForm: {
                    ProductoID: '',
                    NombreProducto: '',
                    Descripcion: '',
                    PrecioCompra: '',
                    PrecioVenta: '',
                    Stock: '',
                    ProveedorID: '',
                },

                openCreateModal() {
                    this.resetCreateForm();
                    this.showCreate = true;
                },
                closeCreateModal() {
                    this.showCreate = false;
                },

                openEditModal(producto) {
                    this.editForm.ProductoID = producto.ProductoID;
                    this.editForm.NombreProducto = producto.NombreProducto;
                    this.editForm.Descripcion = producto.Descripcion;
                    this.editForm.PrecioCompra = producto.PrecioCompra;
                    this.editForm.PrecioVenta = producto.PrecioVenta;
                    this.editForm.Stock = producto.Stock;
                    this.editForm.ProveedorID = producto.ProveedorID;
                    this.showEdit = true;
                },
                closeEditModal() {
                    this.showEdit = false;
                },

                resetCreateForm() {
                    this.createForm = {
                        NombreProducto: '',
                        Descripcion: '',
                        PrecioCompra: '',
                        PrecioVenta: '',
                        Stock: '',
                        ProveedorID: '',
                    };
                },
            }
        }
    </script>

    {{-- Asegúrate de incluir Alpine.js en tu layout principal --}}
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

</x-app-layout>

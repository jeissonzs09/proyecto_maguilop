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

            {{-- Botón Nuevo producto --}}
            @if($permisos::tienePermiso('Productos', 'crear'))
                <button
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap"
                >
                    <i class="fas fa-plus"></i> Nuevo producto
                </button>
            @endif

            <!-- Visor de imagen grande -->
<div
  x-show="viewerOpen"
  style="display:none"
  class="fixed inset-0 bg-black/70 z-[70] flex items-center justify-center p-4"
  @click.self="closeViewer()"
  @keydown.window.escape="closeViewer()"
>
  <div class="relative max-w-4xl max-h-[90vh]">
    <button
      type="button"
      @click.stop="closeViewer()"
      class="absolute -top-3 -right-3 bg-white text-gray-700 rounded-full shadow p-2 leading-none"
      aria-label="Cerrar"
    >&times;</button>

    <img :src="viewerSrc" alt="Imagen del producto"
         class="max-w-full max-h-[90vh] rounded shadow-lg">
  </div>
</div>

        </div>

        {{-- Tabla productos --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-center">Foto</th>        {{-- Nuevo --}}
                        <th class="px-4 py-3 text-left">Código</th>        {{-- Nuevo --}}
                        <th class="px-4 py-3 text-left">Nombre Producto</th>
                        <th class="px-4 py-3 text-left">Área</th>          {{-- Nuevo --}}
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

                            {{-- Miniatura de la foto --}}
                            <td class="px-4 py-2 text-center">
  @if(!empty($producto->Foto))
    <img
      src="{{ asset('storage/'.$producto->Foto) }}"
      alt="Foto"
      class="h-10 w-10 object-cover rounded border mx-auto cursor-pointer"
      @click="openViewer($event.target.src)"
    >
  @else
    <span class="text-gray-400 text-xs">Sin foto</span>
  @endif
</td>


                            {{-- Código --}}
                            <td class="px-4 py-2">{{ $producto->Codigo }}</td>

                            {{-- Nombre --}}
                            <td class="px-4 py-2">{{ $producto->NombreProducto }}</td>

                            {{-- Área --}}
                            <td class="px-4 py-2">{{ $producto->Area }}</td>

                            {{-- Descripción --}}
                            <td class="px-4 py-2">{{ $producto->Descripcion }}</td>

                            {{-- Precios y stock --}}
                            <td class="px-4 py-2 text-right">L. {{ number_format($producto->PrecioCompra, 2) }}</td>
                            <td class="px-4 py-2 text-right">L. {{ number_format($producto->PrecioVenta, 2) }}</td>
                            <td class="px-4 py-2 text-center">{{ $producto->Stock }}</td>
                            <td class="px-4 py-2 text-center">{{ $producto->proveedor->Descripcion ?? 'Sin proveedor' }}</td>

                            {{-- Acciones --}}
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('Productos', 'editar'))
                                        <button
                                            @click="openEditModal({
                                                ProductoID: {{ $producto->ProductoID }},
                                                Codigo: '{{ addslashes($producto->Codigo) }}',
                                                NombreProducto: '{{ addslashes($producto->NombreProducto) }}',
                                                Descripcion: '{{ addslashes($producto->Descripcion) }}',
                                                Area: '{{ $producto->Area }}',
                                                PrecioCompra: {{ $producto->PrecioCompra }},
                                                PrecioVenta: {{ $producto->PrecioVenta }},
                                                Stock: {{ $producto->Stock }},
                                                ProveedorID: {{ $producto->ProveedorID ?? 'null' }},
                                                Foto: '{{ $producto->Foto }}'
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
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-6"
    style="display: none;"
    x-transition
    @keydown.window.escape="closeCreateModal()"
>
  <div
      @click.away="closeCreateModal()"
      class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[85vh] overflow-y-auto"
      x-transition
      x-data="{ zoom:false, zoomUrl:'' }"  <!-- estado local para ampliar imagen -->
  >
    <button
        @click="closeCreateModal()"
        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900 text-2xl font-bold"
        title="Cerrar"
    >&times;</button>

    <h3 class="text-xl font-semibold mb-4">➕ Registrar Producto</h3>

    {{-- Importante para la foto: usa enctype multipart --}}
    <form method="POST"
          action="{{ route('producto.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
      @csrf

      <!-- Código autogenerado -->
      <div class="flex items-end gap-3">
        <div class="grow">
          <label for="CodigoCreate" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
          <input type="text" name="Codigo" id="CodigoCreate"
                 x-model="createForm.Codigo" readonly
                 class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 bg-gray-100 text-gray-700">
        </div>
        <button type="button"
                @click="genCodigo()"
                class="h-10 px-3 rounded-md bg-slate-600 text-white hover:bg-slate-700">
          Regenerar
        </button>
      </div>

      <div>
        <label for="NombreProductoCreate" class="block text-sm font-medium text-gray-700 mb-1">Nombre del producto</label>
        <input type="text" name="NombreProducto" id="NombreProductoCreate" required
               maxlength="60"
               x-model="createForm.NombreProducto"
               @input="
                 createForm.NombreProducto = $event.target.value
                   .replace(/[^\p{L}\p{N} .,:;()\-#\/'\"°%]/gu,'')
                   .replace(/\s+/g,' ')
                   .trimStart();
               "
               class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
        @error('NombreProducto') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="DescripcionCreate" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
        <textarea name="Descripcion" id="DescripcionCreate" rows="3" required
                  maxlength="200"
                  x-model="createForm.Descripcion"
                  @input="
                    createForm.Descripcion = $event.target.value
                      .replace(/[^\p{L}\p{N} .,:;()\-#\/'\"°%]/gu,'')
                      .replace(/\s+/g,' ')
                      .trimStart();
                  "
                  class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring focus:ring-indigo-200"></textarea>
        @error('Descripcion') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <!-- Área -->
      <div>
        <label for="AreaCreate" class="block text-sm font-medium text-gray-700 mb-1">Área</label>
        <select name="Area" id="AreaCreate" required
                x-model="createForm.Area"
                class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
          <option value="">Selecciona un área</option>
          <option value="Electronica">Electrónica</option>
          <option value="Refrigeracion">Refrigeración</option>
        </select>
        @error('Area') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label for="PrecioCompraCreate" class="block text-sm font-medium text-gray-700 mb-1">Precio compra (Lps.)</label>
          <input type="number" step="0.01" name="PrecioCompra" id="PrecioCompraCreate" required
                 class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                 x-model="createForm.PrecioCompra">
          @error('PrecioCompra') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="PrecioVentaCreate" class="block text-sm font-medium text-gray-700 mb-1">Precio venta (Lps.)</label>
          <input type="number" step="0.01" name="PrecioVenta" id="PrecioVentaCreate" required
                 class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                 x-model="createForm.PrecioVenta">
          @error('PrecioVenta') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror

          <p x-show="Number(createForm.PrecioVenta) < Number(createForm.PrecioCompra)"
             class="text-red-600 text-xs mt-1">El precio de venta no puede ser menor que el de compra.</p>
        </div>

        <div>
          <label for="StockCreate" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
          <input type="number" name="Stock" id="StockCreate" required
                 class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                 x-model="createForm.Stock">
          @error('Stock') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <!-- Fotografía -->
      <div>
        <label for="FotoCreate" class="block text-sm font-medium text-gray-700 mb-1">Fotografía del producto</label>
        <input type="file" name="Foto" id="FotoCreate" accept="image/*"
               @change="onFotoChange($event)"
               class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2">
        @error('Foto') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror

        <!-- Miniatura clickeable -->
        <template x-if="createForm.FotoPreview">
          <img
            :src="createForm.FotoPreview"
            alt="Vista previa"
            class="mt-2 h-28 w-28 object-cover rounded border cursor-pointer"
            @click="zoomUrl = createForm.FotoPreview; zoom = true"
          >
        </template>

        <!-- Modal para ver imagen en grande -->
        <div
          x-show="zoom"
          style="display:none"
          class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[60]"
          @click.self="zoom=false"
        >
          <div class="bg-white p-3 rounded-lg shadow-lg max-w-3xl max-h-[90vh] overflow-auto relative">
            <button
  type="button"
  @click.stop.prevent="zoom = false"
  class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl leading-none"
  aria-label="Cerrar"
>&times;</button>
            <img :src="zoomUrl" alt="Foto producto" class="max-w-full max-h-[80vh] mx-auto rounded">
          </div>
        </div>
      </div>

      <!-- Proveedor -->
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
        @error('ProveedorID') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="pt-4 flex justify-end space-x-4">
        <button type="button"
                @click="closeCreateModal()"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition">
          Cancelar
        </button>

        <button type="submit"
          :disabled="
            createForm.NombreProducto.trim().length < 3 ||
            createForm.Descripcion.trim().length < 2 ||
            !createForm.Area || !createForm.Codigo
          "
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
          Registrar Producto
        </button>
      </div>
    </form>
  </div>
</div>


        <!-- Modal Editar Producto -->
<div
    x-show="showEdit"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 overflow-y-auto py-6"
    style="display: none;"
    x-transition
    @keydown.window.escape="closeEditModal()"
>
  <div
      @click.away="closeEditModal()"
      class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative max-h-[85vh] overflow-y-auto"
      x-transition
      x-data="{ zoom:false, zoomUrl:'' }"
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
        enctype="multipart/form-data"
        class="space-y-6"
    >
      @csrf
      @method('PUT')

      <!-- Código (solo lectura) -->
      <div>
        <label for="CodigoEdit" class="block text-sm font-medium text-gray-700 mb-1">Código</label>
        <input type="text" id="CodigoEdit" name="Codigo"
               x-model="editForm.Codigo" readonly
               class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 bg-gray-100 text-gray-700">
      </div>

      <div>
        <label for="NombreProductoEdit" class="block text-sm font-medium text-gray-700 mb-1">Nombre del producto</label>
        <input type="text" name="NombreProducto" id="NombreProductoEdit" required
               maxlength="60"
               x-model="editForm.NombreProducto"
               @input="
                 editForm.NombreProducto = $event.target.value
                   .replace(/[^\p{L}\p{N} .,:;()\-#\/'\"°%]/gu,'')
                   .replace(/\s+/g,' ')
                   .trimStart();
               "
               class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
        @error('NombreProducto') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label for="DescripcionEdit" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
        <textarea name="Descripcion" id="DescripcionEdit" rows="3" required
                  maxlength="200"
                  x-model="editForm.Descripcion"
                  @input="
                    editForm.Descripcion = $event.target.value
                      .replace(/[^\p{L}\p{N} .,:;()\-#\/'\"°%]/gu,'')
                      .replace(/\s+/g,' ')
                      .trimStart();
                  "
                  class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:outline-none focus:ring focus:ring-indigo-200"></textarea>
        @error('Descripcion') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <!-- Área -->
      <div>
        <label for="AreaEdit" class="block text-sm font-medium text-gray-700 mb-1">Área</label>
        <select name="Area" id="AreaEdit" required
                x-model="editForm.Area"
                class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200">
          <option value="">Selecciona un área</option>
          <option value="Electronica">Electrónica</option>
          <option value="Refrigeracion">Refrigeración</option>
        </select>
        @error('Area') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <label for="PrecioCompraEdit" class="block text-sm font-medium text-gray-700 mb-1">Precio compra (Lps.)</label>
          <input type="number" step="0.01" name="PrecioCompra" id="PrecioCompraEdit" required
                 class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                 x-model="editForm.PrecioCompra">
          @error('PrecioCompra') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="PrecioVentaEdit" class="block text-sm font-medium text-gray-700 mb-1">Precio venta (Lps.)</label>
          <input type="number" step="0.01" name="PrecioVenta" id="PrecioVentaEdit" required
                 class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                 x-model="editForm.PrecioVenta">
          @error('PrecioVenta') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror

          <p x-show="Number(editForm.PrecioVenta) < Number(editForm.PrecioCompra)"
             class="text-red-600 text-xs mt-1">El precio de venta no puede ser menor que el de compra.</p>
        </div>

        <div>
          <label for="StockEdit" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
          <input type="number" name="Stock" id="StockEdit" required
                 class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring focus:ring-indigo-200"
                 x-model="editForm.Stock">
          @error('Stock') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <!-- Fotografía (actual y reemplazo) -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Fotografía del producto</label>

        <!-- Actual -->
        <template x-if="editForm.FotoUrl && !editForm.FotoPreview">
          <img
            :src="editForm.FotoUrl"
            alt="Foto actual"
            class="mb-2 h-28 w-28 object-cover rounded border cursor-pointer"
            @click="zoomUrl = editForm.FotoUrl; zoom = true"
          >
        </template>

        <!-- Nueva (preview) -->
        <template x-if="editForm.FotoPreview">
          <img
            :src="editForm.FotoPreview"
            alt="Nueva foto"
            class="mb-2 h-28 w-28 object-cover rounded border cursor-pointer"
            @click="zoomUrl = editForm.FotoPreview; zoom = true"
          >
        </template>

        <input type="file" name="Foto" id="FotoEdit" accept="image/*"
               @change="onEditFotoChange($event)"
               class="w-full border-gray-300 rounded-md shadow-sm px-4 py-2">
        @error('Foto') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        <p class="text-xs text-gray-500 mt-1">Si no eliges archivo, se mantiene la imagen actual.</p>

        <!-- Modal para ver imagen en grande -->
        <div
          x-show="zoom"
          style="display:none"
          class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[60]"
          @click.self="zoom=false"
        >
          <div class="bg-white p-3 rounded-lg shadow-lg max-w-3xl max-h-[90vh] overflow-auto relative">
            <button
  type="button"
  @click.stop.prevent="zoom = false"
  class="absolute top-2 right-2 text-gray-600 hover:text-gray-900 text-2xl leading-none"
  aria-label="Cerrar"
>&times;</button>
            <img :src="zoomUrl" alt="Foto producto" class="max-w-full max-h-[80vh] mx-auto rounded">
          </div>
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
        @error('ProveedorID') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
      </div>

      <div class="pt-4 flex justify-end space-x-4">
        <button type="button"
                @click="closeEditModal()"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition">
          Cancelar
        </button>

        <button type="submit"
          :disabled="
            editForm.NombreProducto.trim().length < 3 ||
            editForm.Descripcion.trim().length < 2 ||
            !editForm.Area
          "
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed">
          Guardar cambios
        </button>
      </div>
    </form>
  </div>
</div>


    <script>
        function productosModal() {
            return {

                viewerOpen: false,
viewerSrc: '',
openViewer(src) { this.viewerSrc = src; this.viewerOpen = true; },
closeViewer()   { this.viewerOpen = false; this.viewerSrc = ''; },
                showCreate: false,
                showEdit: false,
                search: '',

                // Crear
                createForm: {
                    Codigo: '',
                    NombreProducto: '',
                    Descripcion: '',
                    Area: '',
                    PrecioCompra: '',
                    PrecioVenta: '',
                    Stock: '',
                    ProveedorID: '',
                    Foto: null,
                    FotoPreview: ''
                    
                },

                // Editar
                editForm: {
                    ProductoID: '',
                    Codigo: '',
                    NombreProducto: '',
                    Descripcion: '',
                    Area: '',
                    PrecioCompra: '',
                    PrecioVenta: '',
                    Stock: '',
                    ProveedorID: '',
                    FotoPreview: '',
                    FotoUrl: '' // URL pública actual (asset('storage/...'))
                },

                // --- Métodos ---
                genCodigo() {
                    const pad = n => n.toString().padStart(4, '0');
                    const rnd = pad(Math.floor(Math.random() * 10000));
                    const y = new Date().getFullYear().toString().slice(-2);
                    const m = String(new Date().getMonth() + 1).padStart(2, '0');
                    const d = String(new Date().getDate()).padStart(2, '0');
                    this.createForm.Codigo = `PRD-${y}${m}${d}-${rnd}`;
                },

                onFotoChange(e) {
                    const file = e.target.files[0];
                    if (!file) { this.createForm.Foto = null; this.createForm.FotoPreview = ''; return; }
                    if (!['image/jpeg','image/png','image/webp','image/jpg'].includes(file.type)) {
                        alert('Solo imágenes JPG, PNG o WEBP');
                        e.target.value = '';
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) { // 2MB
                        alert('La imagen no debe superar 2 MB');
                        e.target.value = '';
                        return;
                    }
                    this.createForm.Foto = file;
                    const reader = new FileReader();
                    reader.onload = ev => this.createForm.FotoPreview = ev.target.result;
                    reader.readAsDataURL(file);
                },

                onEditFotoChange(e) {
                    const file = e.target.files[0];
                    if (!file) { this.editForm.FotoPreview = ''; return; }
                    if (!['image/jpeg','image/png','image/webp','image/jpg'].includes(file.type)) {
                        alert('Solo imágenes JPG, PNG o WEBP');
                        e.target.value = '';
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) { // 2MB
                        alert('La imagen no debe superar 2 MB');
                        e.target.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = ev => this.editForm.FotoPreview = ev.target.result;
                    reader.readAsDataURL(file);
                },

                openCreateModal() {
                    this.resetCreateForm();
                    this.genCodigo();
                    this.showCreate = true;
                },
                closeCreateModal() {
                    this.showCreate = false;
                },

                openEditModal(producto) {
                    this.editForm.ProductoID   = producto.ProductoID;
                    this.editForm.Codigo       = producto.Codigo ?? '';
                    this.editForm.NombreProducto = producto.NombreProducto;
                    this.editForm.Descripcion  = producto.Descripcion;
                    this.editForm.Area         = producto.Area ?? '';
                    this.editForm.PrecioCompra = producto.PrecioCompra;
                    this.editForm.PrecioVenta  = producto.PrecioVenta;
                    this.editForm.Stock        = producto.Stock;
                    this.editForm.ProveedorID  = producto.ProveedorID;
                    this.editForm.FotoUrl      = producto.Foto ? `{{ asset('storage') }}/${producto.Foto}` : '';
                    this.editForm.FotoPreview  = '';
                    this.showEdit = true;
                },
                closeEditModal() {
                    this.showEdit = false;
                },

                resetCreateForm() {
                    this.createForm = {
                        Codigo: '',
                        NombreProducto: '',
                        Descripcion: '',
                        Area: '',
                        PrecioCompra: '',
                        PrecioVenta: '',
                        Stock: '',
                        ProveedorID: '',
                        Foto: null,
                        FotoPreview: ''
                    };
                },
            }
        }
    </script>

    {{-- Toasts --}}
    @php
        $toastType = session('error') ? 'error' : (session('success') ? 'success' : null);
        $toastMsg  = session('error') ?: session('success');
    @endphp

    @if($toastType)
        <div
            id="toast-empleado"
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
                const toast = document.getElementById('toast-empleado');
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
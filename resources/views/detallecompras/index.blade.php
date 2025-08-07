<x-app-layout> 
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-file-invoice-dollar"></i> Detalle Compras
        </h2>
    </x-slot>

    @php
        $permisos = \App\Helpers\PermisosHelper::class;
    @endphp

    <div class="p-4" x-data="detalleCompraHandler()">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                {{-- Buscador reactivo con lupa --}}
                <div class="relative max-w-xs w-full">
                    <input
                        type="text"
                        x-model="search"
                        @input.debounce.500="window.location.href = '?search=' + encodeURIComponent(search)"
                        placeholder="Buscar detalle de compra..."
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

                {{-- Botón Exportar PDF --}}
                <a href="{{ route('detallecompras.exportarPDF', ['search' => request('search')]) }}"
                   class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </a>
            </div>

            {{-- Botón Nuevo Detalle de Compra --}}
            @if($permisos::tienePermiso('DetalleCompras', 'crear'))
                <button @click="abrirModalCrear" 
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow whitespace-nowrap">
                    <i class="fas fa-plus"></i> Nuevo detalle de compra
                </button>
            @endif
        </div>

        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-center">Compra ID</th>
                        <th class="px-4 py-3 text-center">Producto</th>
                        <th class="px-4 py-3 text-center">Cantidad</th>
                        <th class="px-4 py-3 text-center">Precio Unitario</th>
                        <th class="px-4 py-3 text-center">Subtotal</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($detalleCompras as $detalle)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2 text-center">{{ $detalle->CompraID }}</td>
                            <td class="px-4 py-2 text-center">{{ $detalle->producto->NombreProducto ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-center">{{ $detalle->Cantidad }}</td>
                            <td class="px-4 py-2 text-center">L. {{ number_format($detalle->PrecioUnitario, 2) }}</td>
                            <td class="px-4 py-2 text-center">L. {{ number_format($detalle->Subtotal, 2) }}</td>
                            <td class="px-4 py-2 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($permisos::tienePermiso('DetalleCompras', 'editar'))
                                        <button @click="abrirModalEditar({{ $detalle }})" 
                                                class="bg-yellow-400 hover:bg-yellow-500 text-white p-2 rounded-full" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif

                                    @if($permisos::tienePermiso('DetalleCompras', 'eliminar'))
                                        <form action="{{ route('detallecompras.destroy', $detalle->DetalleCompraID) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este detalle de compra?')">
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

     <!-- Modal Crear Detalle -->
<template x-if="modalCrear">
    <div
        class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="cerrarModales"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md relative p-6 border border-indigo-300">
            <!-- Botón cerrar (X) -->
            <button
                @click="cerrarModales"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition"
                aria-label="Cerrar modal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <h3 class="text-xl font-semibold mb-5 flex items-center gap-2 text-indigo-600">
                <i class="fas fa-plus-circle text-2xl"></i> Nuevo Detalle de Compra
            </h3>
            
            <form method="POST" action="{{ route('detallecompras.store') }}" @submit.prevent="if (validarCrear()) { calcularSubtotalCrear(); $el.submit(); }">
                @csrf

                <div class="mb-4">
                    <label for="CompraID" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-hashtag text-indigo-500"></i> Compra ID
                    </label>
                    <input
                        type="text"
                        id="CompraID"
                        name="CompraID"
                        x-model.trim="detalleCrear.CompraID"
                        @input="validarCampo('CompraID')"
                        placeholder="Ingrese ID de compra"
                        class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                        required
                    />
                    <template x-if="erroresCrear.CompraID">
                        <p class="text-red-600 text-xs mt-1" x-text="erroresCrear.CompraID"></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label for="ProductoID" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-box-open text-indigo-500"></i> Producto
                    </label>
                    <select
                        id="ProductoID"
                        name="ProductoID"
                        x-model="detalleCrear.ProductoID"
                        @change="validarCampo('ProductoID')"
                        class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                        required
                    >
                        <option value="" disabled>Seleccione producto</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->ProductoID }}">{{ $producto->NombreProducto }}</option>
                        @endforeach
                    </select>
                    <template x-if="erroresCrear.ProductoID">
                        <p class="text-red-600 text-xs mt-1" x-text="erroresCrear.ProductoID"></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label for="Cantidad" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-sort-numeric-up text-indigo-500"></i> Cantidad
                    </label>
                    <input
                        type="number"
                        id="Cantidad"
                        name="Cantidad"
                        min="1"
                        x-model.number="detalleCrear.Cantidad"
                        @input="validarCampo('Cantidad'); calcularSubtotalCrear()"
                        class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                        required
                    />
                    <template x-if="erroresCrear.Cantidad">
                        <p class="text-red-600 text-xs mt-1" x-text="erroresCrear.Cantidad"></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label for="PrecioUnitario" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-dollar-sign text-indigo-500"></i> Precio Unitario
                    </label>
                    <input
                        type="number"
                        id="PrecioUnitario"
                        name="PrecioUnitario"
                        step="0.01"
                        min="0"
                        x-model.number="detalleCrear.PrecioUnitario"
                        @input="validarCampo('PrecioUnitario'); calcularSubtotalCrear()"
                        class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                        required
                    />
                    <template x-if="erroresCrear.PrecioUnitario">
                        <p class="text-red-600 text-xs mt-1" x-text="erroresCrear.PrecioUnitario"></p>
                    </template>
                </div>

                <div class="mb-6">
                    <label for="SubtotalFormatted" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-calculator text-indigo-500"></i> Subtotal
                    </label>
                    <input
                        type="text"
                        id="SubtotalFormatted"
                        x-model="detalleCrear.SubtotalFormatted"
                        readonly
                        class="w-full border rounded-md px-3 py-2 bg-gray-100 cursor-not-allowed"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="cerrarModales" class="px-5 py-2 bg-gray-300 rounded-md hover:bg-gray-400 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                        class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition flex items-center gap-2"
                        :disabled="Object.keys(erroresCrear).length > 0"
                    >
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<!-- Modal Editar Detalle -->
<template x-if="modalEditar">
    <div
        class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center p-4"
        @keydown.escape.window="cerrarModales"
    >
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md relative p-6 border border-yellow-300">
            <!-- Botón cerrar (X) -->
            <button
                @click="cerrarModales"
                class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 transition"
                aria-label="Cerrar modal"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <h3 class="text-xl font-semibold mb-5 flex items-center gap-2 text-yellow-600">
                <i class="fas fa-edit text-2xl"></i> Editar Detalle de Compra
            </h3>
            
            <form method="POST" :action="rutaEditar" @submit.prevent="if (validarEditar()) { calcularSubtotalEditar(); $el.submit(); }">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="CompraIDEditar" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-hashtag text-yellow-500"></i> Compra ID
                    </label>
                    <input
                        type="text"
                        id="CompraIDEditar"
                        name="CompraID"
                        x-model="detalleEditar.CompraID"
                        readonly
                        class="w-full border rounded-md px-3 py-2 bg-gray-100 cursor-not-allowed"
                        required
                    />
                </div>

                <div class="mb-4">
                    <label for="ProductoIDEditar" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-box-open text-yellow-500"></i> Producto
                    </label>
                    <select
                        id="ProductoIDEditar"
                        name="ProductoID"
                        x-model="detalleEditar.ProductoID"
                        @change="validarCampoEditar('ProductoID')"
                        class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                        required
                    >
                        <option value="" disabled>Seleccione producto</option>
                        @foreach($productos as $producto)
                            <option :selected="detalleEditar.ProductoID == {{ $producto->ProductoID }}" value="{{ $producto->ProductoID }}">{{ $producto->NombreProducto }}</option>
                        @endforeach
                    </select>
                    <template x-if="erroresEditar.ProductoID">
                        <p class="text-red-600 text-xs mt-1" x-text="erroresEditar.ProductoID"></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label for="CantidadEditar" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-sort-numeric-up text-yellow-500"></i> Cantidad
                    </label>
                    <input
                        type="number"
                        id="CantidadEditar"
                        name="Cantidad"
                        min="1"
                        x-model.number="detalleEditar.Cantidad"
                        @input="validarCampoEditar('Cantidad'); calcularSubtotalEditar()"
                        class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                        required
                    />
                    <template x-if="erroresEditar.Cantidad">
                        <p class="text-red-600 text-xs mt-1" x-text="erroresEditar.Cantidad"></p>
                    </template>
                </div>

                <div class="mb-4">
                    <label for="PrecioUnitarioEditar" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-dollar-sign text-yellow-500"></i> Precio Unitario
                    </label>
                    <input
                        type="number"
                        id="PrecioUnitarioEditar"
                        name="PrecioUnitario"
                        step="0.01"
                        min="0"
                        x-model.number="detalleEditar.PrecioUnitario"
                        @input="validarCampoEditar('PrecioUnitario'); calcularSubtotalEditar()"
                        class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 transition"
                        required
                    />
                    <template x-if="erroresEditar.PrecioUnitario">
                        <p class="text-red-600 text-xs mt-1" x-text="erroresEditar.PrecioUnitario"></p>
                    </template>
                </div>

                <div class="mb-6">
                    <label for="SubtotalFormattedEditar" class="block text-sm font-medium text-gray-700 mb-1 flex items-center gap-1">
                        <i class="fas fa-calculator text-yellow-500"></i> Subtotal
                    </label>
                    <input
                        type="text"
                        id="SubtotalFormattedEditar"
                        x-model="detalleEditar.SubtotalFormatted"
                        readonly
                        class="w-full border rounded-md px-3 py-2 bg-gray-100 cursor-not-allowed"
                    />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="cerrarModales" class="px-5 py-2 bg-gray-300 rounded-md hover:bg-gray-400 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                        class="px-5 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition flex items-center gap-2"
                        :disabled="Object.keys(erroresEditar).length > 0"
                    >
                        <i class="fas fa-save"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>


    <script>
       function detalleCompraHandler() {
    return {
        search: '{{ request('search') }}',
        modalCrear: false,
        modalEditar: false,

        detalleCrear: {
            CompraID: '',
            ProductoID: '',
            Cantidad: 1,
            PrecioUnitario: 0,
            SubtotalFormatted: 'L. 0.00',
        },
        erroresCrear: {},

        detalleEditar: {},
        erroresEditar: {},

        rutaEditar: '',

        abrirModalCrear() {
            this.detalleCrear = {
                CompraID: '',
                ProductoID: '',
                Cantidad: 1,
                PrecioUnitario: 0,
                SubtotalFormatted: 'L. 0.00',
            };
            this.erroresCrear = {};
            this.modalCrear = true;
        },

        abrirModalEditar(detalle) {
            this.detalleEditar = JSON.parse(JSON.stringify(detalle));
            this.detalleEditar.SubtotalFormatted = `L. ${parseFloat(this.detalleEditar.Subtotal).toFixed(2)}`;
            this.rutaEditar = `/detallecompras/${detalle.DetalleCompraID}`;
            this.erroresEditar = {};
            this.modalEditar = true;
        },

        cerrarModales() {
            this.modalCrear = false;
            this.modalEditar = false;
        },

        calcularSubtotalCrear() {
            let subtotal = (this.detalleCrear.Cantidad || 0) * (this.detalleCrear.PrecioUnitario || 0);
            this.detalleCrear.SubtotalFormatted = `L. ${subtotal.toFixed(2)}`;
        },

        calcularSubtotalEditar() {
            let subtotal = (this.detalleEditar.Cantidad || 0) * (this.detalleEditar.PrecioUnitario || 0);
            this.detalleEditar.SubtotalFormatted = `L. ${subtotal.toFixed(2)}`;
        },

        validarCampo(campo) {
            switch(campo) {
                case 'CompraID':
                    if(!this.detalleCrear.CompraID || this.detalleCrear.CompraID.trim() === '') {
                        this.erroresCrear.CompraID = 'Compra ID es obligatorio.';
                    } else {
                        delete this.erroresCrear.CompraID;
                    }
                    break;
                case 'ProductoID':
                    if(!this.detalleCrear.ProductoID) {
                        this.erroresCrear.ProductoID = 'Debe seleccionar un producto.';
                    } else {
                        delete this.erroresCrear.ProductoID;
                    }
                    break;
                case 'Cantidad':
                    if(!this.detalleCrear.Cantidad || this.detalleCrear.Cantidad < 1) {
                        this.erroresCrear.Cantidad = 'Cantidad debe ser mayor a 0.';
                    } else {
                        delete this.erroresCrear.Cantidad;
                    }
                    break;
                case 'PrecioUnitario':
                    if(this.detalleCrear.PrecioUnitario === '' || this.detalleCrear.PrecioUnitario < 0) {
                        this.erroresCrear.PrecioUnitario = 'Precio Unitario debe ser igual o mayor a 0.';
                    } else {
                        delete this.erroresCrear.PrecioUnitario;
                    }
                    break;
            }
        },

        validarCampoEditar(campo) {
            switch(campo) {
                case 'ProductoID':
                    if(!this.detalleEditar.ProductoID) {
                        this.erroresEditar.ProductoID = 'Debe seleccionar un producto.';
                    } else {
                        delete this.erroresEditar.ProductoID;
                    }
                    break;
                case 'Cantidad':
                    if(!this.detalleEditar.Cantidad || this.detalleEditar.Cantidad < 1) {
                        this.erroresEditar.Cantidad = 'Cantidad debe ser mayor a 0.';
                    } else {
                        delete this.erroresEditar.Cantidad;
                    }
                    break;
                case 'PrecioUnitario':
                    if(this.detalleEditar.PrecioUnitario === '' || this.detalleEditar.PrecioUnitario < 0) {
                        this.erroresEditar.PrecioUnitario = 'Precio Unitario debe ser igual o mayor a 0.';
                    } else {
                        delete this.erroresEditar.PrecioUnitario;
                    }
                    break;
            }
        },

        validarCrear() {
            this.validarCampo('CompraID');
            this.validarCampo('ProductoID');
            this.validarCampo('Cantidad');
            this.validarCampo('PrecioUnitario');
            return Object.keys(this.erroresCrear).length === 0;
        },

        validarEditar() {
            this.validarCampoEditar('ProductoID');
            this.validarCampoEditar('Cantidad');
            this.validarCampoEditar('PrecioUnitario');
            return Object.keys(this.erroresEditar).length === 0;
        },
    }
}
        
    </script>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-file-invoice"></i> Control de CAI
        </h2>
    </x-slot>

    <div x-data="caiComponent()" x-init="initToast()">
        {{-- Botón para abrir modal --}}
        <button @click="openModal"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md shadow">
            <i class="fas fa-plus"></i> Nuevo CAI
        </button>

        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Código CAI</th>
                        <th class="px-4 py-3 text-left">Rango</th>
                        <th class="px-4 py-3 text-left">Autorizado</th>
                        <th class="px-4 py-3 text-left">Fecha Límite</th>
                        <th class="px-4 py-3 text-left">Emitidas</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($cais as $cai)
                        @php
                            $fechaLimite = \Carbon\Carbon::parse($cai->fecha_limite_emision);
                            $diasRestantes = now()->diffInDays($fechaLimite, false);
                            $inicio = (int) substr($cai->rango_inicial, -8);
                            $fin = (int) substr($cai->rango_final, -8);
                            $restantes = $fin - ($inicio + $cai->facturas_emitidas);
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $cai->codigo }}</td>
                            <td class="px-4 py-2">{{ $cai->rango_inicial }} - {{ $cai->rango_final }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($cai->fecha_autorizacion)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $fechaLimite->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $cai->facturas_emitidas }}</td>
                            <td class="px-4 py-2">
                                @if($diasRestantes <= 7 || $restantes <= 5)
                                    <span class="text-red-600 font-bold">¡Atención!</span>
                                @else
                                    <span class="text-green-600 font-semibold">Correcto</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modal para crear CAI --}}
        <div x-show="show" style="display: none"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
             @keydown.window.escape="closeModal">
            <div class="bg-white w-full max-w-lg rounded-lg p-6 relative">
                <button @click="closeModal"
                        class="absolute top-2 right-3 text-gray-600 hover:text-red-500 text-xl font-bold">
                    &times;
                </button>

                <h2 class="text-xl font-bold mb-4">➕ Registrar nuevo CAI</h2>

                <form action="{{ route('cai.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Código CAI</label>
                        <input type="text" name="codigo" maxlength="37" required
       x-model="codigo"
       class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200" />
<template x-if="codigo && !codigoValido">
    <p class="text-red-600 text-xs mt-1">El código debe tener exactamente 37 caracteres.</p>
</template>

                    </div>

                    <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Rango Inicial</label>
    <input type="text" name="rango_inicial" required x-model="rangoInicial"
           class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200"
           placeholder="000-001-01-00000001" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Rango Final</label>
    <input type="text" name="rango_final" required x-model="rangoFinal"
           class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200"
           placeholder="000-001-01-00000099" />

    <template x-if="rangoInicial && rangoFinal && !rangoLogico">
        <p class="text-red-600 text-xs mt-1">El rango final debe ser mayor que el inicial.</p>
    </template>
</div>


                    <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Fecha de Autorización</label>
    <input type="date" name="fecha_autorizacion" required
           x-model="fechaAutorizacion"
           class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200" />
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">Fecha Límite de Emisión</label>
    <input type="date" name="fecha_limite_emision" required
           x-model="fechaLimite"
           class="w-full border rounded px-3 py-2 shadow-sm focus:ring focus:ring-blue-200" />
    
    <template x-if="fechaLimite && fechaAutorizacion && !fechasValidas">
        <p class="text-red-600 text-xs mt-1">
            La fecha límite debe ser posterior o igual a la de autorización.
        </p>
    </template>
</div>

                    <div class="flex justify-end mt-4 gap-3">
                        <button type="button" @click="closeModal"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Cancelar
                        </button>
                        <button type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="!todoValido">
    Guardar CAI
</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Alpine.js --}}
    <script>
function caiComponent() {
    return {
        // Estado modal
        show: false,
        openModal() { this.reset(); this.show = true },
        closeModal() { this.show = false },

        // Campos del formulario
        codigo: '',
        rangoInicial: '',
        rangoFinal: '',
        fechaAutorizacion: '',
        fechaLimite: '',

        // Reset
        reset() {
            this.codigo = '';
            this.rangoInicial = '';
            this.rangoFinal = '';
            this.fechaAutorizacion = '';
            this.fechaLimite = '';
        },

        // Toast
        initToast() {
            @if(session('success'))
                Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
            @endif
            @if(session('error'))
                Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
            @endif
        },

        // Validaciones reactivas
        get codigoValido() {
            return /^[A-Za-z0-9\-]{37}$/.test(this.codigo);
        },
        get rangoValido() {
            return /^\d{3}-\d{3}-\d{2}-\d{8}$/.test(this.rangoInicial) &&
                   /^\d{3}-\d{3}-\d{2}-\d{8}$/.test(this.rangoFinal);
        },
        get rangoLogico() {
            if (!this.rangoValido) return false;
            const ini = parseInt(this.rangoInicial.slice(-8));
            const fin = parseInt(this.rangoFinal.slice(-8));
            return ini < fin;
        },
        get fechasValidas() {
            if (!this.fechaAutorizacion || !this.fechaLimite) return false;
            return this.fechaLimite >= this.fechaAutorizacion;
        },
        get todoValido() {
            return this.codigoValido && this.rangoValido && this.rangoLogico && this.fechasValidas;
        }
    };
}
</script>


</x-app-layout>
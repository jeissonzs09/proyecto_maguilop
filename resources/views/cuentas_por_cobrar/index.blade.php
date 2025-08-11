<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-credit-card"></i> Cuentas por Cobrar
        </h2>
    </x-slot>

    

    <div x-data="cuentasComponent()" x-init="initToast()">
        {{-- Tabla --}}
        <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
            <table class="min-w-full text-sm text-gray-800">
                <thead class="bg-orange-500 text-white text-sm uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Factura</th>
                        <th class="px-4 py-3 text-left">Cliente</th>
                        <th class="px-4 py-3 text-left">Fecha Emisión</th>
                        <th class="px-4 py-3 text-left">Fecha Vencimiento</th>
                        <th class="px-4 py-3 text-left">Monto Total</th>
                        <th class="px-4 py-3 text-left">Pagado</th>
                        <th class="px-4 py-3 text-left">Saldo</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($cuentas as $cuenta)
                        @php
                            $saldo = $cuenta->monto_total - $cuenta->monto_pagado;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $cuenta->factura->NumeroFactura }}</td>
                            <td class="px-4 py-2">{{ $cuenta->factura->cliente->NombreCliente ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($cuenta->factura->Fecha)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($cuenta->fecha_vencimiento)->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">L. {{ number_format($cuenta->monto_total, 2) }}</td>
                            <td class="px-4 py-2">L. {{ number_format($cuenta->monto_pagado, 2) }}</td>
                            <td class="px-4 py-2 text-red-600 font-semibold">L. {{ number_format($saldo, 2) }}</td>
                            <td class="px-4 py-2">
                                @if($cuenta->estado == 'Pagada')
                                    <span class="text-green-600 font-bold">Pagada</span>
                                @elseif($cuenta->estado == 'Vencida')
                                    <span class="text-red-600 font-bold">Vencida</span>
                                @else
                                    <span class="text-yellow-600 font-semibold">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center space-x-2">
    <button
        @click="abrirModalPago({{ $cuenta->id }}, '{{ $cuenta->factura->cliente->NombreCliente ?? '' }}', {{ $saldo }})"
        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
        Registrar Pago
    </button>

    <a href="{{ route('pagos.historial', $cuenta->id) }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
        Ver Pagos
    </a>
</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Modal de Pago --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-show="mostrarModalPago">
            <div class="bg-white rounded-lg shadow p-6 w-full max-w-md">
                <h2 class="text-lg font-bold mb-4">Registrar Pago</h2>

                <form method="POST" action="{{ route('pagos.store') }}">
                    @csrf
                    <input type="hidden" name="cuenta_id" x-model="cuentaID">

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Cliente:</label>
                        <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100" x-model="clienteNombre" disabled>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Monto a Pagar (máx. L. <span x-text="saldoRestante.toFixed(2)"></span>):</label>
                        <input type="number" name="monto" min="1" :max="saldoRestante" step="0.01" required
                               class="w-full border rounded px-3 py-2" placeholder="Ingrese monto">
                    </div>

                    <div class="mb-4">
    <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción (opcional)</label>
    <textarea id="descripcion" name="descripcion" rows="2" class="mt-1 block w-full rounded border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"></textarea>
</div>


                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="cerrarModalPago()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancelar</button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function cuentasComponent() {
            return {
                mostrarModalPago: false,
                cuentaID: null,
                clienteNombre: '',
                saldoRestante: 0,

                abrirModalPago(id, cliente, saldo) {
                    this.cuentaID = id;
                    this.clienteNombre = cliente;
                    this.saldoRestante = saldo;
                    this.mostrarModalPago = true;
                },

                cerrarModalPago() {
                    this.mostrarModalPago = false;
                    this.cuentaID = null;
                    this.clienteNombre = '';
                    this.saldoRestante = 0;
                },

                initToast() {
                    @if(session('success'))
                        Toast.fire({ icon: 'success', title: "{{ session('success') }}" });
                    @endif
                    @if(session('error'))
                        Toast.fire({ icon: 'error', title: "{{ session('error') }}" });
                    @endif
                }
            }
        }
    </script>
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
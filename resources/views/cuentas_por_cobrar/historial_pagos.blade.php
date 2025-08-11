<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold flex items-center gap-2">
            <i class="fas fa-money-check-alt"></i> Historial de Pagos
        </h2>
    </x-slot>

    <p class="text-lg font-semibold text-gray-700">
        <i class="fas fa-user"></i> 
    Cliente:
    @if($cuenta->factura && $cuenta->factura->cliente)
        {{ $cuenta->factura->cliente->NombreCliente ?? '' }} {{ $cuenta->factura->cliente->ApellidoCliente ?? '' }}
    @else
        No disponible
    @endif
</p>


    <div class="overflow-x-auto bg-white rounded-lg shadow mt-4">
        <table class="min-w-full text-sm text-gray-800">
            <thead class="bg-orange-500 text-white text-sm uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha de Pago</th>
                    <th class="px-4 py-3 text-left">Monto Pagado</th>
                    <th class="px-4 py-3 text-left">Descripción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($pagos as $pago)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">L. {{ number_format($pago->monto, 2) }}</td>
                        <td class="px-4 py-2">{{ $pago->descripcion ?? 'Sin descripción' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">No se han registrado pagos aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('cuentas-por-cobrar.index') }}"
           class="inline-block px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
            ← Volver a Cuentas por Cobrar
        </a>
    </div>
</x-app-layout>
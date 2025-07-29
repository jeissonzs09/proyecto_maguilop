<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard General</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Tarjetas de resumen --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    {{-- Clientes --}}
<a href="{{ route('clientes.index') }}" class="block">
    <div class="bg-white p-4 rounded shadow text-center hover:bg-blue-50 transition">
        <i class="bi bi-people-fill text-3xl text-blue-600"></i>
        <strong class="block mt-1">Clientes</strong>
        <div class="text-xl font-bold text-blue-600">{{ $totalClientes }}</div>
    </div>
</a>

    {{-- Empleados --}}
<a href="{{ route('empleados.index') }}" class="block">
    <div class="bg-white p-4 rounded shadow text-center hover:bg-green-50 transition">
        <i class="bi bi-person-badge text-3xl text-green-600"></i>
        <strong class="block mt-1">Empleados</strong>
        <div class="text-xl font-bold text-green-600">{{ $totalEmpleados }}</div>
    </div>
</a>


    {{-- Pedidos --}}
 <a href="{{ route('pedidos.index') }}" class="block">
    <div class="bg-white p-4 rounded shadow text-center hover:bg-yellow-50 transition">
        <i class="bi bi-box-seam text-3xl text-yellow-600"></i>
        <strong class="block mt-1">Pedidos</strong>
        <div class="text-xl font-bold text-yellow-600">{{ $totalPedidos }}</div>
    </div>
</a>


    {{-- Total Ventas --}}
    <a href="{{ route('ventas.index') }}" class="block">
    <div class="bg-white p-4 rounded shadow text-center">
        <i class="bi bi-cash-coin text-3xl text-red-600"></i>
        <strong class="block mt-1">Total Ventas</strong>
        <div class="text-xl font-bold text-red-600">L. {{ number_format($totalVentas, 2) }}</div>
    </div>
</div>


            {{-- Gráficas --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white p-6 rounded shadow">
        <h4 class="font-semibold mb-4 flex items-center gap-2">
            <i class="bi bi-wrench-adjustable text-lg text-gray-600"></i>
            Reparaciones por Estado
        </h4>
        <canvas id="reparacionesChart"></canvas>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h4 class="font-semibold mb-4 flex items-center gap-2">
            <i class="bi bi-bar-chart-line text-lg text-gray-600"></i>
            Ventas por Mes
        </h4>
        <canvas id="ventasMesChart"></canvas>
    </div>
</div>

<div class="bg-white p-6 rounded shadow">
    <h4 class="font-semibold mb-4 flex items-center gap-2">
        <i class="bi bi-cart-check text-green-600 text-xl"></i> Compras por Mes
    </h4>
    <canvas id="comprasMesChart"></canvas>
</div>




    @push('scripts')

    
<script>
const repCtx = document.getElementById('reparacionesChart').getContext('2d');

const labels = {!! json_encode(array_keys($reparacionesPorEstado)) !!};
const data = {!! json_encode(array_values($reparacionesPorEstado)) !!};

const colorMap = {
    "Finalizado": "#22c55e",   // verde
    "Pendiente": "#facc15",    // amarillo
    "En proceso": "#3b82f6"    // azul
};

const filteredLabels = labels.filter(label => Object.keys(colorMap).includes(label));
const filteredData = filteredLabels.map(label => data[labels.indexOf(label)]);
const filteredColors = filteredLabels.map(label => colorMap[label]);

new Chart(repCtx, {
    type: 'pie',
    data: {
        labels: filteredLabels,
        datasets: [{
            data: filteredData,
            backgroundColor: filteredColors,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top'
            },
            datalabels: {
                formatter: (value, ctx) => {
                    let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                    let percentage = (value / sum * 100).toFixed(1) + '%';
                    return percentage;
                },
                color: '#fff',
                font: {
                    weight: 'bold',
                    size: 14
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});


        const ventasData = {!! json_encode(array_values($ventasPorMes)) !!};
        const ventasLabels = {!! json_encode(array_keys($ventasPorMes)) !!};
        const ventasCtx = document.getElementById('ventasMesChart')?.getContext('2d');
        if (ventasCtx && ventasData.length > 0) {
            new Chart(ventasCtx, {
                type: 'bar',
                data: {
                    labels: ventasLabels,
                    datasets: [{
                        label: 'Ventas por mes',
                        data: ventasData,
                        backgroundColor: '#3b82f6'
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    </script>

    <script>
    const comprasCtx = document.getElementById('comprasMesChart').getContext('2d');

    new Chart(comprasCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($comprasPorMesFormateado)) !!},
            datasets: [{
                label: 'Compras por mes',
                data: {!! json_encode(array_values($comprasPorMesFormateado)) !!},
                backgroundColor: '#10b981' // verde
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

    @endpush
    
</x-app-layout>



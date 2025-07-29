<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Pedidos | MAGUILOP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 900px;
            background: #ffffff;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo {
            width: 160px; /* Tamaño del logo */
            margin-bottom: 10px;
        }
        .company-title {
            font-size: 34px;
            color: #f97316;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-info {
            text-align: center; /* Centrar la información de la empresa */
        }
        .company-info p {
            font-size: 15px;
            margin: 3px 0;
            color: #333;
        }
        .divider {
            border-top: 3px solid #f97316;
            margin: 25px 0;
        }
        .final-title {
            text-align: center;
            font-size: 24px;
            color: #f97316;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 14px;
            text-align: center;
        }
        th {
            background-color: #f97316;
            color: #fff;
            font-size: 15px;
        }
        tr:nth-child(even) {
            background-color: #fdf1e8;
        }
        .metadata {
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
            color: #333;
        }
        .footer {
            margin-top: 25px;
            font-size: 12px;
            text-align: center;
            color: #888;
        }
        /* Forzar salto de página antes del listado de pedidos */
        .page-break {
            page-break-before: always; /* Salto de página */
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <img src="{{ public_path('images/logo-maguilop.png') }}" class="logo" alt="Logo MAGUILOP">
        <h1 class="company-title">MAGUILOP</h1>
    </div>

    <div class="company-info">
        <p><strong>Dirección:</strong> Ave. República de Chile, Col. Concepción</p>
        <p><strong>Teléfono:</strong> (504) 2233-7722, 2272-5665</p>
        <p><strong>Correo:</strong> maguilop2912792@yahoo.com</p>
        <p><strong>Ubicación:</strong> Tegucigalpa, MDC, HONDURAS C.A.</p>
        <p><strong>R.T.N.:</strong> 08001150018083</p>
        <p><strong>Propietario:</strong> Manuel de Jesús Aguirre López</p>
    </div>

    <div class="metadata">
        <p><strong>Fecha de Generación:</strong> {{ date('d/m/Y H:i') }}</p>
        <p><strong>Total de Registros:</strong> {{ count($pedidos) }} registros</p>
    </div>

    <div class="divider"></div>

    <div class="page-break"></div> <!-- Salto de página -->
     <h2 class="final-title">Listado de Pedidos</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Empleado</th>
                <th>Producto</th>
                <th>Fecha Pedido</th>
                <th>Fecha Entrega</th>
                <th>Estado</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedidos as $p)
                @foreach($p->detalles as $detalle)
                    <tr>
                        <td>{{ $p->PedidoID }}</td>
                        <td>{{ $p->cliente->NombreCliente ?? '—' }}</td>
                        <td>
                            @if($p->empleado && $p->empleado->persona)
                                {{ $p->empleado->persona->Nombre }} {{ $p->empleado->persona->Apellido }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $detalle->producto->NombreProducto ?? '—' }}</td>
                        <td>{{ $p->FechaPedido }}</td>
                        <td>{{ $p->FechaEntrega }}</td>
                        <td>{{ $p->Estado }}</td>
                        <td>{{ $detalle->Cantidad }}</td>
                        <td>L {{ number_format($detalle->PrecioUnitario, 2) }}</td>
                        <td>L {{ number_format($detalle->Subtotal, 2) }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© 2025 MAGUILOP. Todos los derechos reservados.</p>
    </div>
</div>

</body>
</html>

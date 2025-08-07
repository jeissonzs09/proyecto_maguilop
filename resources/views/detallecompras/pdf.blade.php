<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado Detalle de Compras | MAGUILOP</title>
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
            width: 140px;
            margin-bottom: 10px;
        }
        .company-title {
            font-size: 34px;
            color: #f97316;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-info p {
            font-size: 15px;
            margin: 3px 0;
            color: #333;
            text-align: center;
        }
        .metadata {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .final-title {
            text-align: center;
            font-size: 24px;
            color: #f97316;
            font-weight: bold;
            margin: 15px 0;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #444;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f97316;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #fdf1e8;
        }
        .footer {
            margin-top: 25px;
            font-size: 12px;
            text-align: center;
            color: #888;
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
        <p><strong>Total de Detalles de Compras:</strong> {{ count($detalles_compra) }} registros</p>
    </div>

    <h2 class="final-title">Listado Detalle de Compras</h2>

    <table>
        <thead>
            <tr>
                <th>ID Detalle</th>
                <th>Compra</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles_compra as $detalle)
                <tr>
                    <td>{{ $detalle->DetalleCompraID }}</td>
                    <td>
                        @if($detalle->compra)
                            Compra #{{ $detalle->compra->CompraID }}<br>
                            {{ \Carbon\Carbon::parse($detalle->compra->FechaCompra)->format('d/m/Y') }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $detalle->producto->NombreProducto ?? 'Sin nombre' }}</td>
                    <td>{{ $detalle->Cantidad }}</td>
                    <td>L. {{ number_format($detalle->PrecioUnitario, 2) }}</td>
                    <td>L. {{ number_format($detalle->Subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© 2025 MAGUILOP. Todos los derechos reservados.</p>
    </div>
</div>

</body>
</html>
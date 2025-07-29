<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Compras | MAGUILOP</title>
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
            width: 160px;
            margin-bottom: 10px;
        }
        .company-title {
            font-size: 34px;
            color: #f97316;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 15px;
        }
        .company-info p {
            font-size: 15px;
            margin: 3px 0;
            color: #333;
        }
        .metadata {
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
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
        thead {
            display: table-row-group;
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
        <p><strong>Generado:</strong> {{ date('d/m/Y H:i') }}</p>
        <p><strong>Total:</strong> {{ count($compras) }} registros</p>
    </div>

    <h2 style="text-align: center; margin-top: 10px;">Listado de Compras</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Proveedor</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($compras as $c)
                <tr>
                    <td>{{ $c->CompraID }}</td>
                    <td>{{ optional($c->proveedor)->Descripcion ?? '—' }}</td>
                    <td>{{ optional(optional($c->empleado)->persona)->NombreCompleto ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($c->FechaCompra)->format('d/m/Y') }}</td>
                    <td>L. {{ number_format($c->TotalCompra, 2) }}</td>
                    <td>{{ $c->Estado }}</td>
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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Proveedores | MAGUILOP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 10px;
        }
        .container {
            max-width: 100%;
            margin: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
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
            font-size: 14px;
            color: #333;
            margin-bottom: 15px;
        }
        .metadata {
            text-align: center;
            font-size: 14px;
            color: #333;
            margin-bottom: 15px;
        }
        .divider {
            border-top: 3px solid #f97316;
            margin: 20px 0;
        }
        .page-break {
            page-break-before: always;
        }
        .final-title {
            text-align: center;
            font-size: 24px;
            color: #f97316;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        table {
            width: 100% !important;
            border-collapse: collapse;
            table-layout: fixed !important;
            page-break-inside: auto;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px !important;
            font-size: 9px !important;
            text-align: center;
            white-space: normal !important;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        th {
            background-color: #f97316;
            color: #fff;
            font-size: 10px !important;
        }
        tr:nth-child(even) {
            background-color: #fdf1e8;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        .footer {
            margin-top: 20px;
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
        <p><strong>Total de Registros:</strong> {{ count($proveedores) }} registros</p>
    </div>

    <div class="page-break"></div>

    <div class="final-title">Listado de Proveedores</div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">ID</th>
                <th style="width:15%">Persona</th>
                <th style="width:15%">Empresa</th>
                <th style="width:10%">RTN</th>
                <th style="width:20%">Descripción</th>
                <th style="width:10%">Sitio Web</th>
                <th style="width:10%">Ubicación</th>
                <th style="width:10%">Teléfono</th>
                <th style="width:15%">Correo</th>
                <th style="width:10%">Tipo</th>
                <th style="width:10%">Registro</th>
                <th style="width:10%">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proveedores as $p)
                <tr>
                    <td>{{ $p->ProveedorID }}</td>
                    <td>{{ $p->persona->NombreCompleto ?? '—' }}</td>
                    <td>{{ $p->empresa->NombreEmpresa ?? '—' }}</td>
                    <td>{{ $p->RTN }}</td>
                    <td>{{ $p->Descripcion }}</td>
                    <td>{{ $p->URL_Website }}</td>
                    <td>{{ $p->Ubicacion }}</td>
                    <td>{{ $p->Telefono }}</td>
                    <td>{{ $p->CorreoElectronico }}</td>
                    <td>{{ $p->TipoProveedor }}</td>
                    <td>{{ $p->FechaRegistro }}</td>
                    <td>{{ $p->Estado }}</td>
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
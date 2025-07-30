<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Personas | MAGUILOP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fff;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 100%;
            margin: auto;
            padding: 10px;
        }
        .header {
            margin-bottom: 10px;
        }
        .logo {
            width: 140px;
            margin-bottom: 5px;
        }
        .company-title {
            font-size: 28px;
            color: #f97316;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .company-info p,
        .metadata p,
        .footer p {
            font-size: 13px;
            margin: 2px 0;
        }
        .metadata {
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .divider {
            border-top: 2px solid #f97316;
            margin: 10px 0;
        }
        .table-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        table {
            margin: 0 auto;
            width: 95%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px;
        }
        th {
            background-color: #f97316;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #fdf1e8;
        }
        .footer {
            margin-top: 20px;
            font-size: 11px;
            color: #777;
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
        <p><strong>RTN:</strong> 08001150018083</p>
        <p><strong>Propietario:</strong> Manuel de Jesús Aguirre López</p>
    </div>

    <div class="metadata">
        <p><strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Total de personas:</strong> {{ count($personas) }}</p>
    </div>

    <div class="divider"></div>

    <h2 class="table-title">Listado de Personas</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Fecha Nac.</th>
                <th>Género</th>
                <th>Correo</th>
                <th>Teléfonos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personas as $persona)
                <tr>
                    <td>{{ $persona->PersonaID }}</td>
                    <td>{{ $persona->Nombre }}</td>
                    <td>{{ $persona->Apellido }}</td>
                    <td>{{ \Carbon\Carbon::parse($persona->FechaNacimiento)->format('d/m/Y') }}</td>
                    <td>{{ $persona->Genero }}</td>
                    <td>{{ $persona->CorreoElectronico }}</td>
                    <td>
                        @if ($persona->telefonos->isNotEmpty())
                            @foreach ($persona->telefonos as $telefono)
                                {{ $telefono->Tipo }}: {{ $telefono->Numero }}<br>
                            @endforeach
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© {{ date('Y') }} MAGUILOP. Todos los derechos reservados.</p>
    </div>
</div>

</body>
</html>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Bitácora | MAGUILOP</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 30px;
        }
        .container {
            max-width: 1000px;
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
            font-size: 11px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 13px;
            text-align: center;
        }
        th {
            background-color: #f97316;
            color: #fff;
            font-size: 14px;
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
        td.datos-largos {
    max-width: 120px;      /* Ajusta el ancho según convenga */
    word-wrap: break-word;  /* Permite que el texto largo se divida en varias líneas */
    font-size: 12px;        /* Más pequeño que el resto del texto */
}

    </style>
</head>
<body>

<div class="container">
    <div class="header">
        {{-- Usamos asset() para que DomPDF encuentre la imagen correctamente --}}
        <img src="{{ public_path('images/logo-maguilop.png') }}" style="width: 120px;">
        <h1 class="company-title">MAGUILOP</h1>
    </div>

    <div class="company-info" style="text-align: center;">
        <p><strong>Dirección:</strong> Ave. República de Chile, Col. Concepción</p>
        <p><strong>Teléfono:</strong> (504) 2233-7722, 2272-5665</p>
        <p><strong>Correo:</strong> maguilop2912792@yahoo.com</p>
        <p><strong>Ubicación:</strong> Tegucigalpa, MDC, HONDURAS C.A.</p>
        <p><strong>R.T.N.:</strong> 08001150018083</p>
        <p><strong>Propietario:</strong> Manuel de Jesús Aguirre López</p>
    </div>

    <div class="metadata">
        <p><strong>Fecha de Generación:</strong> {{ date('d/m/Y H:i') }}</p>
        <p><strong>Total de Registros:</strong> {{ count($bitacoras) }} registros</p>
    </div>

    <div class="divider"></div>

    <h2 class="final-title">Bitácora de Acciones</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario ID</th>
                <th>Acción</th>
                <th>Tabla Afectada</th>
                <th>Fecha Acción</th>
                <th>Descripción</th>
                <th>Datos Previos</th>
                <th>Datos Nuevos</th>
                <th>Módulo</th>
                <th>Resultado</th>
               
            </tr>
        </thead>
        <tbody>
            @foreach($bitacoras as $b)
                <tr>
                    <td>{{ $b->BitacoraID }}</td>
                    <td>{{ $b->UsuarioID }}</td>
                    <td>{{ $b->Accion }}</td>
                    <td>{{ $b->TablaAfectada }}</td>
                    <td>{{ $b->FechaAccion ? date('d/m/Y H:i', strtotime($b->FechaAccion)) : '—' }}</td>
                    <td>{{ $b->Descripcion }}</td>
                    <td class="datos-largos">{{ $b->DatosPrevios }}</td>
                    <td class="datos-largos">{{ $b->DatosNuevos }}</td>
                    <td>{{ $b->Modulo ?? '—' }}</td>
                    <td>{{ $b->Resultado ?? '—' }}</td>
                    
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>© 2025 UNAH. Todos los derechos reservados.</p>
    </div>
</div>

</body>
</html>
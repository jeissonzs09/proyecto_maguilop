<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 4px; }
        .no-border td { border: none; }
        .center { text-align: center; }
        .blue { background-color: #cce6ff; }
        .bold { font-weight: bold; }
        .right { text-align: right; }
        .small { font-size: 10px; }
    </style>
</head>
<body>

{{-- ENCABEZADO --}}
<table class="no-border">
    <tr>
        <td style="width: 70%;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="{{ $logoSrc }}" style="height: 60px;" alt="Logo Maguilop">
                <div>
                    <h2 style="color: #0066cc; margin: 0;">Maguilop - Servicios Técnicos</h2>
                    <p style="margin: 2px 0;">Propietario: <strong>Manuel de Jesús Aguirre López</strong></p>
                    <p style="margin: 2px 0;">R.T.N.: 08011953018083</p>
                    <p style="margin: 2px 0;">CAI: <strong>1EBAC5-916145-BFBFE0-63BE03-09095C-97</strong></p>
                    <p style="margin: 2px 0;">Ave. República de Chile contiguo a la posta Policial La Guadalupe #223</p>
                    <p style="margin: 2px 0;">PBX: 2239-7292, 2235-6655 | Tigo: 2754-7638</p>
                    <p style="margin: 2px 0;">Email: maguilop239729@yahoo.com</p>
                </div>
            </div>
        </td>
        <td class="center" style="vertical-align: top;">
            <p class="bold">FACTURA No.</p>
            <p style="font-size: 16px;">000-001-01-000{{ str_pad($factura->FacturaID, 5, '0', STR_PAD_LEFT) }}</p>
            <p class="small">Fecha: {{ \Carbon\Carbon::parse($factura->Fecha)->format('d/m/Y') }}</p>
            <p class="small">Fecha límite de emisión: {{ \Carbon\Carbon::parse($factura->Fecha)->addDays(7)->format('d/m/Y') }}</p>
            <p><input type="checkbox"> CONTADO &nbsp;&nbsp; <input type="checkbox"> CRÉDITO</p>
        </td>
    </tr>
</table>

{{-- CLIENTE --}}
<table class="no-border" style="margin-top: 10px;">
    <tr>
        <td><strong>Cliente:</strong> {{ $factura->cliente->NombreCliente }}</td>
        <td><strong>RTN:</strong> {{ $factura->cliente->RTN ?? '------' }}</td>
    </tr>
    <tr>
        <td><strong>Dirección:</strong> {{ $factura->cliente->Direccion ?? '------' }}</td>
        <td><strong>Empleado:</strong> {{ $factura->empleado->persona->Nombre }} {{ $factura->empleado->persona->Apellido }}</td>
    </tr>
</table>

{{-- DETALLES DE FACTURA --}}
<table style="margin-top: 15px;">
    <thead class="blue">
        <tr class="center">
            <th style="width: 15%;">CÓDIGO</th>
            <th style="width: 10%;">CANTIDAD</th>
            <th>DESCRIPCIÓN</th>
            <th style="width: 15%;">P. UNITARIO</th>
            <th style="width: 15%;">DESCUENTO</th>
            <th style="width: 15%;">SUB TOTAL</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($factura->detalles as $detalle)
            <tr>
                <td class="center">{{ $detalle->ProductoID }}</td>
                <td class="center">{{ $detalle->Cantidad }}</td>
                <td>{{ $detalle->producto->NombreProducto }}</td>
                <td class="right">L {{ number_format($detalle->PrecioUnitario, 2) }}</td>
                <td class="right">L 0.00</td>
                <td class="right">L {{ number_format($detalle->Subtotal, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- TOTALES --}}
<table class="no-border" style="margin-top: 20px;">
    <tr>
        <td style="width: 65%;"></td>
        <td>
            <table>
                <tr><td>Importe Exonerado</td><td class="right">L 0.00</td></tr>
                <tr><td>Importe Exento</td><td class="right">L 0.00</td></tr>
                <tr><td>Sub Total</td><td class="right">L {{ number_format($factura->Total, 2) }}</td></tr>
                <tr><td>15% IMP. S/V</td><td class="right">L {{ number_format($factura->Total * 0.15, 2) }}</td></tr>
                <tr><td class="bold">TOTAL A PAGAR</td><td class="right bold">L {{ number_format($factura->Total * 1.15, 2) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

{{-- NOTA --}}
<p class="small" style="margin-top: 30px;">Original: Cliente &nbsp;&nbsp;|&nbsp;&nbsp; Duplicado: Control</p>

</body>
</html>




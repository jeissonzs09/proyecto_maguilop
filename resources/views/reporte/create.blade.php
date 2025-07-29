@extends('layouts.app')

@section('title', 'Crear Reporte')

@section('content')
<div class="container">
    <h1>Crear Configuración de Reporte</h1>

    <form action="{{ route('reporte.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo de reporte</label>
            <select name="tipo" id="tipo" class="form-select">
                <option value="bitacoras">Bitácora</option>
                <option value="ventas">Ventas</option>
                <option value="compras">Compras</option>
                <option value="movimientos">Movimientos</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="filtro_fecha" class="form-label">Filtrar por fecha</label>
            <input type="date" name="filtro_fecha" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Generar</button>
    </form>
</div>
@endsection
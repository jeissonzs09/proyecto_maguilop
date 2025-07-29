@extends('layouts.app')

@section('title', 'Editar Reporte')

@section('content')
<div class="container">
    <h1>Editar Configuración de Reporte</h1>

    <form action="{{ route('reporte.update', $reporte->id ?? 1) }}" method="POST">
        @csrf
        @method('PUT')

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
            <input type="date" name="filtro_fecha" value="{{ old('filtro_fecha', $reporte->filtro_fecha ?? '') }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Actualizar</button>
    </form>
</div>
@endsection
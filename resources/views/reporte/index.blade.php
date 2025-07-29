@extends('layouts.app')

@section('title', 'Reportes')

@section('content')
<div class="container">
    <h1 class="mb-4">Panel de Reportes</h1>

    <div class="list-group">
        <a href="{{ route('reporte.show', 'bitacoras') }}" class="list-group-item list-group-item-action">Reporte de Bitácora</a>
        <a href="{{ route('reporte.show', 'ventas') }}" class="list-group-item list-group-item-action">Reporte de Ventas</a>
        <a href="{{ route('reporte.show', 'compras') }}" class="list-group-item list-group-item-action">Reporte de Compras</a>
        <a href="{{ route('reporte.show', 'movimientos') }}" class="list-group-item list-group-item-action">Reporte de Movimientos</a>
    </div>
</div>
@endsection
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCuentasPorCobrarTable extends Migration
{
    public function up()
    {
        Schema::create('cuentas_por_cobrar', function (Blueprint $table) {
            $table->id();
            $table->integer('FacturaID')->unsigned(false); // igual al tipo original
            $table->date('fecha_vencimiento');
            $table->decimal('monto_total', 10, 2);
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->string('estado')->default('Pendiente');
            $table->timestamps();

            $table->foreign('FacturaID')->references('FacturaID')->on('factura')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cuentas_por_cobrar');
    }
}







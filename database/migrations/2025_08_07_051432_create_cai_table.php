<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCaiTable extends Migration
{
    public function up()
    {
        Schema::create('cai', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 37); // Código CAI
            $table->string('rango_inicial', 19); // Ej: 000-001-01-00000001
            $table->string('rango_final', 19);   // Ej: 000-001-01-00001000
            $table->date('fecha_autorizacion');
            $table->date('fecha_limite_emision');
            $table->unsignedBigInteger('facturas_emitidas')->default(0); // Control interno
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cai');
    }
}

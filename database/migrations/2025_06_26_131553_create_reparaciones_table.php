<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('reparaciones', function (Blueprint $table) {
        $table->id(); // ID de la reparación
        $table->unsignedBigInteger('cliente_id');
        $table->unsignedBigInteger('producto_id');
        $table->date('fecha_entrada')->nullable();
        $table->date('fecha_salida')->nullable();
        $table->text('descripcion')->nullable();
        $table->enum('estado', ['Pendiente', 'En proceso', 'Finalizado'])->default('Pendiente');
        $table->decimal('costo', 10, 2)->default(0);
        $table->string('UsuarioRegistro')->nullable();
        $table->timestamp('created_at')->nullable();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reparaciones');
    }
};

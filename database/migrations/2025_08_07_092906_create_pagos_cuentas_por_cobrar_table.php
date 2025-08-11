<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pagos_cuentas_por_cobrar', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cuenta_id');
            $table->decimal('monto', 10, 2);
            $table->date('fecha_pago');
            $table->timestamps();

            $table->foreign('cuenta_id')->references('id')->on('cuentas_por_cobrar')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_cuentas_por_cobrar');
    }
};


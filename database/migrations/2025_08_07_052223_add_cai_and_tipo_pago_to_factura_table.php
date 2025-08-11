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
    Schema::table('factura', function (Blueprint $table) {
        $table->string('NumeroFactura', 25)->nullable()->unique()->after('FacturaID');
        $table->string('CAI', 45)->nullable()->after('NumeroFactura');
        $table->enum('tipo_pago', ['Contado', 'Crédito'])->default('Contado')->after('Total');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('factura', function (Blueprint $table) {
        $table->dropColumn(['NumeroFactura', 'CAI', 'tipo_pago']);
    });
}

};

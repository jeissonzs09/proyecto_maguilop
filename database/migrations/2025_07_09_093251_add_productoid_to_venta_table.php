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
        Schema::table('venta', function (Blueprint $table) {
            // Comentado porque ya existe la columna ProductoID
            // $table->unsignedBigInteger('ProductoID')->after('EmpleadoID');

            // También comentamos la clave foránea si ya está definida
            // $table->foreign('ProductoID')->references('ProductoID')->on('producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropForeign(['ProductoID']);
            $table->dropColumn('ProductoID');
        });
    }
};


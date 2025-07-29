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
    Schema::table('venta', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->unsignedBigInteger('ProductoID')->after('EmpleadoID');

        // Agregar la restricción de clave foránea
        $table->foreign('ProductoID')->references('ProductoID')->on('producto');
    });
}


    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::table('venta', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->dropForeign(['ProductoID']);
        $table->dropColumn('ProductoID');
    });
}

};

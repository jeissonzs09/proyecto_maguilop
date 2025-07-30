<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Paso 1: Eliminar la clave foránea existente
        Schema::table('cliente', function (Blueprint $table) {
            $table->dropForeign('cliente_ibfk_1');
        });

        // Paso 2: Hacer la columna PersonaID nullable
        Schema::table('cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('PersonaID')->nullable()->change();
        });

        // Paso 3: Volver a agregar la clave foránea (ahora con la columna nullable)
        Schema::table('cliente', function (Blueprint $table) {
            $table->foreign('PersonaID')->references('PersonaID')->on('persona');
        });
    }

    public function down()
    {
        // Revertir los cambios: eliminar la nueva FK, volverla no nullable, y recrear la original

        Schema::table('cliente', function (Blueprint $table) {
            $table->dropForeign(['PersonaID']);
        });

        Schema::table('cliente', function (Blueprint $table) {
            $table->unsignedBigInteger('PersonaID')->nullable(false)->change();
        });

        Schema::table('cliente', function (Blueprint $table) {
            $table->foreign('PersonaID')->references('PersonaID')->on('persona');
        });
    }
};



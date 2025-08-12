<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            // 🔹 Código único (permite NULL temporalmente; MySQL permite múltiples NULL en UNIQUE)
            $table->string('Codigo', 30)->nullable()->unique()->after('ProductoID');

            // 🔹 Área: usa ENUM; si prefieres string, ver nota abajo
            $table->enum('Area', ['Electronica', 'Refrigeracion'])->nullable()->after('Descripcion');

            // 🔹 Foto (ruta en storage/app/public/productos/xxx.jpg)
            $table->string('Foto')->nullable()->after('Stock');
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropUnique(['Codigo']);
            $table->dropColumn(['Codigo', 'Area', 'Foto']);
        });
    }
};
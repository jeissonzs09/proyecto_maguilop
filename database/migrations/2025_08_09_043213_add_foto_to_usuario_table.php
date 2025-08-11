<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_foto_to_usuario_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('Foto')->nullable()->after('CorreoElectronico'); // guarda ruta tipo storage
        });
    }
    public function down(): void {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('Foto');
        });
    }
};
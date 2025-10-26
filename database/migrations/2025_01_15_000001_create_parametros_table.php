<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->string('Clave')->unique();
            $table->text('Valor');
            $table->string('Descripcion')->nullable();
            $table->string('Tipo')->default('string'); // string, number, boolean, array
            $table->boolean('Activo')->default(true);
            $table->timestamps();
        });

        // Insertar parámetros por defecto
        DB::table('parametros')->insert([
            [
                'Clave' => 'dias_vencimiento_usuario',
                'Valor' => '365',
                'Descripcion' => 'Días de vencimiento por defecto para usuarios nuevos',
                'Tipo' => 'number',
                'Activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'Clave' => 'dominios_correo_institucional',
                'Valor' => 'empresa.com,miempresa.org',
                'Descripcion' => 'Dominios permitidos para correos institucionales (separados por coma)',
                'Tipo' => 'array',
                'Activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};

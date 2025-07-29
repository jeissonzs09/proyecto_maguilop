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
    Schema::table('usuario', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->string('CorreoElectronico')->nullable()->after('NombreUsuario');
    });
}

    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::table('usuario', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->dropColumn('CorreoElectronico');
    });
}
};

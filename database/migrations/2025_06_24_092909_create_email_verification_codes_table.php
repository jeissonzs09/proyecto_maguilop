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
    Schema::create('email_verification_codes', function (Blueprint $table) {
        $table->id();
$table->integer('user_id');

        $table->string('code', 6);
        $table->timestamp('expires_at');
        $table->timestamps();

        $table->foreign('user_id')->references('UsuarioID')->on('usuario')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
public function down()
{
    Schema::dropIfExists('email_verification_codes');
}
};

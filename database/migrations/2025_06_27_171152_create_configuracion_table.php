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
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->decimal("precio_min");
            $table->decimal("por_comision");
            $table->bigInteger("fk_last_user")->unsigned();
            $table->foreign('fk_last_user')->references('id')->on('users');
            $table->index('fk_last_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table){
            $table->dropForeign("configuracion_fk_last_user_foreign");
            $table->dropIndex("configuracion_fk_last_user_index");
        });
        Schema::dropIfExists('configuracion');
    }
};

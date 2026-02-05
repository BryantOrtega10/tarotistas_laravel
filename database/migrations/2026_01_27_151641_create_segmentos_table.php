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
        Schema::create('segmentos', function (Blueprint $table) {
            $table->id();
            $table->timestamp("fecha_inicio");
            $table->timestamp("fecha_fin")->nullable();
            $table->decimal("tiempo_seg")->nullable();
            $table->bigInteger("fk_llamada")->unsigned();
            $table->foreign('fk_llamada')->references('id')->on('llamadas');
            $table->index('fk_llamada');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('segmentos', function (Blueprint $table){
            $table->dropForeign("segmentos_fk_llamada_foreign");
            $table->dropIndex("segmentos_fk_llamada_index");
        });

        Schema::dropIfExists('segmentos');
    }
};

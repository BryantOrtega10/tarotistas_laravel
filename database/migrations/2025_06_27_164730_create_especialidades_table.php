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
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
        });

        Schema::create('especialidad_tarotista', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("fk_especialidad")->unsigned();
            $table->foreign('fk_especialidad')->references('id')->on('especialidades');
            $table->index('fk_especialidad');

            $table->bigInteger("fk_tarotista")->unsigned();
            $table->foreign('fk_tarotista')->references('id')->on('tarotistas');
            $table->index('fk_tarotista');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('especialidad_tarotista', function (Blueprint $table){
            $table->dropForeign("especialidad_tarotista_fk_especialidad_foreign");
            $table->dropIndex("especialidad_tarotista_fk_especialidad_index");

            $table->dropForeign("especialidad_tarotista_fk_tarotista_foreign");
            $table->dropIndex("especialidad_tarotista_fk_tarotista_index");
        });
        
        Schema::dropIfExists('especialidad_tarotista');
        Schema::dropIfExists('especialidades');
    }
};

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
        Schema::create('cliente_tarotista', function (Blueprint $table) {
            $table->id();

            $table->tinyInteger("mensajes_gratis")->default(5)->nullable();

            $table->bigInteger("fk_cliente")->unsigned();
            $table->foreign('fk_cliente')->references('id')->on('clientes');
            $table->index('fk_cliente');

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
        Schema::table('cliente_tarotista', function (Blueprint $table){
            $table->dropForeign("cliente_tarotista_fk_cliente_foreign");
            $table->dropIndex("cliente_tarotista_fk_cliente_index");
            
            $table->dropForeign("cliente_tarotista_fk_tarotista_foreign");
            $table->dropIndex("cliente_tarotista_fk_tarotista_index");
            
        });
        Schema::dropIfExists('cliente_tarotista');
    }
};

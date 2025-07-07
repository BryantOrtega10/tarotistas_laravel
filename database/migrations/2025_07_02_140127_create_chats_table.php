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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            
            $table->string("mensaje");
            $table->tinyInteger("origen")->comment("1 - Cliente, 2 - Tarotista");
            $table->tinyInteger("tipo")->comment("1 - Mensaje, 2 - Llamada");
            $table->timestamp("leido")->nullable();

            $table->bigInteger("fk_cliente_tarotista")->unsigned();
            $table->foreign('fk_cliente_tarotista')->references('id')->on('cliente_tarotista');
            $table->index('fk_cliente_tarotista');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table){
            $table->dropForeign("chats_fk_cliente_tarotista_foreign");
            $table->dropIndex("chats_fk_cliente_tarotista_index");
        });

        Schema::dropIfExists('chats');
    }
};

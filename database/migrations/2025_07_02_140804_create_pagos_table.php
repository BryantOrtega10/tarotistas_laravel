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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->decimal("valor");
            $table->string("descripcion");
            
            $table->bigInteger("fk_entry_user")->unsigned()->comment("Usuario que creo el pago");
            $table->foreign('fk_entry_user')->references('id')->on('users');
            $table->index('fk_entry_user');

            $table->bigInteger("fk_tarotista")->unsigned();
            $table->foreign('fk_tarotista')->references('id')->on('tarotistas');
            $table->index('fk_tarotista');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table){
            $table->dropForeign("pagos_fk_entry_user_foreign");
            $table->dropIndex("pagos_fk_entry_user_index");

            $table->dropForeign("pagos_fk_tarotista_foreign");
            $table->dropIndex("pagos_fk_tarotista_index");
        });
        Schema::dropIfExists('pagos');
    }
};

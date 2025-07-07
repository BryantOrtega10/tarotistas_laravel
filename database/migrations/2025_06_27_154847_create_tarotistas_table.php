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
        Schema::create('tarotistas', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->string("descripcion_corta")->nullable();
            $table->tinyInteger("estado")->default(0)->nullable()->comment("0 - En Registro, 1 - Esperando aprobación, 2 - Activado, 3 - Rechazado");
            $table->tinyInteger("estado_conexion")->default(0)->nullable()->comment("0 - Desconectado, 1 - Conectado, 2 - En Llamada");
            $table->string("horario")->nullable();
            $table->string("anios_exp")->nullable();
            $table->decimal("calificacion")->nullable()->comment("Se actualiza con un trigger");
            $table->decimal("saldo")->default(0)->nullable()->comment("Se actualiza con un trigger");
            
            $table->string("tipo_cuenta")->nullable();
            $table->string("cuenta")->nullable();
            
            $table->bigInteger("fk_banco")->unsigned()->nullable();
            $table->foreign('fk_banco')->references('id')->on('bancos');
            $table->index('fk_banco');
            
            $table->bigInteger("fk_pais")->unsigned()->nullable();
            $table->foreign('fk_pais')->references('id')->on('paises');
            $table->index('fk_pais');

            $table->bigInteger("fk_user")->unsigned();
            $table->foreign('fk_user')->references('id')->on('users');
            $table->index('fk_user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tarotistas', function (Blueprint $table){
            $table->dropForeign("tarotistas_fk_user_foreign");
            $table->dropIndex("tarotistas_fk_user_index");

            $table->dropForeign("tarotistas_fk_banco_foreign");
            $table->dropIndex("tarotistas_fk_banco_index");

            $table->dropForeign("tarotistas_fk_pais_foreign");
            $table->dropIndex("tarotistas_fk_pais_index");
        });
        Schema::dropIfExists('tarotistas');
    }
};

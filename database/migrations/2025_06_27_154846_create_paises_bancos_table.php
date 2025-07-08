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
        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->string("bandera");
        });

        Schema::create('bancos', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->boolean("ap_tipo_cuenta")->default(0);
            
            $table->bigInteger("fk_pais")->unsigned();
            $table->foreign('fk_pais')->references('id')->on('paises');
            $table->index('fk_pais');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bancos', function (Blueprint $table){
            $table->dropForeign("bancos_fk_pais_foreign");
            $table->dropIndex("bancos_fk_pais_index");
        });
        Schema::dropIfExists('paises');
        Schema::dropIfExists('bancos');
    }
};

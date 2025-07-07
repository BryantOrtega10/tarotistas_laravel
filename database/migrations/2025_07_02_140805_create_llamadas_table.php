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
        Schema::create('llamadas', function (Blueprint $table) {
            $table->id();

            $table->timestamp("fecha_inicio")->nullable();
            $table->timestamp("fecha_fin")->nullable();
            
            $table->decimal("tarifa")->nullable();
            $table->decimal("por_comision")->nullable();
            $table->decimal("tiempo_mins")->nullable();
            $table->decimal("subtotal")->nullable();
            $table->decimal("comision")->nullable();
            $table->decimal("total")->nullable();

            $table->tinyInteger("estado_llamada")->default(1)->nullable()->comment("1 - Solicitada, 2 - Cancelada, 3 - En llamada, 4 - Terminada");
            $table->tinyInteger("estado_pago_cli")->default(1)->nullable()->comment("1 - Pago Innecesario, 2 - Pendiente, 3 - Pagado, 4 - Rechazado");
            $table->tinyInteger("estado_pago_tar")->default(1)->nullable()->comment("1 - Pago Innecesario, 2 - Sin Pagar, 3 - Pagado");

            $table->text("respuesta_payu")->nullable();

            $table->decimal("calificacion", 2, 1)->nullable();
            
            $table->string("comentario")->nullable();
            $table->string("respuesta_com")->nullable();            

            $table->bigInteger("fk_cliente_tarotista")->unsigned();
            $table->foreign('fk_cliente_tarotista')->references('id')->on('cliente_tarotista');
            $table->index('fk_cliente_tarotista');

            $table->bigInteger("fk_pago")->unsigned()->nullable();
            $table->foreign('fk_pago')->references('id')->on('pagos');
            $table->index('fk_pago');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('llamadas', function (Blueprint $table){
            $table->dropForeign("llamadas_fk_cliente_tarotista_foreign");
            $table->dropIndex("llamadas_fk_cliente_tarotista_index");

            $table->dropForeign("llamadas_fk_pago_foreign");
            $table->dropIndex("llamadas_fk_pago_index");
        });
        Schema::dropIfExists('llamadas');
    }
};

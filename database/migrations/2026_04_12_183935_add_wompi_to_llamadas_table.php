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
        Schema::table('llamadas', function (Blueprint $table) {
            
            $table->dropColumn("estado_pago_cli");
            $table->dropColumn("respuesta_payu");
            $table->dropColumn("tarifa");
            $table->integer("tarifa_valor_min",false, true)->nullable()->after("fecha_fin");
            $table->integer("tarifa_token_min",false, true)->nullable()->after("fecha_fin");
            $table->integer("tokens_gastados",false, true)->default(0)->nullable()->after("tiempo_mins");
        });

        

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('llamadas', function (Blueprint $table) {
            $table->tinyInteger("estado_pago_cli")->default(1)->nullable()->comment("1 - Pago Innecesario, 2 - Pendiente, 3 - Pagado, 4 - Rechazado");
            $table->text("respuesta_payu")->nullable();
            $table->decimal("tarifa")->nullable();
            
            $table->dropColumn("tarifa_valor_min");
            $table->dropColumn("tarifa_token_min");
            $table->dropColumn("tokens_gastados");
        });
    }
};

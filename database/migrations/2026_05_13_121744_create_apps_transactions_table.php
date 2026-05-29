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
        Schema::create('apps_transactions', function (Blueprint $table) {
            $table->id();
            
            $table->string("platform");
            $table->string("purchase_token", 250);
            $table->string("order_id");
            
            $table->integer("tokens", false, true);
            $table->integer("valor", false, true);
            $table->tinyInteger("status")->comment("0 - Pendiente, 1 - Pagando, 2 - Aprobado, 3 - Rechazado, 4 - Anulado, 5 - Error")->default(0);

            $table->bigInteger("fk_cliente")->unsigned();
            $table->foreign('fk_cliente')->references('id')->on('clientes');
            $table->index('fk_cliente');

            $table->bigInteger("fk_paquete")->unsigned();
            $table->foreign('fk_paquete')->references('id')->on('paquetes');
            $table->index('fk_paquete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps_transactions');
    }
};

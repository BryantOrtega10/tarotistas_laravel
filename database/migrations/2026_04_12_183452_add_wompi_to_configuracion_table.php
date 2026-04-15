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
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn("precio_min");
            $table->integer("token_min", false, true);
            $table->integer("valor_min");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->decimal("precio_min");
            $table->dropColumn("token_min");
            $table->dropColumn("valor_min");
        });
    }
};

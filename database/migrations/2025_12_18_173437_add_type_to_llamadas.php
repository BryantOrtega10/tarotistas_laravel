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
            $table->string("type")->default("call-start")->comment("call-start, webrtc-offer, webrtc-answer, webrtc-ice, call-end")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('llamadas', function (Blueprint $table) {
            $table->dropColumn("type");
        });
    }
};

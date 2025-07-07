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
        Schema::table('users', function (Blueprint $table) {
            $table->string("photo")->after("remember_token")->nullable();
            $table->string('provider_id')->after("photo")->nullable();
            $table->string('provider')->after("provider_id")->nullable()->default("Correo");
            $table->string('role')->after("provider");
            $table->string('password')->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn("photo");
            $table->dropColumn('provider_id');
            $table->dropColumn('provider');
            $table->dropColumn('role');
            $table->string('password')->nullable(false)->change();
        });
    }
};

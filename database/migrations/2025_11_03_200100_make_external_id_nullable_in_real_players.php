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
        Schema::table('real_players', function (Blueprint $table) {
            $table->unsignedBigInteger('external_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('real_players', function (Blueprint $table) {
            $table->unsignedBigInteger('external_id')->nullable(false)->change();
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('real_competitions', function (Blueprint $table) {
            // Hacer external_id opcional para permitir carga manual sin API
            $table->unsignedBigInteger('external_id')->nullable()->change();
            // external_source ya tiene default('livescore'), solo lo hacemos nullable
            $table->string('external_source')->nullable()->default('manual')->change();
        });
    }

    public function down(): void
    {
        Schema::table('real_competitions', function (Blueprint $table) {
            $table->unsignedBigInteger('external_id')->nullable(false)->change();
            $table->string('external_source')->nullable(false)->default('livescore')->change();
        });
    }
};
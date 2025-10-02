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
        Schema::create('quiz_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 'rules', 'history', 'players', 'tournaments'
            $table->string('name');
            $table->string('locale', 5)->default('es');
            $table->timestamps();

            // Índices
            $table->index('code');
            $table->index('locale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_categories');
    }
};
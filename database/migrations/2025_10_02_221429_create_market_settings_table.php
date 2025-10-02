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
        Schema::create('market_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->unique() // Una configuración por liga
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->decimal('max_multiplier', 5, 2)->default(3.00); // Cap: 3x valor de mercado
            $table->boolean('trade_window_open')->default(true);
            $table->boolean('loan_allowed')->default(true);
            $table->smallInteger('min_offer_cooldown_h')->default(2); // Horas entre ofertas
            $table->json('data')->nullable(); // Configuraciones adicionales
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_settings');
    }
};
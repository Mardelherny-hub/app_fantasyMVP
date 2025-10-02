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
        Schema::create('gameweeks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')
                  ->constrained('seasons')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->smallInteger('number'); // GW número (1-30)
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->boolean('is_closed')->default(false); // Cierra alineaciones/mercado
            
            // ========== CAMPOS DE PLAYOFFS ==========
            $table->boolean('is_playoff')->default(false);
            $table->tinyInteger('playoff_round')->nullable(); // 1=cuartos, 2=semis, 3=final
            
            $table->timestamps();

            // Una gameweek por temporada
            $table->unique(['season_id', 'number']);
            
            // Índices
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index(['season_id', 'is_closed']);
            $table->index(['season_id', 'is_playoff']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gameweeks');
    }
};
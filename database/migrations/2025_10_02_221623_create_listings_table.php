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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('fantasy_team_id') // Equipo vendedor
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('player_id')
                  ->constrained('players')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->decimal('price', 12, 2);
            $table->tinyInteger('status')->default(0); // 0=active, 1=sold, 2=withdrawn, 3=expired
            $table->datetime('expires_at')->nullable();
            $table->timestamps();

            // Solo un listing activo por jugador por liga
            $table->unique(['league_id', 'player_id', 'status'], 'unique_active_listing');
            
            // Índices
            $table->index(['league_id', 'status']);
            $table->index('fantasy_team_id');
            $table->index('player_id');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
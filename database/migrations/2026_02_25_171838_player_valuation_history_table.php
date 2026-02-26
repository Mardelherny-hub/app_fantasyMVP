<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_valuation_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->foreignId('gameweek_id')->constrained('gameweeks')->cascadeOnDelete();
            $table->decimal('market_value', 12, 2);
            $table->decimal('previous_value', 12, 2)->nullable();
            $table->string('source')->default('auto'); // 'auto' o 'manual'
            $table->timestamps();

            $table->unique(['player_id', 'season_id', 'gameweek_id']);
            $table->index('gameweek_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_valuation_history');
    }
};
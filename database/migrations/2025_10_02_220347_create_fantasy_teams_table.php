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
        Schema::create('fantasy_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')
                  ->constrained('leagues')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->nullable() // NULL para equipos bot
                  ->constrained('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('emblem_url')->nullable();
            $table->integer('total_points')->default(0)->index();
            $table->decimal('budget', 12, 2)->default(100.00); // Presupuesto para mercado
            $table->boolean('is_bot')->default(false);
            $table->timestamps();
            $table->softDeletes();

            // Un equipo por usuario por liga
            $table->unique(['league_id', 'user_id']);
            
            // Un nombre único por liga
            $table->unique(['league_id', 'name']);
            
            // Índices
            $table->index('league_id');
            $table->index('user_id');
            $table->index(['league_id', 'total_points']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fantasy_teams');
    }
};
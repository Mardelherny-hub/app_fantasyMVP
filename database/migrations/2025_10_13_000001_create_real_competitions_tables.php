<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ligas/Copas "del mundo real" (IDs de la API incluidos)
        Schema::create('real_competitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('external_id')->unique()->index(); // p.ej. 257, 448, 76, 258
            $table->string('name');
            $table->string('country')->nullable();         // "Canada", "England", etc.
            $table->enum('type', ['league','cup'])->nullable(); // inferido de la API
            $table->boolean('active')->default(true);
            $table->string('external_source')->default('livescore'); // proveedor
            $table->json('meta')->nullable();              // json crudo opcional
            $table->timestamps();
        });

        // Relación de inscripción por temporada
        // NOTA: Asumo que YA tenés tabla seasons (la mencionaste).
        // Relaciona competición + temporada + equipo
        Schema::create('real_competition_team_season', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('real_competition_id')->index();
            $table->unsignedBigInteger('season_id')->index();      // FK a seasons.id (ya existente)
            $table->unsignedBigInteger('real_team_id')->index();   // FK a teams reales (ver tabla más abajo)
            $table->timestamps();

            $table->unique(['real_competition_id','season_id','real_team_id'], 'uniq_comp_season_team');

            $table->foreign('real_competition_id')->references('id')->on('real_competitions')->cascadeOnUpdate()->cascadeOnDelete();
            // Dejamos las otras FKs diferidas para no atarnos a nombres de tus tablas (ver más abajo).
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_competition_team_season');
        Schema::dropIfExists('real_competitions');
    }
};

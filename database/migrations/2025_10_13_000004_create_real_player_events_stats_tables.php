<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Eventos por jugador en un partido (goles, asistencias, tarjetas, penales, cambios, etc.)
        Schema::create('real_player_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('real_match_id')->index();
            $table->unsignedBigInteger('real_team_id')->index();
            $table->unsignedBigInteger('real_player_id')->index();
            $table->string('type', 32); // goal, assist, yellow, red, own_goal, sub_in, sub_out, penalty_scored, penalty_missed, etc.
            $table->unsignedSmallInteger('minute')->nullable();
            $table->json('data')->nullable(); // payload crudo (ej: "penalty" => true)
            $table->timestamps();

            $table->index(['real_match_id','real_player_id','type','minute'], 'idx_event_dedup');

            $table->foreign('real_match_id')->references('id')->on('real_matches')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_team_id')->references('id')->on('real_teams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_player_id')->references('id')->on('real_players')->cascadeOnUpdate()->cascadeOnDelete();
        });

        // Estadísticas agregadas por jugador/partido (derivadas o provistas por la API)
        Schema::create('real_player_stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('real_match_id')->index();
            $table->unsignedBigInteger('real_team_id')->index();
            $table->unsignedBigInteger('real_player_id')->index();
            $table->unsignedSmallInteger('minutes')->nullable();
            $table->unsignedSmallInteger('goals')->nullable();
            $table->unsignedSmallInteger('assists')->nullable();
            $table->unsignedSmallInteger('yellow_cards')->nullable();
            $table->unsignedSmallInteger('red_cards')->nullable();
            $table->smallInteger('rating')->nullable(); // si la API lo diera (x100 o 0-10)
            $table->json('data')->nullable(); // otros KPIs si el proveedor los trae
            $table->timestamps();

            $table->unique(['real_match_id','real_player_id'], 'uniq_stats_match_player');

            $table->foreign('real_match_id')->references('id')->on('real_matches')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_team_id')->references('id')->on('real_teams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_player_id')->references('id')->on('real_players')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_player_stats');
        Schema::dropIfExists('real_player_events');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Equipos reales
        Schema::create('real_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('external_id')->unique()->index(); // id de la API (team)
            $table->string('name');
            $table->string('country')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('stadium')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // Jugadores reales
        Schema::create('real_players', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('external_id')->unique()->index(); // id de la API (player) si existe
            $table->string('full_name');
            $table->string('position', 5)->nullable();   // GK, DF, MF, FW (si lo trae/si lo usamos)
            $table->date('birthdate')->nullable();
            $table->string('nationality')->nullable();
            $table->string('photo_url')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        // Membresía de jugador en equipo por temporada (plantel)
        Schema::create('real_team_memberships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('real_team_id')->index();
            $table->unsignedBigInteger('real_player_id')->index();
            $table->unsignedBigInteger('season_id')->nullable()->index(); // FK a seasons (si se quiere)
            $table->unsignedSmallInteger('shirt_number')->nullable();
            $table->string('role', 20)->nullable(); // titular/suplente/etc. si alguna vez sirviera
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['real_team_id','real_player_id','season_id'], 'uniq_membership_team_player_season');

            $table->foreign('real_team_id')->references('id')->on('real_teams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_player_id')->references('id')->on('real_players')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_team_memberships');
        Schema::dropIfExists('real_players');
        Schema::dropIfExists('real_teams');
    }
};

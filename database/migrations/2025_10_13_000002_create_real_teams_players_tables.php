<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Equipos reales
        // Equipos reales
        Schema::create('real_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('external_id')->unique()->index(); // id de la API (team)
            $table->string('name');
            $table->string('short_name')->nullable(); // ⬅️ AGREGADO
            $table->string('country')->nullable();
            $table->string('founded_year')->nullable(); // ⬅️ AGREGADO (cambiado a string por si viene formato año)
            $table->string('logo_url')->nullable();
            $table->string('stadium')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes(); // ⬅️ AGREGADO (el modelo usa SoftDeletes)
        });

        // Jugadores reales
        Schema::create('real_players', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('external_id')->unique()->index();
            $table->string('full_name');
            $table->string('position', 5)->nullable();
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
            $table->unsignedBigInteger('season_id')->nullable()->index();
            $table->unsignedSmallInteger('shirt_number')->nullable();
            $table->string('role', 20)->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['real_team_id','real_player_id','season_id'], 'uniq_membership_team_player_season');

            $table->foreign('real_team_id')->references('id')->on('real_teams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_player_id')->references('id')->on('real_players')->cascadeOnUpdate()->cascadeOnDelete();
        });

        // ⬇️ AGREGAR ESTAS LÍNEAS AL FINAL DEL MÉTODO up() ⬇️
        // Agregar foreign key de players hacia real_players
        Schema::table('players', function (Blueprint $table) {
            $table->foreign('real_player_id')
                  ->references('id')
                  ->on('real_players')
                  ->nullOnDelete();
        });
        // ⬆️ FIN DE LÍNEAS NUEVAS ⬆️
    }

    public function down(): void
    {
        // ⬇️ AGREGAR ESTA LÍNEA AL INICIO DEL MÉTODO down() ⬇️
        Schema::table('players', function (Blueprint $table) {
            $table->dropForeign(['real_player_id']);
        });
        // ⬆️ FIN DE LÍNEA NUEVA ⬆️
        
        Schema::dropIfExists('real_team_memberships');
        Schema::dropIfExists('real_players');
        Schema::dropIfExists('real_teams');
    }
};
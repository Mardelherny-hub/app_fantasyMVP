<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Fixture = partido programado (calendario)
        Schema::create('real_fixtures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('external_id')->unique()->index(); // fixture_id de la API
            $table->unsignedBigInteger('real_competition_id')->index();
            $table->unsignedBigInteger('season_id')->nullable()->index(); // seasons.id si la usás
            $table->unsignedBigInteger('home_team_id')->nullable()->index(); // real_teams.id
            $table->unsignedBigInteger('away_team_id')->nullable()->index();
            $table->string('round')->nullable();
            $table->string('venue')->nullable();
            $table->enum('status', ['scheduled','live','finished','postponed','canceled'])->nullable();
            $table->date('match_date_utc')->nullable();
            $table->time('match_time_utc')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('real_competition_id')->references('id')->on('real_competitions')->cascadeOnUpdate()->cascadeOnDelete();
        });

        // Match = instancia jugada (puede referenciar el fixture)
        Schema::create('real_matches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('external_id')->unique()->index(); // match_id de la API (si difiere de fixture)
            $table->unsignedBigInteger('real_fixture_id')->nullable()->index();
            $table->enum('status', ['live','finished','ht','ft','postponed','canceled'])->nullable();
            $table->unsignedSmallInteger('minute')->nullable();
            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('away_score')->nullable();
            $table->dateTime('started_at_utc')->nullable();
            $table->dateTime('finished_at_utc')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('real_fixture_id')->references('id')->on('real_fixtures')->nullOnDelete();
        });

        // Alineaciones por partido (quién jugó)
        Schema::create('real_lineups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('real_match_id')->index();
            $table->unsignedBigInteger('real_team_id')->index();
            $table->unsignedBigInteger('real_player_id')->index();
            $table->boolean('starter')->default(false); // titular (true) / suplente (false)
            $table->unsignedSmallInteger('minutes')->nullable(); // si la API no lo da, luego lo inferimos por cambios
            $table->string('position', 5)->nullable();
            $table->unsignedSmallInteger('shirt_number')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['real_match_id','real_player_id'], 'uniq_lineup_match_player');

            $table->foreign('real_match_id')->references('id')->on('real_matches')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_team_id')->references('id')->on('real_teams')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_player_id')->references('id')->on('real_players')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_lineups');
        Schema::dropIfExists('real_matches');
        Schema::dropIfExists('real_fixtures');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('real_competition_standings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('real_competition_id')->index();
            $table->unsignedBigInteger('season_id')->nullable()->index();
            $table->string('stage')->nullable();  // Regular Season, Playoffs, etc.
            $table->string('group')->nullable();  // Grupo A/B, Eastern/Western, etc.
            $table->unsignedBigInteger('real_team_id')->index();
            $table->unsignedSmallInteger('rank')->nullable();
            $table->unsignedSmallInteger('played')->nullable();
            $table->unsignedSmallInteger('won')->nullable();
            $table->unsignedSmallInteger('drawn')->nullable();
            $table->unsignedSmallInteger('lost')->nullable();
            $table->unsignedSmallInteger('goals_for')->nullable();
            $table->unsignedSmallInteger('goals_against')->nullable();
            $table->smallInteger('goal_diff')->nullable();
            $table->unsignedSmallInteger('points')->nullable();
            $table->json('form')->nullable(); // rachas si la API lo trae
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(
                ['real_competition_id','season_id','stage','group','real_team_id'],
                'uniq_real_standings'
            );

            $table->foreign('real_competition_id')->references('id')->on('real_competitions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('real_team_id')->references('id')->on('real_teams')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('real_competition_standings');
    }
};

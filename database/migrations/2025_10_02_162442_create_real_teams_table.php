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
        Schema::create('real_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name', 10);
            $table->string('country', 2); // ISO 3166-1 alpha-2 (AR, ES, FR, etc.)
            $table->smallInteger('founded_year')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Un equipo es único por nombre + país
            $table->unique(['name', 'country']);
            
            // Índice para búsquedas por país
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('real_teams');
    }
};
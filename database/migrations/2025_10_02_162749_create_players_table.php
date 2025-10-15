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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('known_as')->nullable(); // Nombre conocido (ej: "Messi")
            $table->tinyInteger('position'); // 1=GK, 2=DF, 3=MF, 4=FW
            $table->string('nationality', 2)->nullable(); // ISO 3166-1 alpha-2
            $table->date('birthdate')->nullable();
            $table->smallInteger('height_cm')->nullable();
            $table->smallInteger('weight_kg')->nullable();
            $table->string('photo_url')->nullable();
            $table->unsignedBigInteger('real_player_id')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas frecuentes
            $table->index('position');
            $table->index('nationality');
            $table->index('full_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
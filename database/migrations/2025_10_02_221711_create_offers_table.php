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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')
                  ->constrained('listings')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('buyer_fantasy_team_id')
                  ->constrained('fantasy_teams')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->decimal('offered_price', 12, 2);
            $table->tinyInteger('status')->default(0); // 0=pending, 1=accepted, 2=rejected, 3=expired
            $table->timestamps();

            // Índices
            $table->index(['listing_id', 'status']);
            $table->index('buyer_fantasy_team_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
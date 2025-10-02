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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // 'TRIVIA_WIN', 'GW_WIN', 'PLAYOFF_CHAMPION'
            $table->string('label');
            $table->decimal('amount', 14, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            // Índice
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
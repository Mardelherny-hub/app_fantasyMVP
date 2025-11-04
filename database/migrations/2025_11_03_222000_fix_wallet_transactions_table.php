<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Fix: Agrega las columnas faltantes a wallet_transactions
     */
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Verificar y agregar columnas si no existen
            if (!Schema::hasColumn('wallet_transactions', 'wallet_id')) {
                $table->foreignId('wallet_id')
                      ->after('id')
                      ->constrained('wallets')
                      ->onUpdate('cascade')
                      ->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('wallet_transactions', 'type')) {
                $table->tinyInteger('type')->after('wallet_id'); // 1=credit, 2=debit
            }
            
            if (!Schema::hasColumn('wallet_transactions', 'amount')) {
                $table->decimal('amount', 14, 2)->after('type');
            }
            
            if (!Schema::hasColumn('wallet_transactions', 'reason')) {
                $table->string('reason')->after('amount'); // 'match_win', 'trivia_reward', etc.
            }
            
            if (!Schema::hasColumn('wallet_transactions', 'reference_type')) {
                $table->string('reference_type')->nullable()->after('reason'); // Morph
            }
            
            if (!Schema::hasColumn('wallet_transactions', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }
            
            if (!Schema::hasColumn('wallet_transactions', 'meta')) {
                $table->json('meta')->nullable()->after('reference_id');
            }
        });

        // Agregar índices
        try {
            Schema::table('wallet_transactions', function (Blueprint $table) {
                $table->index(['wallet_id', 'created_at']);
                $table->index(['reference_type', 'reference_id']);
                $table->index('type');
            });
        } catch (\Exception $e) {
            // Índices ya existen, continuar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex(['wallet_id', 'created_at']);
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropIndex(['type']);
            
            // Eliminar columnas
            $table->dropForeign(['wallet_id']);
            $table->dropColumn([
                'wallet_id',
                'type',
                'amount',
                'reason',
                'reference_type',
                'reference_id',
                'meta',
            ]);
        });
    }
};
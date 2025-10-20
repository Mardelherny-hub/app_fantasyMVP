<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;
use App\Models\MarketSettings;

class MarketSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏪 Creando configuraciones de mercado...');

        $leagues = League::all();

        if ($leagues->isEmpty()) {
            $this->command->warn('⚠️  No hay ligas. Crea ligas primero.');
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($leagues as $league) {
            // Verificar si ya existe configuración
            if ($league->marketSettings()->exists()) {
                $skipped++;
                continue;
            }

            // Crear configuración con valores por defecto
            MarketSettings::create([
                'league_id' => $league->id,
                'max_multiplier' => 3.00,
                'trade_window_open' => true,
                'loan_allowed' => false,
                'min_offer_cooldown_h' => 2,
                'data' => null,
            ]);

            $created++;
        }

        $this->command->info("✅ Configuraciones creadas: {$created}");
        
        if ($skipped > 0) {
            $this->command->info("⏭️  Configuraciones saltadas (ya existían): {$skipped}");
        }
    }
}
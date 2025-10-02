<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\ScoringRule;
use Illuminate\Database\Seeder;

class ScoringRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la temporada activa
        $season = Season::where('is_active', true)->first();

        if (!$season) {
            $this->command->warn('⚠️  No hay temporada activa. Ejecuta SeasonsSeeder primero.');
            return;
        }

        $this->command->info("⚽ Generando scoring rules para temporada {$season->name}...");

        $rules = [
            // ========================================
            // MINUTOS JUGADOS
            // ========================================
            [
                'code' => ScoringRule::CODE_MINUTES_0_59,
                'label' => 'Played 1-59 minutes',
                'points' => 1,
            ],
            [
                'code' => ScoringRule::CODE_MINUTES_60_PLUS,
                'label' => 'Played 60+ minutes',
                'points' => 2,
            ],

            // ========================================
            // GOLES POR POSICIÓN
            // ========================================
            [
                'code' => ScoringRule::CODE_GOAL_GK,
                'label' => 'Goal scored by Goalkeeper',
                'points' => 8,
            ],
            [
                'code' => ScoringRule::CODE_GOAL_DF,
                'label' => 'Goal scored by Defender',
                'points' => 6,
            ],
            [
                'code' => ScoringRule::CODE_GOAL_MF,
                'label' => 'Goal scored by Midfielder',
                'points' => 5,
            ],
            [
                'code' => ScoringRule::CODE_GOAL_FW,
                'label' => 'Goal scored by Forward',
                'points' => 4,
            ],

            // ========================================
            // ASISTENCIAS
            // ========================================
            [
                'code' => ScoringRule::CODE_ASSIST,
                'label' => 'Assist',
                'points' => 3,
            ],

            // ========================================
            // CLEAN SHEETS (PORTERÍA A CERO)
            // ========================================
            [
                'code' => ScoringRule::CODE_CS_GK,
                'label' => 'Clean sheet by Goalkeeper',
                'points' => 4,
            ],
            [
                'code' => ScoringRule::CODE_CS_DF,
                'label' => 'Clean sheet by Defender',
                'points' => 4,
            ],
            [
                'code' => ScoringRule::CODE_CS_MF,
                'label' => 'Clean sheet by Midfielder',
                'points' => 1,
            ],

            // ========================================
            // ATAJADAS (PORTEROS)
            // ========================================
            [
                'code' => ScoringRule::CODE_SAVE_3,
                'label' => 'Every 3 saves',
                'points' => 1,
            ],
            [
                'code' => ScoringRule::CODE_PENALTY_SAVED,
                'label' => 'Penalty saved',
                'points' => 5,
            ],
            [
                'code' => ScoringRule::CODE_PENALTY_MISSED,
                'label' => 'Penalty missed',
                'points' => -2,
            ],

            // ========================================
            // GOLES RECIBIDOS
            // ========================================
            [
                'code' => ScoringRule::CODE_GOALS_CONCEDED_2_GK,
                'label' => 'Every 2 goals conceded by Goalkeeper',
                'points' => -1,
            ],
            [
                'code' => ScoringRule::CODE_GOALS_CONCEDED_2_DF,
                'label' => 'Every 2 goals conceded by Defender',
                'points' => -1,
            ],

            // ========================================
            // TARJETAS
            // ========================================
            [
                'code' => ScoringRule::CODE_YELLOW,
                'label' => 'Yellow card',
                'points' => -1,
            ],
            [
                'code' => ScoringRule::CODE_RED,
                'label' => 'Red card',
                'points' => -3,
            ],
        ];

        foreach ($rules as $ruleData) {
            ScoringRule::firstOrCreate(
                [
                    'season_id' => $season->id,
                    'code' => $ruleData['code'],
                ],
                $ruleData
            );
        }

        $this->command->info("✅ {count($rules)} scoring rules creadas para la temporada {$season->name}");
    }
}
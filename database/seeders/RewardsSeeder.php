<?php

namespace Database\Seeders;

use App\Models\Reward;
use Illuminate\Database\Seeder;

class RewardsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('💰 Creando catálogo de recompensas...');

        $rewards = [
            // ========================================
            // TRIVIA REWARDS
            // ========================================
            [
                'code' => 'TRIVIA_QUICK_COMPLETE',
                'label' => 'Trivia Rápida Completada',
                'amount' => 50.00,
                'meta' => [
                    'description' => 'Recompensa por completar una trivia rápida',
                    'icon' => '🎯',
                ],
            ],
            [
                'code' => 'TRIVIA_PERFECT_SCORE',
                'label' => 'Trivia Perfecta (100% aciertos)',
                'amount' => 150.00,
                'meta' => [
                    'description' => 'Bonus por responder todas las preguntas correctamente',
                    'icon' => '🏆',
                ],
            ],
            [
                'code' => 'TRIVIA_THEMATIC_COMPLETE',
                'label' => 'Quiz Temático Completado',
                'amount' => 75.00,
                'meta' => [
                    'description' => 'Recompensa por completar un quiz temático',
                    'icon' => '📚',
                ],
            ],
            [
                'code' => 'TRIVIA_PVP_WIN',
                'label' => 'Victoria en PvP',
                'amount' => 100.00,
                'meta' => [
                    'description' => 'Recompensa por ganar un desafío PvP',
                    'icon' => '⚔️',
                ],
            ],

            // ========================================
            // FANTASY REWARDS
            // ========================================
            [
                'code' => 'GW_TOP_SCORER',
                'label' => 'Máximo Goleador de la Gameweek',
                'amount' => 200.00,
                'meta' => [
                    'description' => 'Recompensa por tener el equipo con más puntos en la gameweek',
                    'icon' => '👑',
                ],
            ],
            [
                'code' => 'GW_WIN',
                'label' => 'Victoria en Gameweek',
                'amount' => 100.00,
                'meta' => [
                    'description' => 'Recompensa por ganar tu partido de gameweek',
                    'icon' => '✅',
                ],
            ],
            [
                'code' => 'CLEAN_SHEET_BONUS',
                'label' => 'Bonus Portería a Cero',
                'amount' => 50.00,
                'meta' => [
                    'description' => 'Bonus adicional por mantener clean sheet con tu equipo',
                    'icon' => '🧤',
                ],
            ],

            // ========================================
            // LEAGUE REWARDS
            // ========================================
            [
                'code' => 'LEAGUE_CHAMPION',
                'label' => 'Campeón de Liga',
                'amount' => 1000.00,
                'meta' => [
                    'description' => 'Recompensa por ganar la fase regular (1° posición)',
                    'icon' => '🏅',
                ],
            ],
            [
                'code' => 'LEAGUE_RUNNER_UP',
                'label' => 'Subcampeón de Liga',
                'amount' => 500.00,
                'meta' => [
                    'description' => 'Recompensa por terminar 2° en la fase regular',
                    'icon' => '🥈',
                ],
            ],
            [
                'code' => 'LEAGUE_THIRD_PLACE',
                'label' => 'Tercer Lugar de Liga',
                'amount' => 300.00,
                'meta' => [
                    'description' => 'Recompensa por terminar 3° en la fase regular',
                    'icon' => '🥉',
                ],
            ],

            // ========================================
            // PLAYOFF REWARDS
            // ========================================
            [
                'code' => 'PLAYOFF_CHAMPION',
                'label' => 'Campeón de Playoffs',
                'amount' => 2000.00,
                'meta' => [
                    'description' => 'Recompensa máxima por ganar los playoffs (GW30)',
                    'icon' => '🏆',
                ],
            ],
            [
                'code' => 'PLAYOFF_FINALIST',
                'label' => 'Finalista de Playoffs',
                'amount' => 750.00,
                'meta' => [
                    'description' => 'Recompensa por llegar a la final',
                    'icon' => '🎖️',
                ],
            ],
            [
                'code' => 'PLAYOFF_SEMIFINALIST',
                'label' => 'Semifinalista',
                'amount' => 300.00,
                'meta' => [
                    'description' => 'Recompensa por llegar a semifinales',
                    'icon' => '🎗️',
                ],
            ],

            // ========================================
            // ACHIEVEMENT REWARDS
            // ========================================
            [
                'code' => 'FIRST_TEAM_CREATED',
                'label' => 'Primer Equipo Creado',
                'amount' => 100.00,
                'meta' => [
                    'description' => 'Bonus de bienvenida por crear tu primer equipo',
                    'icon' => '🎁',
                ],
            ],
            [
                'code' => 'FIRST_TRADE_COMPLETED',
                'label' => 'Primera Transferencia',
                'amount' => 50.00,
                'meta' => [
                    'description' => 'Bonus por completar tu primera transferencia en el mercado',
                    'icon' => '💼',
                ],
            ],
            [
                'code' => 'WEEKLY_LOGIN',
                'label' => 'Login Semanal',
                'amount' => 25.00,
                'meta' => [
                    'description' => 'Bonus por iniciar sesión todos los días de la semana',
                    'icon' => '📅',
                ],
            ],
        ];

        foreach ($rewards as $rewardData) {
            Reward::firstOrCreate(
                ['code' => $rewardData['code']],
                $rewardData
            );
        }

        $this->command->info('✅ ' . count($rewards) . ' recompensas creadas en el catálogo');
        
        // Mostrar total de CAN en el sistema
        $totalCan = array_sum(array_column($rewards, 'amount'));
        $this->command->info("💎 Valor total del catálogo: " . number_format($totalCan, 2) . " CAN");
    }
}
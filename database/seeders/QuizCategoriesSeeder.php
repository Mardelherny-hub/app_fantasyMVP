<?php

namespace Database\Seeders;

use App\Models\QuizCategory;
use Illuminate\Database\Seeder;

class QuizCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('📚 Creando categorías de trivia...');

        $categories = [
            // Español
            [
                'code' => 'rules',
                'name' => 'Reglas del Fútbol',
                'locale' => 'es',
            ],
            [
                'code' => 'history',
                'name' => 'Historia del Fútbol',
                'locale' => 'es',
            ],
            [
                'code' => 'players',
                'name' => 'Jugadores Legendarios',
                'locale' => 'es',
            ],
            [
                'code' => 'tournaments',
                'name' => 'Torneos y Competiciones',
                'locale' => 'es',
            ],
            [
                'code' => 'clubs',
                'name' => 'Clubes Históricos',
                'locale' => 'es',
            ],

            // English
            [
                'code' => 'rules',
                'name' => 'Football Rules',
                'locale' => 'en',
            ],
            [
                'code' => 'history',
                'name' => 'Football History',
                'locale' => 'en',
            ],
            [
                'code' => 'players',
                'name' => 'Legendary Players',
                'locale' => 'en',
            ],
            [
                'code' => 'tournaments',
                'name' => 'Tournaments & Competitions',
                'locale' => 'en',
            ],
            [
                'code' => 'clubs',
                'name' => 'Historic Clubs',
                'locale' => 'en',
            ],

            // Français
            [
                'code' => 'rules',
                'name' => 'Règles du Football',
                'locale' => 'fr',
            ],
            [
                'code' => 'history',
                'name' => 'Histoire du Football',
                'locale' => 'fr',
            ],
            [
                'code' => 'players',
                'name' => 'Joueurs Légendaires',
                'locale' => 'fr',
            ],
            [
                'code' => 'tournaments',
                'name' => 'Tournois et Compétitions',
                'locale' => 'fr',
            ],
            [
                'code' => 'clubs',
                'name' => 'Clubs Historiques',
                'locale' => 'fr',
            ],
        ];

        foreach ($categories as $categoryData) {
            QuizCategory::firstOrCreate(
                [
                    'code' => $categoryData['code'],
                    'locale' => $categoryData['locale'],
                ],
                $categoryData
            );
        }

        $this->command->info('✅ 15 categorías de trivia creadas (5 en ES, 5 en EN, 5 en FR)');
    }
}
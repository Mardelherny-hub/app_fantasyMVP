<?php

namespace Database\Seeders;

use App\Models\QuizCategory;
use App\Models\QuizCategoryTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📚 Creando categorías de trivia...');

        // Definir códigos de categorías
        $categoryCodes = [
            'rules' => [
                'es' => 'Reglas del Fútbol',
                'en' => 'Football Rules',
                'fr' => 'Règles du Football',
            ],
            'history' => [
                'es' => 'Historia del Fútbol',
                'en' => 'Football History',
                'fr' => 'Histoire du Football',
            ],
            'players' => [
                'es' => 'Jugadores Legendarios',
                'en' => 'Legendary Players',
                'fr' => 'Joueurs Légendaires',
            ],
            'tournaments' => [
                'es' => 'Torneos y Competiciones',
                'en' => 'Tournaments & Competitions',
                'fr' => 'Tournois et Compétitions',
            ],
            'clubs' => [
                'es' => 'Clubes Históricos',
                'en' => 'Historic Clubs',
                'fr' => 'Clubs Historiques',
            ],
        ];

        foreach ($categoryCodes as $code => $translations) {
            // Crear categoría base
            $category = \App\Models\QuizCategory::firstOrCreate(
                ['code' => $code]
            );

            // Crear traducciones
            foreach ($translations as $locale => $name) {
                \App\Models\QuizCategoryTranslation::firstOrCreate(
                    [
                        'quiz_category_id' => $category->id,
                        'locale' => $locale,
                    ],
                    ['name' => $name]
                );
            }
        }

        $this->command->info('✅ 5 categorías creadas con 15 traducciones (3 idiomas cada una)');
    }
}

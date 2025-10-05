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
        $data = [
            'rules'       => ['es' => 'Reglas del Fútbol', 'en' => 'Football Rules', 'fr' => 'Règles du Football'],
            'history'     => ['es' => 'Historia',          'en' => 'History',        'fr' => 'Histoire'],
            'players'     => ['es' => 'Jugadores',         'en' => 'Players',        'fr' => 'Joueurs'],
            'tournaments' => ['es' => 'Torneos',           'en' => 'Tournaments',    'fr' => 'Tournois'],
        ];

        DB::transaction(function () use ($data) {
            foreach ($data as $code => $translations) {
                $category = QuizCategory::query()->updateOrCreate(['code' => $code], []);

                foreach ($translations as $locale => $name) {
                    QuizCategoryTranslation::query()->updateOrCreate(
                        ['quiz_category_id' => $category->id, 'locale' => $locale],
                        ['name' => $name]
                    );
                }
            }
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\QuizCategory;
use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\QuestionOption;
use App\Models\QuestionOptionTranslation;
use Illuminate\Database\Seeder;

class DemoQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('❓ Creando preguntas demo con traducciones...');

        // ========================================
        // PREGUNTA 1: Reglas - Fácil
        // ========================================
        $this->createQuestion(
            'rules',
            Question::DIFFICULTY_EASY,
            [
                'es' => '¿Cuántos jugadores conforman un equipo de fútbol en el campo?',
                'en' => 'How many players make up a football team on the field?',
                'fr' => 'Combien de joueurs composent une équipe de football sur le terrain?',
            ],
            [
                ['es' => '11 jugadores', 'en' => '11 players', 'fr' => '11 joueurs', 'is_correct' => true],
                ['es' => '10 jugadores', 'en' => '10 players', 'fr' => '10 joueurs', 'is_correct' => false],
                ['es' => '12 jugadores', 'en' => '12 players', 'fr' => '12 joueurs', 'is_correct' => false],
                ['es' => '9 jugadores', 'en' => '9 players', 'fr' => '9 joueurs', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 2: Historia - Media
        // ========================================
        $this->createQuestion(
            'history',
            Question::DIFFICULTY_MEDIUM,
            [
                'es' => '¿En qué año se celebró el primer Mundial de Fútbol?',
                'en' => 'In which year was the first Football World Cup held?',
                'fr' => 'En quelle année s\'est déroulée la première Coupe du Monde de football?',
            ],
            [
                ['es' => '1930', 'en' => '1930', 'fr' => '1930', 'is_correct' => true],
                ['es' => '1934', 'en' => '1934', 'fr' => '1934', 'is_correct' => false],
                ['es' => '1950', 'en' => '1950', 'fr' => '1950', 'is_correct' => false],
                ['es' => '1928', 'en' => '1928', 'fr' => '1928', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 3: Jugadores - Fácil
        // ========================================
        $this->createQuestion(
            'players',
            Question::DIFFICULTY_EASY,
            [
                'es' => '¿De qué país es Diego Maradona?',
                'en' => 'Which country is Diego Maradona from?',
                'fr' => 'De quel pays vient Diego Maradona?',
            ],
            [
                ['es' => 'Argentina', 'en' => 'Argentina', 'fr' => 'Argentine', 'is_correct' => true],
                ['es' => 'Brasil', 'en' => 'Brazil', 'fr' => 'Brésil', 'is_correct' => false],
                ['es' => 'Uruguay', 'en' => 'Uruguay', 'fr' => 'Uruguay', 'is_correct' => false],
                ['es' => 'España', 'en' => 'Spain', 'fr' => 'Espagne', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 4: Torneos - Media
        // ========================================
        $this->createQuestion(
            'tournaments',
            Question::DIFFICULTY_MEDIUM,
            [
                'es' => '¿Qué país ha ganado más Copas del Mundo?',
                'en' => 'Which country has won the most World Cups?',
                'fr' => 'Quel pays a remporté le plus de Coupes du Monde?',
            ],
            [
                ['es' => 'Brasil', 'en' => 'Brazil', 'fr' => 'Brésil', 'is_correct' => true],
                ['es' => 'Alemania', 'en' => 'Germany', 'fr' => 'Allemagne', 'is_correct' => false],
                ['es' => 'Italia', 'en' => 'Italy', 'fr' => 'Italie', 'is_correct' => false],
                ['es' => 'Argentina', 'en' => 'Argentina', 'fr' => 'Argentine', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 5: Clubes - Fácil
        // ========================================
        $this->createQuestion(
            'clubs',
            Question::DIFFICULTY_EASY,
            [
                'es' => '¿En qué ciudad juega el FC Barcelona?',
                'en' => 'In which city does FC Barcelona play?',
                'fr' => 'Dans quelle ville joue le FC Barcelone?',
            ],
            [
                ['es' => 'Barcelona', 'en' => 'Barcelona', 'fr' => 'Barcelone', 'is_correct' => true],
                ['es' => 'Madrid', 'en' => 'Madrid', 'fr' => 'Madrid', 'is_correct' => false],
                ['es' => 'Valencia', 'en' => 'Valencia', 'fr' => 'Valence', 'is_correct' => false],
                ['es' => 'Sevilla', 'en' => 'Seville', 'fr' => 'Séville', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 6: Reglas - Difícil
        // ========================================
        $this->createQuestion(
            'rules',
            Question::DIFFICULTY_HARD,
            [
                'es' => '¿Cuánto dura cada tiempo extra en un partido oficial?',
                'en' => 'How long does each extra time period last in an official match?',
                'fr' => 'Combien de temps dure chaque prolongation dans un match officiel?',
            ],
            [
                ['es' => '15 minutos', 'en' => '15 minutes', 'fr' => '15 minutes', 'is_correct' => true],
                ['es' => '10 minutos', 'en' => '10 minutes', 'fr' => '10 minutes', 'is_correct' => false],
                ['es' => '20 minutos', 'en' => '20 minutes', 'fr' => '20 minutes', 'is_correct' => false],
                ['es' => '30 minutos', 'en' => '30 minutes', 'fr' => '30 minutes', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 7: Historia - Difícil
        // ========================================
        $this->createQuestion(
            'history',
            Question::DIFFICULTY_HARD,
            [
                'es' => '¿Quién ganó el primer Balón de Oro de la historia?',
                'en' => 'Who won the first Ballon d\'Or in history?',
                'fr' => 'Qui a remporté le premier Ballon d\'Or de l\'histoire?',
            ],
            [
                ['es' => 'Stanley Matthews', 'en' => 'Stanley Matthews', 'fr' => 'Stanley Matthews', 'is_correct' => true],
                ['es' => 'Alfredo Di Stéfano', 'en' => 'Alfredo Di Stéfano', 'fr' => 'Alfredo Di Stéfano', 'is_correct' => false],
                ['es' => 'Raymond Kopa', 'en' => 'Raymond Kopa', 'fr' => 'Raymond Kopa', 'is_correct' => false],
                ['es' => 'Ferenc Puskás', 'en' => 'Ferenc Puskás', 'fr' => 'Ferenc Puskás', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 8: Jugadores - Media
        // ========================================
        $this->createQuestion(
            'players',
            Question::DIFFICULTY_MEDIUM,
            [
                'es' => '¿Cuántos Balones de Oro ha ganado Lionel Messi?',
                'en' => 'How many Ballon d\'Or awards has Lionel Messi won?',
                'fr' => 'Combien de Ballons d\'Or Lionel Messi a-t-il remportés?',
            ],
            [
                ['es' => '8', 'en' => '8', 'fr' => '8', 'is_correct' => true],
                ['es' => '7', 'en' => '7', 'fr' => '7', 'is_correct' => false],
                ['es' => '6', 'en' => '6', 'fr' => '6', 'is_correct' => false],
                ['es' => '5', 'en' => '5', 'fr' => '5', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 9: Torneos - Difícil
        // ========================================
        $this->createQuestion(
            'tournaments',
            Question::DIFFICULTY_HARD,
            [
                'es' => '¿En qué año Uruguay organizó y ganó el primer Mundial?',
                'en' => 'In which year did Uruguay host and win the first World Cup?',
                'fr' => 'En quelle année l\'Uruguay a-t-il organisé et remporté la première Coupe du Monde?',
            ],
            [
                ['es' => '1930', 'en' => '1930', 'fr' => '1930', 'is_correct' => true],
                ['es' => '1934', 'en' => '1934', 'fr' => '1934', 'is_correct' => false],
                ['es' => '1928', 'en' => '1928', 'fr' => '1928', 'is_correct' => false],
                ['es' => '1950', 'en' => '1950', 'fr' => '1950', 'is_correct' => false],
            ]
        );

        // ========================================
        // PREGUNTA 10: Clubes - Media
        // ========================================
        $this->createQuestion(
            'clubs',
            Question::DIFFICULTY_MEDIUM,
            [
                'es' => '¿Qué equipo tiene más Champions League ganadas?',
                'en' => 'Which team has won the most Champions League titles?',
                'fr' => 'Quelle équipe a remporté le plus de Ligue des Champions?',
            ],
            [
                ['es' => 'Real Madrid', 'en' => 'Real Madrid', 'fr' => 'Real Madrid', 'is_correct' => true],
                ['es' => 'AC Milan', 'en' => 'AC Milan', 'fr' => 'AC Milan', 'is_correct' => false],
                ['es' => 'Bayern Munich', 'en' => 'Bayern Munich', 'fr' => 'Bayern Munich', 'is_correct' => false],
                ['es' => 'Liverpool', 'en' => 'Liverpool', 'fr' => 'Liverpool', 'is_correct' => false],
            ]
        );

        $this->command->info('✅ 10 preguntas demo creadas con traducciones completas (ES/EN/FR)');
        $this->command->info('📊 Total: 10 preguntas × 3 idiomas × 4 opciones = 120 traducciones');
    }

    /**
     * Helper para crear pregunta completa con traducciones.
     */
    private function createQuestion(
        string $categoryCode,
        int $difficulty,
        array $questionTexts,
        array $options
    ): void {
        // Obtener categoría por código (sin locale)
        $category = QuizCategory::where('code', $categoryCode)->first();

        if (!$category) {
            $this->command->warn("⚠️  Categoría '{$categoryCode}' no encontrada");
            return;
        }

        // Crear pregunta
        $question = Question::create([
            'category_id' => $category->id,
            'difficulty' => $difficulty,
            'is_active' => true,
        ]);

        // Crear traducciones de la pregunta
        foreach ($questionTexts as $locale => $text) {
            QuestionTranslation::create([
                'question_id' => $question->id,
                'locale' => $locale,
                'text' => $text,
            ]);
        }

        // Crear opciones con sus traducciones
        foreach ($options as $index => $optionData) {
            $option = QuestionOption::create([
                'question_id' => $question->id,
                'is_correct' => $optionData['is_correct'],
                'order' => $index + 1,
            ]);

            // Crear traducciones de la opción
            foreach (['es', 'en', 'fr'] as $locale) {
                if (isset($optionData[$locale])) {
                    QuestionOptionTranslation::create([
                        'question_option_id' => $option->id,
                        'locale' => $locale,
                        'text' => $optionData[$locale],
                    ]);
                }
            }
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\QuizCategory;
use App\Models\Question;
use App\Models\QuestionTranslation;
use App\Models\QuestionOption;
use App\Models\QuestionOptionTranslation;
use Illuminate\Database\Seeder;

class QuizQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Crea 20 preguntas demo con traducciones completas (ES/EN/FR)
     * distribuidas en todas las categorías y dificultades.
     */
    public function run(): void
    {
        $this->command->info('❓ Creando preguntas demo del módulo educativo...');

        // Array de preguntas organizadas por categoría
        $questions = [
            // REGLAS DEL FÚTBOL (5 preguntas)
            [
                'category' => 'rules',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿Cuántos jugadores conforman un equipo de fútbol en el campo?',
                    'en' => 'How many players make up a football team on the field?',
                    'fr' => 'Combien de joueurs composent une équipe de football sur le terrain?',
                ],
                'options' => [
                    ['es' => '11 jugadores', 'en' => '11 players', 'fr' => '11 joueurs', 'correct' => true],
                    ['es' => '10 jugadores', 'en' => '10 players', 'fr' => '10 joueurs', 'correct' => false],
                    ['es' => '12 jugadores', 'en' => '12 players', 'fr' => '12 joueurs', 'correct' => false],
                    ['es' => '9 jugadores', 'en' => '9 players', 'fr' => '9 joueurs', 'correct' => false],
                ],
            ],
            [
                'category' => 'rules',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿Cuántos cambios se pueden hacer en un partido oficial de fútbol?',
                    'en' => 'How many substitutions can be made in an official football match?',
                    'fr' => 'Combien de remplacements peuvent être effectués dans un match de football officiel?',
                ],
                'options' => [
                    ['es' => '5 cambios', 'en' => '5 substitutions', 'fr' => '5 remplacements', 'correct' => true],
                    ['es' => '3 cambios', 'en' => '3 substitutions', 'fr' => '3 remplacements', 'correct' => false],
                    ['es' => '7 cambios', 'en' => '7 substitutions', 'fr' => '7 remplacements', 'correct' => false],
                    ['es' => '4 cambios', 'en' => '4 substitutions', 'fr' => '4 remplacements', 'correct' => false],
                ],
            ],
            [
                'category' => 'rules',
                'difficulty' => Question::DIFFICULTY_HARD,
                'question' => [
                    'es' => '¿Cuánto tiempo tiene el arquero para soltar el balón cuando lo tiene en sus manos?',
                    'en' => 'How much time does the goalkeeper have to release the ball when holding it?',
                    'fr' => 'Combien de temps le gardien a-t-il pour relâcher le ballon lorsqu\'il le tient?',
                ],
                'options' => [
                    ['es' => '6 segundos', 'en' => '6 seconds', 'fr' => '6 secondes', 'correct' => true],
                    ['es' => '10 segundos', 'en' => '10 seconds', 'fr' => '10 secondes', 'correct' => false],
                    ['es' => '8 segundos', 'en' => '8 seconds', 'fr' => '8 secondes', 'correct' => false],
                    ['es' => '5 segundos', 'en' => '5 seconds', 'fr' => '5 secondes', 'correct' => false],
                ],
            ],
            [
                'category' => 'rules',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿De qué color es la tarjeta que expulsa a un jugador?',
                    'en' => 'What color is the card that sends off a player?',
                    'fr' => 'De quelle couleur est le carton qui expulse un joueur?',
                ],
                'options' => [
                    ['es' => 'Roja', 'en' => 'Red', 'fr' => 'Rouge', 'correct' => true],
                    ['es' => 'Amarilla', 'en' => 'Yellow', 'fr' => 'Jaune', 'correct' => false],
                    ['es' => 'Verde', 'en' => 'Green', 'fr' => 'Verte', 'correct' => false],
                    ['es' => 'Azul', 'en' => 'Blue', 'fr' => 'Bleue', 'correct' => false],
                ],
            ],
            [
                'category' => 'rules',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿Cuánto dura cada tiempo en un partido de fútbol profesional?',
                    'en' => 'How long does each half last in a professional football match?',
                    'fr' => 'Combien de temps dure chaque mi-temps dans un match de football professionnel?',
                ],
                'options' => [
                    ['es' => '45 minutos', 'en' => '45 minutes', 'fr' => '45 minutes', 'correct' => true],
                    ['es' => '40 minutos', 'en' => '40 minutes', 'fr' => '40 minutes', 'correct' => false],
                    ['es' => '50 minutos', 'en' => '50 minutes', 'fr' => '50 minutes', 'correct' => false],
                    ['es' => '35 minutos', 'en' => '35 minutes', 'fr' => '35 minutes', 'correct' => false],
                ],
            ],

            // HISTORIA DEL FÚTBOL (5 preguntas)
            [
                'category' => 'history',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿En qué año se celebró el primer Mundial de Fútbol?',
                    'en' => 'In which year was the first Football World Cup held?',
                    'fr' => 'En quelle année s\'est déroulée la première Coupe du Monde de football?',
                ],
                'options' => [
                    ['es' => '1930', 'en' => '1930', 'fr' => '1930', 'correct' => true],
                    ['es' => '1934', 'en' => '1934', 'fr' => '1934', 'correct' => false],
                    ['es' => '1928', 'en' => '1928', 'fr' => '1928', 'correct' => false],
                    ['es' => '1950', 'en' => '1950', 'fr' => '1950', 'correct' => false],
                ],
            ],
            [
                'category' => 'history',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿Qué país organizó el primer Mundial de Fútbol?',
                    'en' => 'Which country hosted the first Football World Cup?',
                    'fr' => 'Quel pays a organisé la première Coupe du Monde de football?',
                ],
                'options' => [
                    ['es' => 'Uruguay', 'en' => 'Uruguay', 'fr' => 'Uruguay', 'correct' => true],
                    ['es' => 'Brasil', 'en' => 'Brazil', 'fr' => 'Brésil', 'correct' => false],
                    ['es' => 'Argentina', 'en' => 'Argentina', 'fr' => 'Argentine', 'correct' => false],
                    ['es' => 'Italia', 'en' => 'Italy', 'fr' => 'Italie', 'correct' => false],
                ],
            ],
            [
                'category' => 'history',
                'difficulty' => Question::DIFFICULTY_HARD,
                'question' => [
                    'es' => '¿En qué año se fundó la FIFA?',
                    'en' => 'In which year was FIFA founded?',
                    'fr' => 'En quelle année la FIFA a-t-elle été fondée?',
                ],
                'options' => [
                    ['es' => '1904', 'en' => '1904', 'fr' => '1904', 'correct' => true],
                    ['es' => '1910', 'en' => '1910', 'fr' => '1910', 'correct' => false],
                    ['es' => '1900', 'en' => '1900', 'fr' => '1900', 'correct' => false],
                    ['es' => '1898', 'en' => '1898', 'fr' => '1898', 'correct' => false],
                ],
            ],
            [
                'category' => 'history',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿Qué selección ha ganado más Copas del Mundo?',
                    'en' => 'Which national team has won the most World Cups?',
                    'fr' => 'Quelle équipe nationale a remporté le plus de Coupes du Monde?',
                ],
                'options' => [
                    ['es' => 'Brasil', 'en' => 'Brazil', 'fr' => 'Brésil', 'correct' => true],
                    ['es' => 'Alemania', 'en' => 'Germany', 'fr' => 'Allemagne', 'correct' => false],
                    ['es' => 'Italia', 'en' => 'Italy', 'fr' => 'Italie', 'correct' => false],
                    ['es' => 'Argentina', 'en' => 'Argentina', 'fr' => 'Argentine', 'correct' => false],
                ],
            ],
            [
                'category' => 'history',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿En qué Mundial se introdujo el VAR por primera vez?',
                    'en' => 'In which World Cup was VAR introduced for the first time?',
                    'fr' => 'Dans quelle Coupe du Monde le VAR a-t-il été introduit pour la première fois?',
                ],
                'options' => [
                    ['es' => 'Rusia 2018', 'en' => 'Russia 2018', 'fr' => 'Russie 2018', 'correct' => true],
                    ['es' => 'Brasil 2014', 'en' => 'Brazil 2014', 'fr' => 'Brésil 2014', 'correct' => false],
                    ['es' => 'Qatar 2022', 'en' => 'Qatar 2022', 'fr' => 'Qatar 2022', 'correct' => false],
                    ['es' => 'Sudáfrica 2010', 'en' => 'South Africa 2010', 'fr' => 'Afrique du Sud 2010', 'correct' => false],
                ],
            ],

            // JUGADORES LEGENDARIOS (5 preguntas)
            [
                'category' => 'players',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿De qué país es Diego Maradona?',
                    'en' => 'Which country is Diego Maradona from?',
                    'fr' => 'De quel pays vient Diego Maradona?',
                ],
                'options' => [
                    ['es' => 'Argentina', 'en' => 'Argentina', 'fr' => 'Argentine', 'correct' => true],
                    ['es' => 'Brasil', 'en' => 'Brazil', 'fr' => 'Brésil', 'correct' => false],
                    ['es' => 'Uruguay', 'en' => 'Uruguay', 'fr' => 'Uruguay', 'correct' => false],
                    ['es' => 'Chile', 'en' => 'Chile', 'fr' => 'Chili', 'correct' => false],
                ],
            ],
            [
                'category' => 'players',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿Cuántos Balones de Oro ha ganado Lionel Messi?',
                    'en' => 'How many Ballon d\'Or awards has Lionel Messi won?',
                    'fr' => 'Combien de Ballons d\'Or Lionel Messi a-t-il remportés?',
                ],
                'options' => [
                    ['es' => '8', 'en' => '8', 'fr' => '8', 'correct' => true],
                    ['es' => '7', 'en' => '7', 'fr' => '7', 'correct' => false],
                    ['es' => '6', 'en' => '6', 'fr' => '6', 'correct' => false],
                    ['es' => '5', 'en' => '5', 'fr' => '5', 'correct' => false],
                ],
            ],
            [
                'category' => 'players',
                'difficulty' => Question::DIFFICULTY_HARD,
                'question' => [
                    'es' => '¿Quién es el máximo goleador histórico de la Champions League?',
                    'en' => 'Who is the all-time top scorer in the Champions League?',
                    'fr' => 'Qui est le meilleur buteur de tous les temps en Ligue des Champions?',
                ],
                'options' => [
                    ['es' => 'Cristiano Ronaldo', 'en' => 'Cristiano Ronaldo', 'fr' => 'Cristiano Ronaldo', 'correct' => true],
                    ['es' => 'Lionel Messi', 'en' => 'Lionel Messi', 'fr' => 'Lionel Messi', 'correct' => false],
                    ['es' => 'Robert Lewandowski', 'en' => 'Robert Lewandowski', 'fr' => 'Robert Lewandowski', 'correct' => false],
                    ['es' => 'Karim Benzema', 'en' => 'Karim Benzema', 'fr' => 'Karim Benzema', 'correct' => false],
                ],
            ],
            [
                'category' => 'players',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿Qué jugador brasileño es conocido como "El Rey del Fútbol"?',
                    'en' => 'Which Brazilian player is known as "The King of Football"?',
                    'fr' => 'Quel joueur brésilien est connu comme "Le Roi du Football"?',
                ],
                'options' => [
                    ['es' => 'Pelé', 'en' => 'Pelé', 'fr' => 'Pelé', 'correct' => true],
                    ['es' => 'Ronaldinho', 'en' => 'Ronaldinho', 'fr' => 'Ronaldinho', 'correct' => false],
                    ['es' => 'Romário', 'en' => 'Romário', 'fr' => 'Romário', 'correct' => false],
                    ['es' => 'Ronaldo', 'en' => 'Ronaldo', 'fr' => 'Ronaldo', 'correct' => false],
                ],
            ],
            [
                'category' => 'players',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿En qué equipo jugó Zinedine Zidane antes de retirarse?',
                    'en' => 'Which team did Zinedine Zidane play for before retiring?',
                    'fr' => 'Dans quelle équipe Zinedine Zidane a-t-il joué avant de prendre sa retraite?',
                ],
                'options' => [
                    ['es' => 'Real Madrid', 'en' => 'Real Madrid', 'fr' => 'Real Madrid', 'correct' => true],
                    ['es' => 'Juventus', 'en' => 'Juventus', 'fr' => 'Juventus', 'correct' => false],
                    ['es' => 'Barcelona', 'en' => 'Barcelona', 'fr' => 'Barcelone', 'correct' => false],
                    ['es' => 'Bayern Múnich', 'en' => 'Bayern Munich', 'fr' => 'Bayern Munich', 'correct' => false],
                ],
            ],

            // TORNEOS Y COMPETICIONES (3 preguntas)
            [
                'category' => 'tournaments',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿Qué torneo se juega entre los mejores clubes de Europa?',
                    'en' => 'Which tournament is played between the best clubs in Europe?',
                    'fr' => 'Quel tournoi est disputé entre les meilleurs clubs d\'Europe?',
                ],
                'options' => [
                    ['es' => 'UEFA Champions League', 'en' => 'UEFA Champions League', 'fr' => 'UEFA Champions League', 'correct' => true],
                    ['es' => 'Copa Libertadores', 'en' => 'Copa Libertadores', 'fr' => 'Copa Libertadores', 'correct' => false],
                    ['es' => 'Copa del Mundo', 'en' => 'World Cup', 'fr' => 'Coupe du Monde', 'correct' => false],
                    ['es' => 'Premier League', 'en' => 'Premier League', 'fr' => 'Premier League', 'correct' => false],
                ],
            ],
            [
                'category' => 'tournaments',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿Cada cuántos años se celebra la Copa del Mundo de la FIFA?',
                    'en' => 'How often is the FIFA World Cup held?',
                    'fr' => 'Tous les combien d\'années se tient la Coupe du Monde de la FIFA?',
                ],
                'options' => [
                    ['es' => 'Cada 4 años', 'en' => 'Every 4 years', 'fr' => 'Tous les 4 ans', 'correct' => true],
                    ['es' => 'Cada 2 años', 'en' => 'Every 2 years', 'fr' => 'Tous les 2 ans', 'correct' => false],
                    ['es' => 'Cada 3 años', 'en' => 'Every 3 years', 'fr' => 'Tous les 3 ans', 'correct' => false],
                    ['es' => 'Cada 5 años', 'en' => 'Every 5 years', 'fr' => 'Tous les 5 ans', 'correct' => false],
                ],
            ],
            [
                'category' => 'tournaments',
                'difficulty' => Question::DIFFICULTY_HARD,
                'question' => [
                    'es' => '¿Qué club ha ganado más UEFA Champions League?',
                    'en' => 'Which club has won the most UEFA Champions League titles?',
                    'fr' => 'Quel club a remporté le plus de titres de l\'UEFA Champions League?',
                ],
                'options' => [
                    ['es' => 'Real Madrid', 'en' => 'Real Madrid', 'fr' => 'Real Madrid', 'correct' => true],
                    ['es' => 'AC Milan', 'en' => 'AC Milan', 'fr' => 'AC Milan', 'correct' => false],
                    ['es' => 'Bayern Múnich', 'en' => 'Bayern Munich', 'fr' => 'Bayern Munich', 'correct' => false],
                    ['es' => 'Liverpool', 'en' => 'Liverpool', 'fr' => 'Liverpool', 'correct' => false],
                ],
            ],

            // CLUBES HISTÓRICOS (2 preguntas)
            [
                'category' => 'clubs',
                'difficulty' => Question::DIFFICULTY_EASY,
                'question' => [
                    'es' => '¿En qué ciudad juega el FC Barcelona?',
                    'en' => 'In which city does FC Barcelona play?',
                    'fr' => 'Dans quelle ville joue le FC Barcelone?',
                ],
                'options' => [
                    ['es' => 'Barcelona', 'en' => 'Barcelona', 'fr' => 'Barcelone', 'correct' => true],
                    ['es' => 'Madrid', 'en' => 'Madrid', 'fr' => 'Madrid', 'correct' => false],
                    ['es' => 'Valencia', 'en' => 'Valencia', 'fr' => 'Valence', 'correct' => false],
                    ['es' => 'Sevilla', 'en' => 'Seville', 'fr' => 'Séville', 'correct' => false],
                ],
            ],
            [
                'category' => 'clubs',
                'difficulty' => Question::DIFFICULTY_MEDIUM,
                'question' => [
                    'es' => '¿Cuál es el estadio del Manchester United?',
                    'en' => 'What is Manchester United\'s stadium?',
                    'fr' => 'Quel est le stade de Manchester United?',
                ],
                'options' => [
                    ['es' => 'Old Trafford', 'en' => 'Old Trafford', 'fr' => 'Old Trafford', 'correct' => true],
                    ['es' => 'Anfield', 'en' => 'Anfield', 'fr' => 'Anfield', 'correct' => false],
                    ['es' => 'Stamford Bridge', 'en' => 'Stamford Bridge', 'fr' => 'Stamford Bridge', 'correct' => false],
                    ['es' => 'Emirates Stadium', 'en' => 'Emirates Stadium', 'fr' => 'Emirates Stadium', 'correct' => false],
                ],
            ],
        ];

        // Crear preguntas
        $created = 0;
        foreach ($questions as $questionData) {
            $category = QuizCategory::where('code', $questionData['category'])->first();
            
            if (!$category) {
                $this->command->warn("⚠️  Categoría '{$questionData['category']}' no encontrada");
                continue;
            }

            // Crear pregunta
            $question = Question::create([
                'category_id' => $category->id,
                'difficulty' => $questionData['difficulty'],
                'is_active' => true,
            ]);

            // Crear traducciones de la pregunta
            foreach ($questionData['question'] as $locale => $text) {
                QuestionTranslation::create([
                    'question_id' => $question->id,
                    'locale' => $locale,
                    'text' => $text,
                ]);
            }

            // Crear opciones con traducciones
            foreach ($questionData['options'] as $index => $optionData) {
                $option = QuestionOption::create([
                    'question_id' => $question->id,
                    'is_correct' => $optionData['correct'],
                    'order' => $index + 1,
                ]);

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

            $created++;
        }

        $this->command->newLine();
        $this->command->info("✅ {$created} preguntas creadas exitosamente");
        $this->command->info('📊 Distribución:');
        $this->command->line('  - Reglas: 5 preguntas');
        $this->command->line('  - Historia: 5 preguntas');
        $this->command->line('  - Jugadores: 5 preguntas');
        $this->command->line('  - Torneos: 3 preguntas');
        $this->command->line('  - Clubes: 2 preguntas');
    }
}
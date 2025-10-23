<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class QuizSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Crea todas las configuraciones necesarias para el módulo educativo.
     */
    public function run(): void
    {
        $this->command->info('📚 Creando settings del módulo educativo...');

        // ========================================
        // PUNTUACIÓN
        // ========================================
        $this->createSetting([
            'key' => 'quiz.points.easy',
            'value' => '10',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Puntos por pregunta fácil correcta',
            'description' => 'Puntos que recibe el usuario al responder correctamente una pregunta de dificultad fácil',
            'default_value' => '10',
            'validation_rules' => 'required|integer|min:1|max:100',
            'sort_order' => 1,
        ]);

        $this->createSetting([
            'key' => 'quiz.points.medium',
            'value' => '20',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Puntos por pregunta media correcta',
            'description' => 'Puntos que recibe el usuario al responder correctamente una pregunta de dificultad media',
            'default_value' => '20',
            'validation_rules' => 'required|integer|min:1|max:100',
            'sort_order' => 2,
        ]);

        $this->createSetting([
            'key' => 'quiz.points.hard',
            'value' => '30',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Puntos por pregunta difícil correcta',
            'description' => 'Puntos que recibe el usuario al responder correctamente una pregunta de dificultad difícil',
            'default_value' => '30',
            'validation_rules' => 'required|integer|min:1|max:100',
            'sort_order' => 3,
        ]);

        $this->createSetting([
            'key' => 'quiz.points.speed_bonus',
            'value' => '5',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Bonus por velocidad',
            'description' => 'Puntos adicionales por responder rápido (menos de 10 segundos)',
            'default_value' => '5',
            'validation_rules' => 'required|integer|min:0|max:50',
            'sort_order' => 4,
        ]);

        $this->createSetting([
            'key' => 'quiz.points.speed_threshold',
            'value' => '10',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Umbral de velocidad (segundos)',
            'description' => 'Tiempo máximo en segundos para recibir el bonus por velocidad',
            'default_value' => '10',
            'validation_rules' => 'required|integer|min:1|max:30',
            'sort_order' => 5,
        ]);

        $this->createSetting([
            'key' => 'quiz.points.streak_bonus',
            'value' => '10',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Bonus por racha',
            'description' => 'Puntos adicionales al alcanzar una racha de respuestas correctas',
            'default_value' => '10',
            'validation_rules' => 'required|integer|min:0|max:100',
            'sort_order' => 6,
        ]);

        $this->createSetting([
            'key' => 'quiz.points.streak_threshold',
            'value' => '5',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Umbral de racha',
            'description' => 'Cantidad de respuestas correctas seguidas necesarias para recibir el bonus',
            'default_value' => '5',
            'validation_rules' => 'required|integer|min:1|max:20',
            'sort_order' => 7,
        ]);

        // ========================================
        // CONVERSIÓN A MONEDAS VIRTUALES
        // ========================================
        $this->createSetting([
            'key' => 'quiz.coins.conversion_rate',
            'value' => '0.1',
            'group' => 'quiz',
            'type' => 'string',
            'label' => 'Ratio de conversión puntos → monedas',
            'description' => 'Ratio para convertir puntos educativos a monedas virtuales (ej: 0.1 = 10 puntos = 1 moneda)',
            'default_value' => '0.1',
            'validation_rules' => 'required|numeric|min:0|max:10',
            'sort_order' => 10,
        ]);

        $this->createSetting([
            'key' => 'quiz.coins.award_on_completion',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Otorgar monedas al completar',
            'description' => 'Si se deben otorgar monedas automáticamente al completar un quiz',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 11,
        ]);

        // ========================================
        // COOLDOWN Y LÍMITES DE INTENTOS
        // ========================================
        $this->createSetting([
            'key' => 'quiz.attempt.cooldown_minutes',
            'value' => '5',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Cooldown entre intentos (minutos)',
            'description' => 'Tiempo mínimo que debe esperar un usuario entre intentos del mismo quiz',
            'default_value' => '5',
            'validation_rules' => 'required|integer|min:0|max:1440',
            'sort_order' => 20,
        ]);

        $this->createSetting([
            'key' => 'quiz.attempt.max_per_day',
            'value' => '20',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Máximo de intentos por día',
            'description' => 'Cantidad máxima de quizzes que un usuario puede completar por día (0 = sin límite)',
            'default_value' => '20',
            'validation_rules' => 'required|integer|min:0|max:100',
            'sort_order' => 21,
        ]);

        $this->createSetting([
            'key' => 'quiz.attempt.max_retries_same_quiz',
            'value' => '3',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Máximo de reintentos del mismo quiz',
            'description' => 'Cantidad máxima de veces que un usuario puede intentar el mismo quiz (0 = sin límite)',
            'default_value' => '3',
            'validation_rules' => 'required|integer|min:0|max:10',
            'sort_order' => 22,
        ]);

        // ========================================
        // CONFIGURACIÓN DE TIEMPO
        // ========================================
        $this->createSetting([
            'key' => 'quiz.timer.default_seconds',
            'value' => '30',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Tiempo por pregunta (segundos)',
            'description' => 'Tiempo por defecto que tiene el usuario para responder cada pregunta',
            'default_value' => '30',
            'validation_rules' => 'required|integer|min:10|max:300',
            'sort_order' => 30,
        ]);

        $this->createSetting([
            'key' => 'quiz.timer.enabled',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Habilitar cronómetro',
            'description' => 'Si está habilitado, las preguntas tendrán un límite de tiempo',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 31,
        ]);

        $this->createSetting([
            'key' => 'quiz.timer.show_countdown',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Mostrar cuenta regresiva',
            'description' => 'Si se debe mostrar el contador de tiempo al usuario',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 32,
        ]);

        // ========================================
        // QUICK QUIZ
        // ========================================
        $this->createSetting([
            'key' => 'quiz.quick.questions_count',
            'value' => '10',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Cantidad de preguntas en Quick Quiz',
            'description' => 'Número de preguntas aleatorias que se mostrarán en el modo Quick Quiz',
            'default_value' => '10',
            'validation_rules' => 'required|integer|min:5|max:50',
            'sort_order' => 40,
        ]);

        $this->createSetting([
            'key' => 'quiz.quick.allow_category_selection',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Permitir selección de categoría',
            'description' => 'Si el usuario puede elegir una categoría específica para Quick Quiz',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 41,
        ]);

        $this->createSetting([
            'key' => 'quiz.quick.allow_difficulty_selection',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Permitir selección de dificultad',
            'description' => 'Si el usuario puede elegir el nivel de dificultad para Quick Quiz',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 42,
        ]);

        // ========================================
        // RANKING Y LEADERBOARD
        // ========================================
        $this->createSetting([
            'key' => 'quiz.leaderboard.enabled',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Habilitar ranking educativo',
            'description' => 'Si está habilitado, se mostrará el ranking de usuarios por puntos educativos',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 50,
        ]);

        $this->createSetting([
            'key' => 'quiz.leaderboard.top_users',
            'value' => '100',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Usuarios en el ranking',
            'description' => 'Cantidad de usuarios que se mostrarán en el ranking público',
            'default_value' => '100',
            'validation_rules' => 'required|integer|min:10|max:1000',
            'sort_order' => 51,
        ]);

        $this->createSetting([
            'key' => 'quiz.leaderboard.cache_minutes',
            'value' => '10',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Cache del ranking (minutos)',
            'description' => 'Tiempo en minutos que se cachea el ranking para mejorar rendimiento',
            'default_value' => '10',
            'validation_rules' => 'required|integer|min:1|max:60',
            'sort_order' => 52,
        ]);

        // ========================================
        // FEEDBACK Y UX
        // ========================================
        $this->createSetting([
            'key' => 'quiz.feedback.show_correct_answer',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Mostrar respuesta correcta',
            'description' => 'Si se debe mostrar la respuesta correcta después de responder',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 60,
        ]);

        $this->createSetting([
            'key' => 'quiz.feedback.show_explanation',
            'value' => 'false',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Mostrar explicación',
            'description' => 'Si se debe mostrar una explicación de la respuesta correcta (requiere campo en preguntas)',
            'default_value' => 'false',
            'validation_rules' => 'required|boolean',
            'sort_order' => 61,
        ]);

        $this->createSetting([
            'key' => 'quiz.feedback.delay_seconds',
            'value' => '2',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Delay del feedback (segundos)',
            'description' => 'Tiempo que se muestra el feedback antes de pasar a la siguiente pregunta',
            'default_value' => '2',
            'validation_rules' => 'required|integer|min:0|max:10',
            'sort_order' => 62,
        ]);

        // ========================================
        // OPCIONES Y PRESENTACIÓN
        // ========================================
        $this->createSetting([
            'key' => 'quiz.options.shuffle',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Mezclar opciones',
            'description' => 'Si se deben mezclar las opciones de respuesta en cada pregunta',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'sort_order' => 70,
        ]);

        $this->createSetting([
            'key' => 'quiz.options.min_options',
            'value' => '2',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Mínimo de opciones',
            'description' => 'Cantidad mínima de opciones de respuesta por pregunta',
            'default_value' => '2',
            'validation_rules' => 'required|integer|min:2|max:6',
            'sort_order' => 71,
        ]);

        $this->createSetting([
            'key' => 'quiz.options.max_options',
            'value' => '6',
            'group' => 'quiz',
            'type' => 'integer',
            'label' => 'Máximo de opciones',
            'description' => 'Cantidad máxima de opciones de respuesta por pregunta',
            'default_value' => '6',
            'validation_rules' => 'required|integer|min:2|max:10',
            'sort_order' => 72,
        ]);

        // ========================================
        // MÓDULO HABILITADO
        // ========================================
        $this->createSetting([
            'key' => 'quiz.module.enabled',
            'value' => 'true',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Módulo educativo habilitado',
            'description' => 'Si está habilitado, los usuarios podrán acceder al módulo educativo',
            'default_value' => 'true',
            'validation_rules' => 'required|boolean',
            'is_editable' => true,
            'sort_order' => 1,
        ]);

        $this->createSetting([
            'key' => 'quiz.module.maintenance_mode',
            'value' => 'false',
            'group' => 'quiz',
            'type' => 'boolean',
            'label' => 'Modo mantenimiento',
            'description' => 'Poner el módulo educativo en modo mantenimiento (solo admins pueden acceder)',
            'default_value' => 'false',
            'validation_rules' => 'required|boolean',
            'sort_order' => 2,
        ]);

        $this->command->info('✅ Settings del módulo educativo creados');
        $this->command->info('📊 Total: ' . Setting::where('group', 'quiz')->count() . ' configuraciones');
    }

    /**
     * Helper para crear un setting.
     */
    private function createSetting(array $data): void
    {
        Setting::updateOrCreate(
            ['key' => $data['key']],
            array_merge([
                'is_active' => true,
                'is_editable' => true,
            ], $data)
        );
    }
}

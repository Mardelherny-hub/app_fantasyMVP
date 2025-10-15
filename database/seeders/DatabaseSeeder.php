<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeders...');
        $this->command->newLine();

        // ========================================
        // GRUPO 1: FUNDACIÓN
        // ========================================
        $this->command->info('📋 GRUPO 1: Roles, Permisos y Usuarios');
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(AdminSeeder::class);
        $this->command->newLine();

        $this->command->info('⚽ GRUPO 1: Liga, Equipos y Miembros');
        $this->call(LeagueMembersSeeder::class);
        $this->call(FantasyTeamsSeeder::class);
        $this->command->newLine();

        // ========================================
        // GRUPO 2: REFERENCIALES
        // ========================================
        $this->command->info('📅 GRUPO 2: Temporadas y Configuración');
        $this->call(SeasonsSeeder::class);
        $this->call(GameweeksSeeder::class);
        $this->call(ScoringRulesSeeder::class);
        $this->command->newLine();

        // ========================================
        // GRUPO 3: ECONOMIA
        // ========================================
        $this->command->info('💰 GRUPO 3: Sistema de Economía');
        $this->call(RewardsSeeder::class);
        $this->command->newLine();

        // ========================================
        // GRUPO 4: TRIVIA
        // ========================================
        $this->command->info('📚 GRUPO 4: Sistema Educativo');
        $this->call(QuizCategoriesSeeder::class);
        $this->call(DemoQuestionsSeeder::class);
        $this->command->newLine();

        // ========================================
        // OPCIONAL: DATOS DEMO CON FACTORIES
        // ========================================
        if ($this->command->confirm('¿Generar datos de prueba (jugadores, equipos, partidos)?', false)) {
            $this->command->info('🎲 GRUPO 5: Generando datos de prueba...');
            // Aquí irán las llamadas a factories cuando las creemos
            $this->command->warn('⚠️  Factories pendientes de implementar');
        }

        $this->command->newLine();
        $this->command->info('✅ Seeders básicos completados exitosamente');
        $this->command->newLine();

        // ========================================
        // NOTA: SEEDERS DE API (Ejecutar manualmente)
        // ========================================
        $this->command->info('📢 IMPORTANTE: Para cargar datos reales de la API de LiveScore:');
        $this->command->warn('   Ejecuta estos comandos MANUALMENTE después del migrate:fresh --seed:');
        $this->command->newLine();
        $this->command->line('   php artisan db:seed --class=CanadianCompetitionsSeeder');
        $this->command->line('   php artisan db:seed --class=CanadianTeamsSeeder');
        $this->command->line('   php artisan db:seed --class=CanadianPlayersSeeder');
        $this->command->line('   php artisan db:seed --class=CanadianFixturesSeeder');
        $this->command->line('   php artisan db:seed --class=CanadianStandingsSeeder');
        $this->command->newLine();

        // Resumen
        $this->command->table(
            ['Recurso', 'Estado'],
            [
                ['Roles y Permisos', '✅ Creados'],
                ['Usuarios (Admin, Manager, Operator, Users)', '✅ Creados'],
                ['Temporadas', '✅ 3 temporadas'],
                ['Gameweeks', '✅ 30 (27 + 3 playoffs)'],
                ['Scoring Rules', '✅ 16 reglas'],
                ['Quiz Categories', '✅ 15 categorías (3 idiomas)'],
                ['Recompensas (Rewards)', '✅ 16 tipos'],
                ['Preguntas Demo', '✅ 10 preguntas (3 idiomas)'],
                ['⚠️  Datos de API', '⏸️  Ejecutar manualmente'],
            ]
        );
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class QuizPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Crea todos los permisos específicos del módulo educativo
     * y los asigna a los roles correspondientes.
     */
    public function run(): void
    {
        $this->command->info('🔐 Creando permisos del módulo educativo...');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // PERMISOS DEL MÓDULO EDUCATIVO
        // ========================================

        $quizPermissions = [
            // VISTA GENERAL
            'quiz.view' => 'Ver módulo educativo',
            'quiz.play' => 'Jugar quizzes',
            
            // GESTIÓN DE PREGUNTAS
            'quiz.questions.view' => 'Ver preguntas',
            'quiz.questions.create' => 'Crear preguntas',
            'quiz.questions.edit' => 'Editar preguntas',
            'quiz.questions.delete' => 'Eliminar preguntas',
            'quiz.questions.activate' => 'Activar/Desactivar preguntas',
            'quiz.questions.import' => 'Importar preguntas masivamente',
            'quiz.questions.export' => 'Exportar preguntas',
            
            // GESTIÓN DE CATEGORÍAS
            'quiz.categories.view' => 'Ver categorías de quiz',
            'quiz.categories.create' => 'Crear categorías',
            'quiz.categories.edit' => 'Editar categorías',
            'quiz.categories.delete' => 'Eliminar categorías',
            
            // GESTIÓN DE QUIZZES (temáticos)
            'quiz.quizzes.view' => 'Ver quizzes temáticos',
            'quiz.quizzes.create' => 'Crear quizzes',
            'quiz.quizzes.edit' => 'Editar quizzes',
            'quiz.quizzes.delete' => 'Eliminar quizzes',
            'quiz.quizzes.publish' => 'Publicar quizzes',
            
            // INTENTOS Y RESPUESTAS
            'quiz.attempts.view_own' => 'Ver mis intentos',
            'quiz.attempts.view_all' => 'Ver todos los intentos (admin)',
            'quiz.attempts.delete' => 'Eliminar intentos (moderación)',
            
            // REPORTES Y ESTADÍSTICAS
            'quiz.reports.view' => 'Ver reportes educativos',
            'quiz.reports.export' => 'Exportar reportes',
            'quiz.reports.analytics' => 'Ver analíticas avanzadas',
            
            // CONFIGURACIÓN
            'quiz.settings.view' => 'Ver configuración del módulo',
            'quiz.settings.edit' => 'Editar configuración del módulo',
            
            // GESTIÓN COMPLETA (super permiso)
            'quiz.manage' => 'Gestión completa del módulo educativo',
        ];

        // Crear permisos
        foreach ($quizPermissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name],
                ['guard_name' => 'web']
            );
            $this->command->line("  ✓ Permiso creado: {$name}");
        }

        $this->command->newLine();
        $this->command->info('📋 Asignando permisos a roles...');

        // ========================================
        // ASIGNACIÓN DE PERMISOS POR ROL
        // ========================================

        // ROLE: ADMIN (acceso total)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'quiz.view',
            'quiz.play',
            'quiz.questions.view',
            'quiz.questions.create',
            'quiz.questions.edit',
            'quiz.questions.delete',
            'quiz.questions.activate',
            'quiz.questions.import',
            'quiz.questions.export',
            'quiz.categories.view',
            'quiz.categories.create',
            'quiz.categories.edit',
            'quiz.categories.delete',
            'quiz.quizzes.view',
            'quiz.quizzes.create',
            'quiz.quizzes.edit',
            'quiz.quizzes.delete',
            'quiz.quizzes.publish',
            'quiz.attempts.view_own',
            'quiz.attempts.view_all',
            'quiz.attempts.delete',
            'quiz.reports.view',
            'quiz.reports.export',
            'quiz.reports.analytics',
            'quiz.settings.view',
            'quiz.settings.edit',
            'quiz.manage',
        ]);
        $this->command->line("  ✓ Permisos asignados a: ADMIN");

        // ROLE: OPERATOR (gestión de contenido educativo)
        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->givePermissionTo([
            'quiz.view',
            'quiz.play',
            'quiz.questions.view',
            'quiz.questions.create',
            'quiz.questions.edit',
            'quiz.questions.activate',
            'quiz.questions.import',
            'quiz.categories.view',
            'quiz.categories.create',
            'quiz.categories.edit',
            'quiz.quizzes.view',
            'quiz.quizzes.create',
            'quiz.quizzes.edit',
            'quiz.attempts.view_own',
            'quiz.reports.view',
        ]);
        $this->command->line("  ✓ Permisos asignados a: OPERATOR");

        // ROLE: MANAGER (solo puede jugar y ver reportes propios)
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'quiz.view',
            'quiz.play',
            'quiz.attempts.view_own',
        ]);
        $this->command->line("  ✓ Permisos asignados a: MANAGER");

        // ROLE: USER (jugar quizzes)
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            'quiz.view',
            'quiz.play',
            'quiz.attempts.view_own',
        ]);
        $this->command->line("  ✓ Permisos asignados a: USER");

        $this->command->newLine();
        $this->command->info('✅ Permisos del módulo educativo creados y asignados');
        $this->command->info('📊 Total de permisos: ' . count($quizPermissions));
    }
}

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

        // 1. Roles y Permisos (PRIMERO)
        $this->command->info('📋 Creando roles y permisos...');
        $this->call(RolesAndPermissionsSeeder::class);
        $this->command->newLine();

        // 2. Usuarios (SEGUNDO - depende de roles)
        $this->command->info('👥 Creando usuarios...');
        $this->call(AdminSeeder::class);
        $this->command->newLine();

        // 3. Temporadas 👈 NUEVO
        $this->command->info('🏆 Creando temporadas...');
        $this->call(SeasonsSeeder::class);
        $this->command->newLine();

        // 3. Aquí irán los demás seeders cuando los creemos
        // $this->call(SeasonsSeeder::class);
        // $this->call(RealTeamsSeeder::class);
        // $this->call(PlayersSeeder::class);
        // etc...

        $this->command->newLine();
        $this->command->info('✅ Seeders completados exitosamente');
    }
}
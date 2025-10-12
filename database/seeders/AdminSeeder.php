<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // USUARIO ADMINISTRADOR
        // ========================================
        $admin = User::firstOrCreate(
            ['email' => 'admin@fantasy.local'],
            [
                'name' => 'Administrador',
                'username' => 'admin',
                'password' => bcrypt('12345678'),
                'locale' => 'es',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');
        $this->command->info("✅ Admin creado: admin@fantasy.local / 12345678");

        // ========================================
        // USUARIO MANAGER
        // ========================================
        for ($i = 1; $i <= 50; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@fantasy.local"],
                [
                    'name' => "Usuario Manager {$i}",
                    'username' => "user{$i}",
                    'password' => bcrypt('12345678'),
                    'locale' => 'es',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('manager');
        }
        $this->command->info("✅ 50 managers de prueba creados: user1@fantasy.local ... user5@fantasy.local / 12345678");

        // ========================================
        // USUARIO OPERATOR
        // ========================================
        $operator = User::firstOrCreate(
            ['email' => 'operator@fantasy.local'],
            [
                'name' => 'Operador de Datos',
                'username' => 'operator',
                'password' => bcrypt('12345678'),
                'locale' => 'es',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $operator->assignRole('operator');
        $this->command->info("✅ Operator creado: operator@fantasy.local / 12345678");

        // ========================================
        // USUARIOS DEMO (para pruebas)
        // ========================================
        
            $user = User::firstOrCreate(
                ['email' => "user{$i}@fantasy.local"],
                [
                    'name' => "Usuario Demo {$i}",
                    'username' => "user{$i}",
                    'password' => bcrypt('12345678'),
                    'locale' => 'es',
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
            $user->assignRole('user');

        $this->command->info("✅ 1 usuario demo creado: user1@fantasy.local / 12345678");
    }
}
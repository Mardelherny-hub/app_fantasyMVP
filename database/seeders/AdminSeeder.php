<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Crear rol admin si no existe
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@fantasy.local'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('12345678'),
            ]
        );

        // Asignar rol
        $admin->assignRole($adminRole);
    }
}

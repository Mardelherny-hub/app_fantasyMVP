<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // DEFINICIÓN DE PERMISOS POR MÓDULO
        // ========================================

        $permissions = [
            // USERS MODULE
            'users' => [
                'view_users',
                'create_users',
                'edit_users',
                'delete_users',
                'activate_users',
                'deactivate_users',
            ],

            // LEAGUES MODULE
            'leagues' => [
                'view_leagues',
                'create_leagues',
                'edit_leagues',
                'delete_leagues',
                'manage_league_settings',
                'close_league_registration',
            ],

            // TEAMS MODULE
            'teams' => [
                'view_teams',
                'create_teams',
                'edit_teams',
                'delete_teams',
                'view_any_team', // Ver equipos de otros
            ],

            // PLAYERS MODULE
            'players' => [
                'view_players',
                'import_players',
                'edit_players',
                'delete_players',
                'manage_player_valuations',
            ],

            // GAMEWEEKS MODULE
            'gameweeks' => [
                'view_gameweeks',
                'create_gameweeks',
                'open_gameweek',
                'close_gameweek',
                'manage_gameweek_deadlines',
            ],

            // SCORING MODULE
            'scoring' => [
                'view_scores',
                'import_scores',
                'edit_scores',
                'manage_scoring_rules',
                'calculate_fantasy_points',
            ],

            // MARKET MODULE
            'market' => [
                'view_market',
                'create_listing',
                'make_offer',
                'accept_offer',
                'manage_transfers',
                'manage_loans',
                'configure_market_settings',
            ],

            // TRIVIA MODULE
            'trivia' => [
                'view_trivia',
                'play_trivia',
                'create_questions',
                'edit_questions',
                'delete_questions',
                'manage_quiz_categories',
                'view_trivia_stats',
            ],

            // REWARDS MODULE
            'rewards' => [
                'view_rewards',
                'create_rewards',
                'award_rewards',
                'manage_wallets',
            ],

            // REPORTS MODULE
            'reports' => [
                'view_reports',
                'view_audit_logs',
                'export_data',
            ],
        ];

        // Crear todos los permisos
        foreach ($permissions as $module => $modulePermissions) {
            foreach ($modulePermissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }
        }

        // ========================================
        // DEFINICIÓN DE ROLES Y ASIGNACIÓN
        // ========================================

        // ROLE: ADMIN (Control total)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // ROLE: MANAGER (Gestión de ligas y operaciones)
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            // Ligas
            'view_leagues',
            'create_leagues',
            'edit_leagues',
            'manage_league_settings',
            'close_league_registration',
            
            // Teams
            'view_teams',
            'view_any_team',
            
            // Players
            'view_players',
            'import_players',
            'manage_player_valuations',
            
            // Gameweeks
            'view_gameweeks',
            'create_gameweeks',
            'open_gameweek',
            'close_gameweek',
            'manage_gameweek_deadlines',
            
            // Scoring
            'view_scores',
            'import_scores',
            'manage_scoring_rules',
            'calculate_fantasy_points',
            
            // Market
            'view_market',
            'manage_transfers',
            'manage_loans',
            'configure_market_settings',
            
            // Trivia
            'view_trivia',
            'create_questions',
            'edit_questions',
            'manage_quiz_categories',
            'view_trivia_stats',
            
            // Reports
            'view_reports',
            'export_data',
        ]);

        // ROLE: OPERATOR (Actualización de datos)
        $operator = Role::firstOrCreate(['name' => 'operator']);
        $operator->givePermissionTo([
            // Players
            'view_players',
            'import_players',
            'edit_players',
            
            // Scoring
            'view_scores',
            'import_scores',
            'edit_scores',
            
            // Gameweeks
            'view_gameweeks',
            
            // Trivia
            'view_trivia',
            'create_questions',
            'edit_questions',
        ]);

        // ROLE: USER (Usuario estándar)
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([
            // Leagues
            'view_leagues',
            
            // Teams
            'view_teams',
            'create_teams',
            'edit_teams',
            
            // Players
            'view_players',
            
            // Gameweeks
            'view_gameweeks',
            
            // Scoring
            'view_scores',
            
            // Market
            'view_market',
            'create_listing',
            'make_offer',
            'accept_offer',
            
            // Trivia
            'view_trivia',
            'play_trivia',
            
            // Rewards
            'view_rewards',
        ]);

        $this->command->info('✅ Roles y permisos creados correctamente');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\League;
use App\Models\User;
use App\Models\LeagueMember;
use App\Models\FantasyTeam;
use Illuminate\Support\Str;

class LeagueMembersSeeder extends Seeder
{
    public function run(): void
    {
        // Para cada liga existente que tenga fantasy teams sin league_members
        $leagues = League::all();

        foreach ($leagues as $league) {
            // Obtener fantasy teams de la liga que tienen user_id
            $fantasyTeams = $league->fantasyTeams()->whereNotNull('user_id')->get();

            foreach ($fantasyTeams as $team) {
                // Verificar si ya existe el league_member
                if (!$league->members()->where('user_id', $team->user_id)->exists()) {
                    // Crear league_member
                    $league->members()->create([
                        'user_id' => $team->user_id,
                        'role' => $team->user_id === $league->owner_user_id 
                            ? LeagueMember::ROLE_MANAGER 
                            : LeagueMember::ROLE_PARTICIPANT,
                        'is_active' => true,
                    ]);
                }
            }
        }

        $this->command->info('League members sincronizados correctamente.');
    }
}
<?php

namespace App\Console\Commands;

use App\Models\League;
use App\Models\Gameweek;
use App\Services\Admin\Standings\StandingsUpdateService;
use Illuminate\Console\Command;

class UpdateStandings extends Command
{
    protected $signature = 'standings:update {league_id} {gameweek_id}';
    protected $description = 'Actualizar standings de una liga para un gameweek específico';

    public function __construct(
        protected StandingsUpdateService $standingsService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $leagueId = $this->argument('league_id');
        $gameweekId = $this->argument('gameweek_id');

        $league = League::find($leagueId);
        if (!$league) {
            $this->error("Liga {$leagueId} no encontrada");
            return self::FAILURE;
        }

        $gameweek = Gameweek::find($gameweekId);
        if (!$gameweek) {
            $this->error("Gameweek {$gameweekId} no encontrado");
            return self::FAILURE;
        }

        $this->info("Actualizando standings...");
        
        try {
            $this->standingsService->updateStandings($league, $gameweek);
            $this->info("✅ Standings actualizados correctamente");
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
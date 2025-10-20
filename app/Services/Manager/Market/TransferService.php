<?php

namespace App\Services\Manager\Market;

use App\Models\FantasyTeam;
use App\Models\Player;
use App\Models\Transfer;
use App\Models\FantasyRoster;
use App\Models\Season;
use App\Models\Gameweek;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TransferService
{
    /**
     * Crear transferencia de agente libre
     */
    public function createFreeAgentTransfer(
        FantasyTeam $buyer,
        Player $player,
        float $price
    ): Transfer {
        $transfer = Transfer::create([
            'league_id' => $buyer->league_id,
            'player_id' => $player->id,
            'from_fantasy_team_id' => null,
            'to_fantasy_team_id' => $buyer->id,
            'price' => $price,
            'type' => Transfer::TYPE_BUY,
            'effective_at' => now(),
            'meta' => ['source' => 'free_agent'],
        ]);
        
        $this->createRosterEntry($buyer, $player);
        
        return $transfer;
    }
    
    /**
     * Crear transferencia entre equipos
     */
    public function createTransfer(
        FantasyTeam $buyer,
        FantasyTeam $seller,
        Player $player,
        float $price
    ): Transfer {
        $transfer = Transfer::create([
            'league_id' => $buyer->league_id,
            'player_id' => $player->id,
            'from_fantasy_team_id' => $seller->id,
            'to_fantasy_team_id' => $buyer->id,
            'price' => $price,
            'type' => Transfer::TYPE_BUY,
            'effective_at' => now(),
            'meta' => ['source' => 'listing'],
        ]);
        
        $this->removeFromRoster($seller, $player);
        $this->createRosterEntry($buyer, $player);
        
        return $transfer;
    }
    
    /**
     * Crear entrada en roster para siguiente gameweek
     */
    public function createRosterEntry(FantasyTeam $team, Player $player): void
    {
        $nextGameweek = $this->getNextGameweek($team->league->season);
        
        if (!$nextGameweek) {
            throw new \Exception('No hay gameweek siguiente disponible.');
        }
        
        $maxSlot = FantasyRoster::where('fantasy_team_id', $team->id)
            ->where('gameweek_id', $nextGameweek->id)
            ->max('slot') ?? 0;
        
        FantasyRoster::create([
            'fantasy_team_id' => $team->id,
            'player_id' => $player->id,
            'gameweek_id' => $nextGameweek->id,
            'slot' => $maxSlot + 1,
            'is_starter' => false,
            'captaincy' => 0,
        ]);
    }
    
    /**
     * Remover del roster (siguiente gameweek)
     */
    public function removeFromRoster(FantasyTeam $team, Player $player): void
    {
        $nextGameweek = $this->getNextGameweek($team->league->season);
        
        if ($nextGameweek) {
            FantasyRoster::where('fantasy_team_id', $team->id)
                ->where('player_id', $player->id)
                ->where('gameweek_id', $nextGameweek->id)
                ->delete();
        }
    }
    
    /**
     * Obtener siguiente gameweek
     */
    private function getNextGameweek(Season $season): ?Gameweek
    {
        return Gameweek::where('season_id', $season->id)
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->first();
    }
    
    /**
     * Incrementar contador de transferencias
     */
    public function incrementTransfersCount(FantasyTeam $team, Gameweek $gameweek): void
    {
        $cacheKey = "transfers_count_{$team->id}_{$gameweek->id}";
        Cache::increment($cacheKey);
        Cache::put($cacheKey, Cache::get($cacheKey, 0), now()->addDays(7));
    }
    
    /**
     * Obtener transferencias usadas
     */
    public function getTransfersUsed(FantasyTeam $team, Gameweek $gameweek): int
    {
        $cacheKey = "transfers_count_{$team->id}_{$gameweek->id}";
        return Cache::get($cacheKey, 0);
    }
    
    /**
     * Resetear contador de transferencias
     */
    public function resetTransfersCount(FantasyTeam $team, Gameweek $gameweek): void
    {
        $cacheKey = "transfers_count_{$team->id}_{$gameweek->id}";
        Cache::forget($cacheKey);
    }
}
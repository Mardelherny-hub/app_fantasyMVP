<?php

namespace App\Services\Admin\Market;

use App\Models\Player;
use App\Models\PlayerValuation;
use App\Models\Season;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PriceManagementService
{
    /**
     * Mínimo valor de mercado permitido
     */
    const MIN_MARKET_VALUE = 0.50;

    /**
     * Máximo multiplicador para ajuste masivo
     */
    const MAX_MULTIPLIER = 2.0;

    /**
     * Actualizar precio de un jugador individual
     *
     * @param Player $player
     * @param Season $season
     * @param float $newPrice
     * @return PlayerValuation
     * @throws ValidationException
     */
    public function updatePlayerPrice(Player $player, Season $season, float $newPrice): PlayerValuation
    {
        $this->validatePrice($newPrice);

        $valuation = PlayerValuation::firstOrCreate(
            ['player_id' => $player->id, 'season_id' => $season->id],
            ['market_value' => 0.50, 'updated_at' => now()]
        );

        $oldPrice = $valuation->market_value;

        $valuation->update(['market_value' => $newPrice]);

        // Registrar en historial como edición manual
        $currentGameweek = \App\Models\Gameweek::where('season_id', $season->id)
            ->where('is_closed', true)
            ->orderBy('number', 'desc')
            ->first();

        if ($currentGameweek) {
            \App\Models\PlayerValuationHistory::updateOrCreate(
                ['player_id' => $player->id, 'season_id' => $season->id, 'gameweek_id' => $currentGameweek->id],
                ['market_value' => $newPrice, 'previous_value' => $oldPrice, 'source' => 'manual']
            );
        }

        Log::info('Price updated manually', [
            'player_id' => $player->id,
            'player_name' => $player->full_name,
            'season_id' => $season->id,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
        ]);

        return $valuation->fresh();
    }

    /**
     * Ajuste masivo de precios por posición
     *
     * @param Season $season
     * @param int $position
     * @param float $percentage Porcentaje de cambio (ej: 10 = +10%, -5 = -5%)
     * @return array
     * @throws ValidationException
     */
    public function bulkAdjustByPosition(Season $season, int $position, float $percentage): array
    {
        $this->validatePosition($position);
        $this->validatePercentage($percentage);

        $multiplier = 1 + ($percentage / 100);

        $updated = 0;
        $errors = [];

        DB::transaction(function () use ($season, $position, $multiplier, &$updated, &$errors) {
            $players = Player::where('position', $position)
                ->where('is_active', true)
                ->with(['valuations' => fn($q) => $q->where('season_id', $season->id)])
                ->get();

            foreach ($players as $player) {
                $valuation = $player->valuations->first();

                if (!$valuation) {
                    $errors[] = "Player {$player->id} has no valuation";
                    continue;
                }

                $currentPrice = (float) $valuation->market_value;
                $newPrice = $currentPrice * $multiplier;

                // Aplicar límites
                $newPrice = max(self::MIN_MARKET_VALUE, $newPrice);
                $newPrice = round($newPrice, 2);

                $valuation->update(['market_value' => $newPrice]);
                $updated++;
            }
        });

        Log::info('Bulk price adjustment by position', [
            'season_id' => $season->id,
            'position' => $position,
            'percentage' => $percentage,
            'players_updated' => $updated,
            'errors_count' => count($errors),
        ]);

        return [
            'updated' => $updated,
            'errors' => $errors,
        ];
    }

    /**
     * Ajuste masivo de precios por rango de precio actual
     *
     * @param Season $season
     * @param float $minPrice
     * @param float $maxPrice
     * @param float $percentage
     * @return array
     * @throws ValidationException
     */
    public function bulkAdjustByPriceRange(
        Season $season,
        float $minPrice,
        float $maxPrice,
        float $percentage
    ): array {
        $this->validatePriceRange($minPrice, $maxPrice);
        $this->validatePercentage($percentage);

        $multiplier = 1 + ($percentage / 100);

        $updated = 0;

        DB::transaction(function () use ($season, $minPrice, $maxPrice, $multiplier, &$updated) {
            $valuations = PlayerValuation::where('season_id', $season->id)
                ->whereBetween('market_value', [$minPrice, $maxPrice])
                ->get();

            foreach ($valuations as $valuation) {
                $currentPrice = (float) $valuation->market_value;
                $newPrice = $currentPrice * $multiplier;

                $newPrice = max(self::MIN_MARKET_VALUE, $newPrice);
                $newPrice = round($newPrice, 2);

                $valuation->update(['market_value' => $newPrice]);
                $updated++;
            }
        });

        Log::info('Bulk price adjustment by range', [
            'season_id' => $season->id,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'percentage' => $percentage,
            'valuations_updated' => $updated,
        ]);

        return ['updated' => $updated];
    }

    /**
     * Resetear todos los precios a un valor base
     *
     * @param Season $season
     * @param float $basePrice
     * @return int
     */
    public function resetAllPrices(Season $season, float $basePrice = 5.00): int
    {
        $this->validatePrice($basePrice);

        $updated = PlayerValuation::where('season_id', $season->id)
            ->update(['market_value' => $basePrice]);

        Log::warning('All prices reset', [
            'season_id' => $season->id,
            'base_price' => $basePrice,
            'valuations_updated' => $updated,
        ]);

        return $updated;
    }

    /**
     * Obtener estadísticas de precios
     *
     * @param Season $season
     * @return array
     */
    public function getPriceStats(Season $season): array
    {
        $valuations = PlayerValuation::where('season_id', $season->id)->get();

        return [
            'total_players' => $valuations->count(),
            'avg_price' => round($valuations->avg('market_value'), 2),
            'min_price' => round($valuations->min('market_value'), 2),
            'max_price' => round($valuations->max('market_value'), 2),
            'total_value' => round($valuations->sum('market_value'), 2),
            'by_position' => $this->getStatsByPosition($season),
        ];
    }

    /**
     * Obtener estadísticas por posición
     *
     * @param Season $season
     * @return array
     */
    private function getStatsByPosition(Season $season): array
    {
        $stats = [];

        $positions = [
            Player::POSITION_GK => 'GK',
            Player::POSITION_DF => 'DF',
            Player::POSITION_MF => 'MF',
            Player::POSITION_FW => 'FW',
        ];

        foreach ($positions as $positionId => $positionName) {
            $valuations = PlayerValuation::where('season_id', $season->id)
                ->whereHas('player', fn($q) => $q->where('position', $positionId))
                ->get();

            $stats[$positionName] = [
                'count' => $valuations->count(),
                'avg' => round($valuations->avg('market_value') ?? 0, 2),
                'min' => round($valuations->min('market_value') ?? 0, 2),
                'max' => round($valuations->max('market_value') ?? 0, 2),
            ];
        }

        return $stats;
    }

    /**
     * Validar precio
     *
     * @param float $price
     * @throws ValidationException
     */
    private function validatePrice(float $price): void
    {
        if ($price < self::MIN_MARKET_VALUE) {
            throw ValidationException::withMessages([
                'price' => __('El precio mínimo permitido es $:min', [
                    'min' => number_format(self::MIN_MARKET_VALUE, 2)
                ])
            ]);
        }

        if ($price > 999999.99) {
            throw ValidationException::withMessages([
                'price' => __('El precio máximo permitido es $999,999.99')
            ]);
        }
    }

    /**
     * Validar posición
     *
     * @param int $position
     * @throws ValidationException
     */
    private function validatePosition(int $position): void
    {
        $validPositions = [
            Player::POSITION_GK,
            Player::POSITION_DF,
            Player::POSITION_MF,
            Player::POSITION_FW,
        ];

        if (!in_array($position, $validPositions)) {
            throw ValidationException::withMessages([
                'position' => __('Posición inválida')
            ]);
        }
    }

    /**
     * Validar porcentaje de ajuste
     *
     * @param float $percentage
     * @throws ValidationException
     */
    private function validatePercentage(float $percentage): void
    {
        if ($percentage < -50) {
            throw ValidationException::withMessages([
                'percentage' => __('La reducción máxima permitida es -50%')
            ]);
        }

        if ($percentage > 100) {
            throw ValidationException::withMessages([
                'percentage' => __('El aumento máximo permitido es +100%')
            ]);
        }
    }

    /**
     * Validar rango de precios
     *
     * @param float $minPrice
     * @param float $maxPrice
     * @throws ValidationException
     */
    private function validatePriceRange(float $minPrice, float $maxPrice): void
    {
        if ($minPrice >= $maxPrice) {
            throw ValidationException::withMessages([
                'price_range' => __('El precio mínimo debe ser menor al máximo')
            ]);
        }

        $this->validatePrice($minPrice);
        $this->validatePrice($maxPrice);
    }
}
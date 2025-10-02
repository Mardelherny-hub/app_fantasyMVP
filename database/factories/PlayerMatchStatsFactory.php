<?php

namespace Database\Factories;

use App\Models\PlayerMatchStats;
use App\Models\FootballMatch;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlayerMatchStats>
 */
class PlayerMatchStatsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlayerMatchStats::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $minutes = fake()->numberBetween(0, 90);
        
        // Estadísticas base (serán ajustadas por posición)
        return [
            'match_id' => FootballMatch::factory(),
            'player_id' => Player::factory(),
            'minutes' => $minutes,
            'goals' => 0,
            'assists' => 0,
            'shots' => 0,
            'saves' => 0,
            'yellow' => fake()->boolean(15) ? 1 : 0, // 15% probabilidad amarilla
            'red' => fake()->boolean(2) ? 1 : 0, // 2% probabilidad roja
            'clean_sheet' => false,
            'conceded' => 0,
            'rating' => $minutes > 0 ? fake()->randomFloat(2, 5.0, 9.5) : null,
            'raw' => null,
        ];
    }

    /**
     * Stats for a goalkeeper.
     */
    public function goalkeeper(): static
    {
        return $this->state(function (array $attributes) {
            $minutes = $attributes['minutes'];
            $cleanSheet = fake()->boolean(35); // 35% clean sheet
            $saves = $minutes > 0 ? fake()->numberBetween(2, 10) : 0;
            $conceded = $cleanSheet ? 0 : fake()->numberBetween(1, 4);
            
            return [
                'goals' => fake()->boolean(1) ? 1 : 0, // 1% gol de portero
                'assists' => 0,
                'shots' => 0,
                'saves' => $saves,
                'clean_sheet' => $cleanSheet,
                'conceded' => $conceded,
                'rating' => $minutes > 0 ? fake()->randomFloat(2, 5.5, 9.0) : null,
            ];
        });
    }

    /**
     * Stats for a defender.
     */
    public function defender(): static
    {
        return $this->state(function (array $attributes) {
            $minutes = $attributes['minutes'];
            $cleanSheet = fake()->boolean(30); // 30% clean sheet
            
            return [
                'goals' => fake()->boolean(8) ? 1 : 0, // 8% probabilidad de gol
                'assists' => fake()->boolean(12) ? 1 : 0, // 12% asistencia
                'shots' => $minutes > 0 ? fake()->numberBetween(0, 3) : 0,
                'saves' => 0,
                'clean_sheet' => $cleanSheet,
                'conceded' => $cleanSheet ? 0 : fake()->numberBetween(1, 3),
                'rating' => $minutes > 0 ? fake()->randomFloat(2, 5.5, 8.5) : null,
            ];
        });
    }

    /**
     * Stats for a midfielder.
     */
    public function midfielder(): static
    {
        return $this->state(function (array $attributes) {
            $minutes = $attributes['minutes'];
            $cleanSheet = fake()->boolean(20); // 20% clean sheet
            
            return [
                'goals' => fake()->boolean(15) ? fake()->numberBetween(1, 2) : 0, // 15% gol
                'assists' => fake()->boolean(25) ? fake()->numberBetween(1, 2) : 0, // 25% asistencia
                'shots' => $minutes > 0 ? fake()->numberBetween(1, 5) : 0,
                'saves' => 0,
                'clean_sheet' => $cleanSheet,
                'conceded' => 0,
                'rating' => $minutes > 0 ? fake()->randomFloat(2, 5.0, 9.0) : null,
            ];
        });
    }

    /**
     * Stats for a forward.
     */
    public function forward(): static
    {
        return $this->state(function (array $attributes) {
            $minutes = $attributes['minutes'];
            
            return [
                'goals' => fake()->boolean(30) ? fake()->numberBetween(1, 3) : 0, // 30% gol
                'assists' => fake()->boolean(20) ? 1 : 0, // 20% asistencia
                'shots' => $minutes > 0 ? fake()->numberBetween(2, 8) : 0,
                'saves' => 0,
                'clean_sheet' => false,
                'conceded' => 0,
                'rating' => $minutes > 0 ? fake()->randomFloat(2, 5.0, 9.5) : null,
            ];
        });
    }

    /**
     * Player who scored.
     */
    public function scored(): static
    {
        return $this->state(fn (array $attributes) => [
            'goals' => fake()->numberBetween(1, 3),
            'shots' => fake()->numberBetween(3, 8),
            'rating' => fake()->randomFloat(2, 7.0, 9.5),
        ]);
    }

    /**
     * Player who assisted.
     */
    public function assisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'assists' => fake()->numberBetween(1, 2),
            'rating' => fake()->randomFloat(2, 7.0, 9.0),
        ]);
    }

    /**
     * Player with full 90 minutes.
     */
    public function fullMatch(): static
    {
        return $this->state(fn (array $attributes) => [
            'minutes' => 90,
        ]);
    }

    /**
     * Player as substitute (less than 45 minutes).
     */
    public function substitute(): static
    {
        return $this->state(fn (array $attributes) => [
            'minutes' => fake()->numberBetween(10, 44),
        ]);
    }

    /**
     * Player who didn't play.
     */
    public function unused(): static
    {
        return $this->state(fn (array $attributes) => [
            'minutes' => 0,
            'goals' => 0,
            'assists' => 0,
            'shots' => 0,
            'saves' => 0,
            'yellow' => 0,
            'red' => 0,
            'rating' => null,
        ]);
    }

    /**
     * Player with yellow card.
     */
    public function booked(): static
    {
        return $this->state(fn (array $attributes) => [
            'yellow' => 1,
            'rating' => fake()->randomFloat(2, 5.0, 7.0),
        ]);
    }

    /**
     * Player with red card.
     */
    public function sentOff(): static
    {
        return $this->state(fn (array $attributes) => [
            'red' => 1,
            'minutes' => fake()->numberBetween(15, 80), // Expulsado antes del final
            'rating' => fake()->randomFloat(2, 3.0, 5.5),
        ]);
    }

    /**
     * Man of the match performance.
     */
    public function manOfTheMatch(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'minutes' => 90,
                'goals' => fake()->numberBetween(1, 3),
                'assists' => fake()->numberBetween(0, 2),
                'shots' => fake()->numberBetween(4, 10),
                'yellow' => 0,
                'red' => 0,
                'rating' => fake()->randomFloat(2, 8.5, 10.0),
            ];
        });
    }

    /**
     * Poor performance.
     */
    public function poorPerformance(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'minutes' => fake()->numberBetween(45, 90),
                'goals' => 0,
                'assists' => 0,
                'shots' => fake()->numberBetween(0, 2),
                'yellow' => fake()->boolean(40) ? 1 : 0,
                'rating' => fake()->randomFloat(2, 3.5, 5.5),
            ];
        });
    }

    /**
     * Set specific match and player.
     */
    public function forMatchAndPlayer(int $matchId, int $playerId): static
    {
        return $this->state(fn (array $attributes) => [
            'match_id' => $matchId,
            'player_id' => $playerId,
        ]);
    }

    /**
     * Add additional raw stats data.
     */
    public function withRawData(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'raw' => [
                    'passes_completed' => fake()->numberBetween(20, 80),
                    'passes_attempted' => fake()->numberBetween(25, 90),
                    'tackles' => fake()->numberBetween(0, 8),
                    'interceptions' => fake()->numberBetween(0, 6),
                    'duels_won' => fake()->numberBetween(2, 15),
                    'fouls_committed' => fake()->numberBetween(0, 4),
                    'fouls_suffered' => fake()->numberBetween(0, 4),
                ],
            ];
        });
    }
}
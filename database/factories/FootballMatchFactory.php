<?php

namespace Database\Factories;

use App\Models\FootballMatch;
use App\Models\Season;
use App\Models\RealTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FootballMatch>
 */
class FootballMatchFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = FootballMatch::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'matchday' => fake()->numberBetween(1, 30),
            'home_team_id' => RealTeam::factory(),
            'away_team_id' => RealTeam::factory(),
            'starts_at' => fake()->dateTimeBetween('now', '+3 months'),
            'status' => FootballMatch::STATUS_PENDING,
            'home_goals' => 0,
            'away_goals' => 0,
            'data' => null,
        ];
    }

    /**
     * Indicate that the match is finished with random score.
     */
    public function finished(): static
    {
        return $this->state(function (array $attributes) {
            $homeGoals = fake()->numberBetween(0, 5);
            $awayGoals = fake()->numberBetween(0, 5);
            
            return [
                'starts_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
                'status' => FootballMatch::STATUS_FINISHED,
                'home_goals' => $homeGoals,
                'away_goals' => $awayGoals,
            ];
        });
    }

    /**
     * Indicate that the match is live.
     */
    public function live(): static
    {
        return $this->state(function (array $attributes) {
            // Partido en curso con goles parciales
            $homeGoals = fake()->numberBetween(0, 3);
            $awayGoals = fake()->numberBetween(0, 3);
            
            return [
                'starts_at' => fake()->dateTimeBetween('-2 hours', 'now'),
                'status' => FootballMatch::STATUS_LIVE,
                'home_goals' => $homeGoals,
                'away_goals' => $awayGoals,
            ];
        });
    }

    /**
     * Indicate that the match is postponed.
     */
    public function postponed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FootballMatch::STATUS_POSTPONED,
        ]);
    }

    /**
     * Set specific matchday.
     */
    public function matchday(int $matchday): static
    {
        return $this->state(fn (array $attributes) => [
            'matchday' => $matchday,
        ]);
    }

    /**
     * Set specific season.
     */
    public function forSeason(int $seasonId): static
    {
        return $this->state(fn (array $attributes) => [
            'season_id' => $seasonId,
        ]);
    }

    /**
     * Set specific teams.
     */
    public function between(int $homeTeamId, int $awayTeamId): static
    {
        return $this->state(fn (array $attributes) => [
            'home_team_id' => $homeTeamId,
            'away_team_id' => $awayTeamId,
        ]);
    }

    /**
     * Set specific score (only for finished matches).
     */
    public function withScore(int $homeGoals, int $awayGoals): static
    {
        return $this->state(fn (array $attributes) => [
            'home_goals' => $homeGoals,
            'away_goals' => $awayGoals,
        ]);
    }

    /**
     * Add match data (referee, stadium, etc).
     */
    public function withData(): static
    {
        return $this->state(function (array $attributes) {
            $stadiums = [
                'BMO Field', 'BC Place', 'Tim Hortons Field', 'TD Place',
                'IG Field', 'Wanderers Grounds', 'Spruce Meadows', 'Stade Saputo'
            ];
            
            $referees = [
                'John Smith', 'Marie Dubois', 'Carlos Rodriguez', 'David Johnson',
                'Sarah Williams', 'Pierre Tremblay', 'Michael Brown', 'Lisa Chen'
            ];
            
            return [
                'data' => [
                    'stadium' => fake()->randomElement($stadiums),
                    'referee' => fake()->randomElement($referees),
                    'attendance' => fake()->numberBetween(2000, 15000),
                    'weather' => fake()->randomElement(['Sunny', 'Cloudy', 'Rainy', 'Clear']),
                    'temperature' => fake()->numberBetween(-5, 30),
                ],
            ];
        });
    }

    /**
     * Home win result.
     */
    public function homeWin(): static
    {
        return $this->state(function (array $attributes) {
            $homeGoals = fake()->numberBetween(2, 5);
            $awayGoals = fake()->numberBetween(0, $homeGoals - 1);
            
            return [
                'starts_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
                'status' => FootballMatch::STATUS_FINISHED,
                'home_goals' => $homeGoals,
                'away_goals' => $awayGoals,
            ];
        });
    }

    /**
     * Away win result.
     */
    public function awayWin(): static
    {
        return $this->state(function (array $attributes) {
            $awayGoals = fake()->numberBetween(2, 5);
            $homeGoals = fake()->numberBetween(0, $awayGoals - 1);
            
            return [
                'starts_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
                'status' => FootballMatch::STATUS_FINISHED,
                'home_goals' => $homeGoals,
                'away_goals' => $awayGoals,
            ];
        });
    }

    /**
     * Draw result.
     */
    public function draw(): static
    {
        return $this->state(function (array $attributes) {
            $goals = fake()->numberBetween(0, 3);
            
            return [
                'starts_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
                'status' => FootballMatch::STATUS_FINISHED,
                'home_goals' => $goals,
                'away_goals' => $goals,
            ];
        });
    }

    /**
     * High-scoring match.
     */
    public function highScoring(): static
    {
        return $this->state(function (array $attributes) {
            $homeGoals = fake()->numberBetween(3, 6);
            $awayGoals = fake()->numberBetween(3, 6);
            
            return [
                'starts_at' => fake()->dateTimeBetween('-2 months', '-1 day'),
                'status' => FootballMatch::STATUS_FINISHED,
                'home_goals' => $homeGoals,
                'away_goals' => $awayGoals,
            ];
        });
    }

    /**
     * Upcoming match (future date).
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => fake()->dateTimeBetween('now', '+3 months'),
            'status' => FootballMatch::STATUS_PENDING,
        ]);
    }
}
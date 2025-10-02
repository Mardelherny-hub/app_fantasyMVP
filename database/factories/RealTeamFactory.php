<?php

namespace Database\Factories;

use App\Models\RealTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RealTeam>
 */
class RealTeamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RealTeam::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Equipos inspirados en Canadian Premier League
        $canadianCities = [
            'Toronto', 'Vancouver', 'Montreal', 'Calgary', 'Edmonton',
            'Ottawa', 'Winnipeg', 'Halifax', 'Victoria', 'Hamilton',
            'Quebec City', 'Saskatoon', 'Regina', 'London', 'Windsor'
        ];
        
        $suffixes = ['FC', 'United', 'Wanderers', 'City', 'Athletic', 'Cavalry'];
        $descriptors = ['North', 'Pacific', 'Forge', 'Valour', 'York', 'Atlético'];

        // Generar nombre de equipo canadiense
        $useCity = fake()->boolean(70);
        $useDescriptor = fake()->boolean(30);
        
        $teamName = fake()->randomElement($canadianCities);
        
        if ($useDescriptor && !$useCity) {
            $teamName = fake()->randomElement($descriptors);
        }
        
        $teamName .= ' ' . fake()->randomElement($suffixes);

        // Generar short_name (3-4 letras)
        $words = explode(' ', $teamName);
        $shortName = '';
        foreach ($words as $word) {
            if (strlen($word) > 2) { // Ignorar palabras muy cortas
                $shortName .= strtoupper(substr($word, 0, 1));
            }
        }
        $shortName = substr($shortName, 0, 4); // Max 4 caracteres
        if (strlen($shortName) < 3) {
            $shortName = strtoupper(substr($teamName, 0, 3));
        }

        return [
            'name' => $teamName,
            'short_name' => $shortName,
            'country' => 'CA', // Canada por defecto
            'founded_year' => fake()->numberBetween(2017, 2024), // CPL fundada en 2019
            'logo_url' => fake()->boolean(40) ? 'https://placehold.co/200x200/png?text=' . urlencode($shortName) : null,
        ];
    }

    /**
     * Indicate that the team is from Eastern Canada.
     */
    public function eastern(): static
    {
        return $this->state(function (array $attributes) {
            $easternCities = ['Toronto', 'Montreal', 'Ottawa', 'Halifax', 'Hamilton', 'Quebec City'];
            $city = fake()->randomElement($easternCities);
            $suffix = fake()->randomElement(['FC', 'United', 'City', 'Athletic']);
            
            return [
                'name' => $city . ' ' . $suffix,
            ];
        });
    }

    /**
     * Indicate that the team is from Western Canada.
     */
    public function western(): static
    {
        return $this->state(function (array $attributes) {
            $westernCities = ['Vancouver', 'Calgary', 'Edmonton', 'Winnipeg', 'Victoria', 'Saskatoon'];
            $city = fake()->randomElement($westernCities);
            $suffix = fake()->randomElement(['FC', 'United', 'Whitecaps', 'Cavalry']);
            
            return [
                'name' => $city . ' ' . $suffix,
            ];
        });
    }

    /**
     * Indicate that the team is a CPL founding member (2019).
     */
    public function foundingMember(): static
    {
        return $this->state(fn (array $attributes) => [
            'founded_year' => 2019,
        ]);
    }

    /**
     * Indicate that the team is recently established (2020+).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'founded_year' => fake()->numberBetween(2020, 2024),
        ]);
    }

    /**
     * Indicate that the team has a logo.
     */
    public function withLogo(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'logo_url' => 'https://placehold.co/200x200/png?text=' . urlencode($attributes['short_name']),
            ];
        });
    }
}
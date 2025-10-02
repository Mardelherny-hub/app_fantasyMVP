<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Player::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $fullName = $firstName . ' ' . $lastName;
        
        // 40% de probabilidad de tener un apodo/known_as
        $knownAs = fake()->boolean(40) ? $firstName : null;

        // Posición aleatoria
        $position = fake()->numberBetween(Player::POSITION_GK, Player::POSITION_FW);

        // Altura y peso según posición
        $heightWeight = $this->getHeightWeightForPosition($position);

        return [
            'full_name' => $fullName,
            'known_as' => $knownAs,
            'position' => $position,
            'nationality' => 'CA', // Canadá por defecto
            'birthdate' => fake()->dateTimeBetween('-38 years', '-18 years')->format('Y-m-d'),
            'height_cm' => $heightWeight['height'],
            'weight_kg' => $heightWeight['weight'],
            'photo_url' => fake()->boolean(25) ? 'https://i.pravatar.cc/300?u=' . urlencode($fullName) : null,
            'is_active' => true,
        ];
    }

    /**
     * Get realistic height and weight based on position.
     */
    private function getHeightWeightForPosition(int $position): array
    {
        return match($position) {
            Player::POSITION_GK => [
                'height' => fake()->numberBetween(185, 200), // Porteros más altos
                'weight' => fake()->numberBetween(75, 95),
            ],
            Player::POSITION_DF => [
                'height' => fake()->numberBetween(175, 195), // Defensas altos
                'weight' => fake()->numberBetween(70, 90),
            ],
            Player::POSITION_MF => [
                'height' => fake()->numberBetween(165, 185), // Mediocampistas variados
                'weight' => fake()->numberBetween(65, 82),
            ],
            Player::POSITION_FW => [
                'height' => fake()->numberBetween(170, 190), // Delanteros variados
                'weight' => fake()->numberBetween(68, 88),
            ],
            default => [
                'height' => fake()->numberBetween(170, 190),
                'weight' => fake()->numberBetween(70, 85),
            ],
        };
    }

    /**
     * Indicate that the player is a goalkeeper.
     */
    public function goalkeeper(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => Player::POSITION_GK,
            'height_cm' => fake()->numberBetween(185, 200),
            'weight_kg' => fake()->numberBetween(75, 95),
        ]);
    }

    /**
     * Indicate that the player is a defender.
     */
    public function defender(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => Player::POSITION_DF,
            'height_cm' => fake()->numberBetween(175, 195),
            'weight_kg' => fake()->numberBetween(70, 90),
        ]);
    }

    /**
     * Indicate that the player is a midfielder.
     */
    public function midfielder(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => Player::POSITION_MF,
            'height_cm' => fake()->numberBetween(165, 185),
            'weight_kg' => fake()->numberBetween(65, 82),
        ]);
    }

    /**
     * Indicate that the player is a forward.
     */
    public function forward(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => Player::POSITION_FW,
            'height_cm' => fake()->numberBetween(170, 190),
            'weight_kg' => fake()->numberBetween(68, 88),
        ]);
    }

    /**
     * Indicate that the player is young (18-25 years).
     */
    public function young(): static
    {
        return $this->state(fn (array $attributes) => [
            'birthdate' => fake()->dateTimeBetween('-25 years', '-18 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the player is in their prime (26-30 years).
     */
    public function prime(): static
    {
        return $this->state(fn (array $attributes) => [
            'birthdate' => fake()->dateTimeBetween('-30 years', '-26 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the player is a veteran (31-38 years).
     */
    public function veteran(): static
    {
        return $this->state(fn (array $attributes) => [
            'birthdate' => fake()->dateTimeBetween('-38 years', '-31 years')->format('Y-m-d'),
        ]);
    }

    /**
     * Indicate that the player is international (non-Canadian).
     */
    public function international(): static
    {
        return $this->state(fn (array $attributes) => [
            'nationality' => fake()->randomElement([
                'US', 'MX', 'BR', 'AR', 'CO', 'UY', 'CL',  // Américas
                'ES', 'FR', 'IT', 'DE', 'GB', 'PT', 'NL',  // Europa
                'NG', 'GH', 'CI', 'SN', 'CM',              // África
            ]),
        ]);
    }

    /**
     * Indicate that the player has a photo.
     */
    public function withPhoto(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'photo_url' => 'https://i.pravatar.cc/300?u=' . urlencode($attributes['full_name']),
            ];
        });
    }

    /**
     * Indicate that the player has a known nickname.
     */
    public function withNickname(): static
    {
        return $this->state(function (array $attributes) {
            $nicknames = ['El Tigre', 'La Perla', 'The Rocket', 'Speedy', 'Flash', 'The Wall', 'Magic'];
            return [
                'known_as' => fake()->randomElement($nicknames),
            ];
        });
    }

    /**
     * Indicate that the player is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
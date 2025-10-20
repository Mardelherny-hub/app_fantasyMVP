<?php

namespace App\Services\Manager;

use App\Models\Player;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class FormationService
{
    /**
     * Formaciones disponibles
     * Formato: [GK-DF-MF-FW]
     */
    const FORMATIONS = [
        '4-4-2' => [
            'name' => '4-4-2 Clásica',
            'description' => 'Formación equilibrada y versátil',
            'positions' => [
                Player::POSITION_GK => 1,
                Player::POSITION_DF => 4,
                Player::POSITION_MF => 4,
                Player::POSITION_FW => 2,
            ],
        ],
        '4-3-3' => [
            'name' => '4-3-3 Ofensiva',
            'description' => 'Formación ofensiva con tres delanteros',
            'positions' => [
                Player::POSITION_GK => 1,
                Player::POSITION_DF => 4,
                Player::POSITION_MF => 3,
                Player::POSITION_FW => 3,
            ],
        ],
        '3-5-2' => [
            'name' => '3-5-2 Media Fuerte',
            'description' => 'Dominio del mediocampo',
            'positions' => [
                Player::POSITION_GK => 1,
                Player::POSITION_DF => 3,
                Player::POSITION_MF => 5,
                Player::POSITION_FW => 2,
            ],
        ],
        '5-3-2' => [
            'name' => '5-3-2 Defensiva',
            'description' => 'Formación sólida defensivamente',
            'positions' => [
                Player::POSITION_GK => 1,
                Player::POSITION_DF => 5,
                Player::POSITION_MF => 3,
                Player::POSITION_FW => 2,
            ],
        ],
        '3-4-3' => [
            'name' => '3-4-3 Ultra Ofensiva',
            'description' => 'Máximo ataque con tres delanteros',
            'positions' => [
                Player::POSITION_GK => 1,
                Player::POSITION_DF => 3,
                Player::POSITION_MF => 4,
                Player::POSITION_FW => 3,
            ],
        ],
        '4-5-1' => [
            'name' => '4-5-1 Control',
            'description' => 'Control del mediocampo con un delantero',
            'positions' => [
                Player::POSITION_GK => 1,
                Player::POSITION_DF => 4,
                Player::POSITION_MF => 5,
                Player::POSITION_FW => 1,
            ],
        ],
        '5-4-1' => [
            'name' => '5-4-1 Muy Defensiva',
            'description' => 'Formación ultra defensiva',
            'positions' => [
                Player::POSITION_GK => 1,
                Player::POSITION_DF => 5,
                Player::POSITION_MF => 4,
                Player::POSITION_FW => 1,
            ],
        ],
    ];

    /**
     * Obtener todas las formaciones disponibles
     */
    public function getAvailableFormations(): array
    {
        return self::FORMATIONS;
    }

    /**
     * Obtener formación específica
     */
    public function getFormation(string $formationKey): ?array
    {
        return self::FORMATIONS[$formationKey] ?? null;
    }

    /**
     * Detectar formación actual a partir de titulares
     */
    public function detectFormation(Collection $starters): ?string
    {
        if ($starters->count() !== 11) {
            return null;
        }

        // Contar jugadores por posición
        $positionCounts = $this->countPositions($starters);

        // Buscar coincidencia exacta con formaciones disponibles
        foreach (self::FORMATIONS as $key => $formation) {
            if ($this->matchesFormation($positionCounts, $formation['positions'])) {
                return $key;
            }
        }

        // Si no coincide con ninguna formación predefinida, retornar formato genérico
        return sprintf(
            '%d-%d-%d',
            $positionCounts[Player::POSITION_DF] ?? 0,
            $positionCounts[Player::POSITION_MF] ?? 0,
            $positionCounts[Player::POSITION_FW] ?? 0
        );
    }

    /**
     * Verificar si los titulares coinciden con una formación válida
     */
    public function isValidFormation(Collection $starters): bool
    {
        if ($starters->count() !== 11) {
            return false;
        }

        $positionCounts = $this->countPositions($starters);

        // Validar que hay exactamente 1 arquero
        if ($positionCounts[Player::POSITION_GK] !== 1) {
            return false;
        }

        // Verificar si coincide con alguna formación predefinida
        foreach (self::FORMATIONS as $formation) {
            if ($this->matchesFormation($positionCounts, $formation['positions'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validar que una formación específica es válida
     */
    public function validateSpecificFormation(Collection $starters, string $formationKey): void
    {
        $formation = $this->getFormation($formationKey);

        if (!$formation) {
            throw ValidationException::withMessages([
                'formation' => __('Formación no válida.'),
            ]);
        }

        if ($starters->count() !== 11) {
            throw ValidationException::withMessages([
                'formation' => __('Debes tener exactamente 11 titulares.'),
            ]);
        }

        $positionCounts = $this->countPositions($starters);

        if (!$this->matchesFormation($positionCounts, $formation['positions'])) {
            throw ValidationException::withMessages([
                'formation' => __('Los jugadores no coinciden con la formación :formation.', [
                    'formation' => $formation['name'],
                ]),
            ]);
        }
    }

    /**
     * Obtener formaciones compatibles con los jugadores disponibles
     */
    public function getCompatibleFormations(Collection $availablePlayers): array
    {
        $positionCounts = $this->countPositions($availablePlayers);
        $compatible = [];

        foreach (self::FORMATIONS as $key => $formation) {
            $isCompatible = true;

            foreach ($formation['positions'] as $position => $required) {
                if (($positionCounts[$position] ?? 0) < $required) {
                    $isCompatible = false;
                    break;
                }
            }

            if ($isCompatible) {
                $compatible[$key] = $formation;
            }
        }

        return $compatible;
    }

    /**
     * Sugerir formaciones óptimas según jugadores disponibles
     */
    public function suggestFormations(Collection $availablePlayers): array
    {
        $compatible = $this->getCompatibleFormations($availablePlayers);

        if (empty($compatible)) {
            return [];
        }

        // Ordenar por cantidad de jugadores utilizados en posiciones fuertes
        $positionCounts = $this->countPositions($availablePlayers);
        $suggestions = [];

        foreach ($compatible as $key => $formation) {
            $score = $this->calculateFormationScore($formation['positions'], $positionCounts);
            $suggestions[$key] = [
                'formation' => $formation,
                'score' => $score,
            ];
        }

        // Ordenar por score descendente
        usort($suggestions, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return array_map(function ($item) {
            return $item['formation'];
        }, $suggestions);
    }

    /**
     * Contar jugadores por posición
     */
    protected function countPositions(Collection $players): array
    {
        $counts = [
            Player::POSITION_GK => 0,
            Player::POSITION_DF => 0,
            Player::POSITION_MF => 0,
            Player::POSITION_FW => 0,
        ];

        foreach ($players as $item) {
            // Si es FantasyRoster, acceder al player
            $player = $item instanceof \App\Models\FantasyRoster ? $item->player : $item;
            
            if ($player && isset($player->position)) {
                $position = $player->position;
                $counts[$position] = ($counts[$position] ?? 0) + 1;
            }
        }

        return $counts;
    }

    /**
     * Verificar si los conteos coinciden con una formación
     */
    protected function matchesFormation(array $positionCounts, array $formationPositions): bool
    {
        foreach ($formationPositions as $position => $required) {
            if (($positionCounts[$position] ?? 0) !== $required) {
                return false;
            }
        }

        return true;
    }

    /**
     * Calcular score de una formación (para sugerencias)
     * Score más alto = mejor aprovechamiento de jugadores disponibles
     */
    protected function calculateFormationScore(array $formationPositions, array $availableCounts): int
    {
        $score = 0;

        foreach ($formationPositions as $position => $required) {
            $available = $availableCounts[$position] ?? 0;
            $surplus = $available - $required;
            
            // Penalizar si hay mucho excedente (indica subutilización)
            if ($surplus > 3) {
                $score -= ($surplus - 3);
            } else {
                $score += $surplus;
            }
        }

        return $score;
    }

    /**
     * Obtener descripción de formación en formato legible
     */
    public function getFormationDisplay(string $formationKey): string
    {
        $formation = $this->getFormation($formationKey);
        
        if (!$formation) {
            return $formationKey;
        }

        return sprintf(
            '%s (%s)',
            $formation['name'],
            $formationKey
        );
    }

    /**
     * Verificar si se puede cambiar a una formación específica
     */
    public function canChangeToFormation(Collection $currentStarters, string $targetFormationKey): bool
    {
        $targetFormation = $this->getFormation($targetFormationKey);

        if (!$targetFormation) {
            return false;
        }

        // Contar disponibles en la plantilla completa
        $positionCounts = $this->countPositions($currentStarters);

        foreach ($targetFormation['positions'] as $position => $required) {
            if (($positionCounts[$position] ?? 0) < $required) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtener mensaje de error si no se puede usar una formación
     */
    public function getFormationErrorMessage(Collection $availablePlayers, string $formationKey): ?string
    {
        $formation = $this->getFormation($formationKey);

        if (!$formation) {
            return __('Formación no encontrada.');
        }

        $positionCounts = $this->countPositions($availablePlayers);
        $missing = [];

        foreach ($formation['positions'] as $position => $required) {
            $available = $positionCounts[$position] ?? 0;
            
            if ($available < $required) {
                $positionName = Player::POSITIONS[$position];
                $needed = $required - $available;
                $missing[] = __(':count :position', [
                    'count' => $needed,
                    'position' => $positionName,
                ]);
            }
        }

        if (empty($missing)) {
            return null;
        }

        return __('Te faltan: :missing', [
            'missing' => implode(', ', $missing),
        ]);
    }

    /**
     * Validar mínimos obligatorios (1 GK, 3 DF, 2 MF, 1 FW)
     */
    public function validateMinimumRequirements(Collection $starters): void
    {
        if ($starters->count() !== 11) {
            throw ValidationException::withMessages([
                'formation' => __('Debes tener exactamente 11 titulares.'),
            ]);
        }

        $positionCounts = $this->countPositions($starters);
        
        $minimums = [
            Player::POSITION_GK => 1,
            Player::POSITION_DF => 3,
            Player::POSITION_MF => 2,
            Player::POSITION_FW => 1,
        ];

        foreach ($minimums as $position => $minimum) {
            $current = $positionCounts[$position] ?? 0;
            
            if ($current < $minimum) {
                $positionName = Player::POSITIONS[$position];
                throw ValidationException::withMessages([
                    'formation' => __('Mínimo requerido: :min :position (tienes :current).', [
                        'min' => $minimum,
                        'position' => $positionName,
                        'current' => $current,
                    ]),
                ]);
            }
        }
    }
}
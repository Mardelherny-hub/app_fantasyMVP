<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Seeder;

class SeasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            [
                'name' => '2024',
                'code' => '2024',
                'starts_at' => '2024-03-01',
                'ends_at' => '2024-12-31',
                'is_active' => false,
            ],
            [
                'name' => '2025',
                'code' => '2025',
                'starts_at' => '2025-03-01',
                'ends_at' => '2026-12-31',
                'is_active' => true, // ⭐ Temporada activa
            ],
            [
                'name' => '2026',
                'code' => '2026',
                'starts_at' => '2026-03-01',
                'ends_at' => '2026-12-31',
                'is_active' => false,
            ],
        ];

        foreach ($seasons as $seasonData) {
            Season::firstOrCreate(
                ['code' => $seasonData['code']],
                $seasonData
            );
        }

        $this->command->info('✅ Temporadas creadas (3)');
    }
}
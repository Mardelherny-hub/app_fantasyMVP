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
                'name' => '2024/25',
                'code' => '2024-25',
                'starts_at' => '2024-08-01',
                'ends_at' => '2025-05-31',
                'is_active' => false,
            ],
            [
                'name' => '2025/26',
                'code' => '2025-26',
                'starts_at' => '2025-08-01',
                'ends_at' => '2026-05-31',
                'is_active' => true, // Temporada activa
            ],
            [
                'name' => '2026/27',
                'code' => '2026-27',
                'starts_at' => '2026-08-01',
                'ends_at' => '2027-05-31',
                'is_active' => false,
            ],
        ];

        foreach ($seasons as $season) {
            Season::firstOrCreate(
                ['code' => $season['code']],
                $season
            );
        }

        $this->command->info('✅ Temporadas creadas');
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RealCompetition;
use App\Services\LiveScoreApiService;

class CanadianCompetitionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏆 Cargando competiciones canadienses...');

        $apiService = new LiveScoreApiService();

        // Test API connection first
        if (!$apiService->testConnection()) {
            $this->command->error('❌ No se pudo conectar a la API de LiveScore.');
            $this->command->warn('⚠️  Verifica las credenciales en el archivo .env');
            $this->command->info('Credenciales configuradas:');
            $credentials = $apiService->getCredentialsInfo();
            foreach ($credentials as $key => $value) {
                $this->command->line("  - {$key}: {$value}");
            }
            return;
        }

        $this->command->info('✅ Conexión a API exitosa');

        // Get Canadian competitions
        $competitions = $apiService->getCanadianCompetitions();

        $created = 0;
        $updated = 0;

        foreach ($competitions as $competitionData) {
            $competition = RealCompetition::updateOrCreate(
                ['external_id' => $competitionData['external_id']],
                [
                    'name' => $competitionData['name'],
                    'country' => $competitionData['country'],
                    'type' => $competitionData['type'],
                    'active' => $competitionData['active'],
                    'external_source' => $competitionData['external_source'],
                    'meta' => [
                        'loaded_at' => now()->toDateTimeString(),
                        'source' => 'LiveScore API',
                    ],
                ]
            );

            if ($competition->wasRecentlyCreated) {
                $created++;
                $this->command->info("  ✓ Creada: {$competition->name} (ID: {$competition->external_id})");
            } else {
                $updated++;
                $this->command->info("  ↻ Actualizada: {$competition->name} (ID: {$competition->external_id})");
            }
        }

        $this->command->newLine();
        $this->command->info("✅ Competiciones procesadas:");
        $this->command->line("   - Creadas: {$created}");
        $this->command->line("   - Actualizadas: {$updated}");
        $this->command->line("   - Total: " . ($created + $updated));

        // Show competition details
        $this->command->newLine();
        $this->command->info('📋 Competiciones cargadas:');
        $allCompetitions = RealCompetition::orderBy('external_id')->get();
        
        foreach ($allCompetitions as $comp) {
            $typeIcon = $comp->type === 'league' ? '🏟️' : '🏆';
            $statusIcon = $comp->active ? '✅' : '⏸️';
            $this->command->line(
                "   {$typeIcon} {$statusIcon} [{$comp->external_id}] {$comp->name} ({$comp->type})"
            );
        }
    }
}
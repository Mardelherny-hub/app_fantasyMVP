<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\Admin\Fixtures\FixtureGeneratorService;
use App\Models\League;
use App\Models\Season;
use App\Models\Gameweek;
use App\Models\FantasyTeam;
use App\Models\User;
use App\Models\Fixture;

class FixtureGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FixtureGeneratorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FixtureGeneratorService();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->seed(\Database\Seeders\SeasonsSeeder::class);
        $this->seed(\Database\Seeders\GameweeksSeeder::class);
    }

    public function test_genera_fixtures_round_robin_con_equipos_pares()
    {
        $season = Season::first();
        
        $league = League::create([
            'owner_user_id' => User::factory()->create()->id,
            'season_id' => $season->id,
            'name' => 'Test League',
            'code' => 'TEST01',
            'type' => League::TYPE_PRIVATE,
            'max_participants' => 10,
            'regular_season_gameweeks' => 27,
            'total_gameweeks' => 30,
            'playoff_teams' => 5,
            'playoff_format' => League::PLAYOFF_FORMAT_PAGE,
        ]);

        // Crear 4 equipos
        for ($i = 1; $i <= 4; $i++) {
            FantasyTeam::create([
                'league_id' => $league->id,
                'user_id' => User::factory()->create()->id,
                'name' => "Team {$i}",
                'slug' => "team-{$i}",
                'budget' => 100.00,
            ]);
        }

        $fixtures = $this->service->generateRegularSeasonFixtures($league, 1);

        $this->assertGreaterThan(0, $fixtures->count());
        $this->assertEquals(Fixture::STATUS_PENDING, $fixtures->first()->status);
        $this->assertFalse($fixtures->first()->is_playoff);
    }

    public function test_genera_fixtures_round_robin_con_equipos_impares()
    {
        $season = Season::first();
        
        $league = League::create([
            'owner_user_id' => User::factory()->create()->id,
            'season_id' => $season->id,
            'name' => 'Test League',
            'code' => 'TEST02',
            'type' => League::TYPE_PRIVATE,
            'max_participants' => 10,
            'regular_season_gameweeks' => 27,
        ]);

        // Crear 5 equipos (impar)
        for ($i = 1; $i <= 5; $i++) {
            FantasyTeam::create([
                'league_id' => $league->id,
                'user_id' => User::factory()->create()->id,
                'name' => "Team {$i}",
                'slug' => "team-{$i}",
                'budget' => 100.00,
            ]);
        }

        $fixtures = $this->service->generateRegularSeasonFixtures($league, 1);

        $this->assertGreaterThan(0, $fixtures->count());
    }

    public function test_genera_playoffs_correctamente()
    {
        $season = Season::first();
        
        $league = League::create([
            'owner_user_id' => User::factory()->create()->id,
            'season_id' => $season->id,
            'name' => 'Test League',
            'code' => 'TEST03',
            'type' => League::TYPE_PRIVATE,
            'max_participants' => 10,
            'playoff_teams' => 5,
            'playoff_format' => League::PLAYOFF_FORMAT_PAGE,
        ]);

        // Crear 5 equipos y sus standings
        for ($i = 1; $i <= 5; $i++) {
            $team = FantasyTeam::create([
                'league_id' => $league->id,
                'user_id' => User::factory()->create()->id,
                'name' => "Team {$i}",
                'slug' => "team-{$i}",
                'budget' => 100.00,
            ]);

            \DB::table('league_standings')->insert([
                'league_id' => $league->id,
                'fantasy_team_id' => $team->id,
                'gameweek_id' => 27,
                'position' => $i,
                'points' => (6 - $i) * 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $fixtures = $this->service->generatePlayoffFixtures($league, 28, 29, 30);

        $this->assertEquals(4, $fixtures->count());
        $this->assertTrue($fixtures->every(fn($f) => $f->is_playoff));
        
        $quarters = $fixtures->where('playoff_round', Fixture::PLAYOFF_QUARTERS);
        $this->assertEquals(1, $quarters->count());
        
        $semis = $fixtures->where('playoff_round', Fixture::PLAYOFF_SEMIS);
        $this->assertEquals(2, $semis->count());
        
        $final = $fixtures->where('playoff_round', Fixture::PLAYOFF_FINAL);
        $this->assertEquals(1, $final->count());
    }

    public function test_falla_con_menos_de_2_equipos()
    {
        $season = Season::first();
        $user = User::factory()->create();
        
        $league = League::create([
            'owner_user_id' => $user->id,
            'season_id' => $season->id,
            'name' => 'Test League',
            'code' => 'TEST04',
            'type' => League::TYPE_PRIVATE,
        ]);

        $this->expectException(\Exception::class);
        $this->service->generateRegularSeasonFixtures($league);
    }
}
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LeagueService;
use App\Services\GameSimulatorService;
use App\Repositories\TeamRepository;
use App\Repositories\GameRepository;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChampionshipPredictionTest extends TestCase
{
    use RefreshDatabase;

    protected LeagueService $leagueService;
    protected TeamRepository $teamRepository;
    protected GameRepository $gameRepository;
    protected GameSimulatorService $simulator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = new TeamRepository();
        $this->gameRepository = new GameRepository();
        $this->simulator = new GameSimulatorService($this->teamRepository, $this->gameRepository);
        $this->leagueService = new LeagueService($this->teamRepository, $this->gameRepository, $this->simulator);
    }

    public function test_initial_predictions_use_strength()
    {
        Team::create(['name' => 'Chelsea', 'strength' => 5]);
        Team::create(['name' => 'Arsenal', 'strength' => 4]);
        Team::create(['name' => 'Manchester City', 'strength' => 4]);
        Team::create(['name' => 'Liverpool', 'strength' => 3]);
        $standings = $this->leagueService->standings();
        $chelsea = $standings->firstWhere('name', 'Chelsea');
        $liverpool = $standings->firstWhere('name', 'Liverpool');
        $this->assertGreaterThan($liverpool->championship_chance, $chelsea->championship_chance);
    }

    public function test_predictions_update_after_games()
    {
        Team::create(['name' => 'Chelsea', 'strength' => 5]);
        Team::create(['name' => 'Arsenal', 'strength' => 4]);
        Team::create(['name' => 'Manchester City', 'strength' => 4]);
        Team::create(['name' => 'Liverpool', 'strength' => 3]);
        $initial = $this->leagueService->standings();
        $initialChelsea = $initial->firstWhere('name', 'Chelsea')->championship_chance;
        $this->leagueService->simulateNextWeek();
        $updated = $this->leagueService->standings();
        $updatedChelsea = $updated->firstWhere('name', 'Chelsea')->championship_chance;
        $this->assertNotEquals($initialChelsea, $updatedChelsea);
    }
} 
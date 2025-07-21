<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\GameSimulatorService;
use App\Repositories\TeamRepository;
use App\Repositories\GameRepository;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GameSimulatorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GameSimulatorService $simulator;
    protected TeamRepository $teamRepository;
    protected GameRepository $gameRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = new TeamRepository();
        $this->gameRepository = new GameRepository();
        $this->simulator = new GameSimulatorService($this->teamRepository, $this->gameRepository);
    }

    public function test_simulate_match_creates_game()
    {
        $team1 = Team::create(['name' => 'A', 'strength' => 4]);
        $team2 = Team::create(['name' => 'B', 'strength' => 3]);
        $game = $this->simulator->simulateMatch($team1, $team2, 1);
        $this->assertNotNull($game);
        $this->assertEquals([$team1->id, $team2->id, 1, true], [$game->team1_id, $game->team2_id, $game->week, $game->played]);
        $this->assertGreaterThanOrEqual(0, $game->team1_goals);
        $this->assertGreaterThanOrEqual(0, $game->team2_goals);
    }

    public function test_simulate_match_updates_stats()
    {
        $team1 = Team::create(['name' => 'A', 'strength' => 4]);
        $team2 = Team::create(['name' => 'B', 'strength' => 3]);
        $game = $this->simulator->simulateMatch($team1, $team2, 1);
        $team1->refresh();
        $team2->refresh();
        $this->assertEquals($game->team1_goals, $team1->goals_for);
        $this->assertEquals($game->team2_goals, $team1->goals_against);
        $this->assertEquals($game->team2_goals, $team2->goals_for);
        $this->assertEquals($game->team1_goals, $team2->goals_against);
    }

    public function test_update_stats_manual()
    {
        $team1 = Team::create(['name' => 'A', 'strength' => 4]);
        $team2 = Team::create(['name' => 'B', 'strength' => 3]);
        $this->simulator->updateStats($team1, $team2, 2, 1);
        $team1->refresh();
        $team2->refresh();
        $this->assertEquals([3,1,2,1], [$team1->points, $team1->wins, $team1->goals_for, $team1->goals_against]);
        $this->assertEquals([0,1,1,2], [$team2->points, $team2->losses, $team2->goals_for, $team2->goals_against]);
    }
} 
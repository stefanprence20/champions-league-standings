<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LeagueService;
use App\Services\GameSimulatorService;
use App\Repositories\TeamRepository;
use App\Repositories\GameRepository;
use App\Models\Team;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeagueServiceTest extends TestCase
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

    public function test_standings_are_sorted_by_points()
    {
        Team::create(['name' => 'A', 'strength' => 3, 'points' => 6, 'wins' => 2, 'draws' => 0, 'losses' => 0, 'goals_for' => 4, 'goals_against' => 1]);
        Team::create(['name' => 'B', 'strength' => 3, 'points' => 3, 'wins' => 1, 'draws' => 0, 'losses' => 1, 'goals_for' => 2, 'goals_against' => 2]);
        Team::create(['name' => 'C', 'strength' => 3, 'points' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 2, 'goals_for' => 1, 'goals_against' => 4]);
        $standings = $this->leagueService->standings();
        $this->assertEquals(['A', 'B', 'C'], [$standings[0]->name, $standings[1]->name, $standings[2]->name]);
    }

    public function test_current_week_is_zero_when_no_games()
    {
        $this->assertEquals(0, $this->leagueService->getCurrentWeek());
    }

    public function test_total_weeks_for_four_teams()
    {
        foreach (['A', 'B', 'C', 'D'] as $name) {
            Team::create(['name' => $name, 'strength' => 3, 'points' => 0, 'wins' => 0, 'draws' => 0, 'losses' => 0, 'goals_for' => 0, 'goals_against' => 0]);
        }
        $this->assertEquals(6, $this->leagueService->getTotalWeeks());
    }

    public function test_reset_clears_games_and_stats()
    {
        $team = Team::create(['name' => 'A', 'strength' => 3, 'points' => 6, 'wins' => 2, 'draws' => 0, 'losses' => 0, 'goals_for' => 4, 'goals_against' => 1]);
        Game::create(['team1_id' => $team->id, 'team2_id' => $team->id, 'team1_goals' => 2, 'team2_goals' => 1, 'week' => 1, 'played' => true]);
        $this->assertTrue($this->leagueService->reset());
        $team->refresh();
        $this->assertEquals([0,0,0,0,0,0], [$team->points, $team->wins, $team->draws, $team->losses, $team->goals_for, $team->goals_against]);
        $this->assertEquals(0, Game::count());
    }
} 
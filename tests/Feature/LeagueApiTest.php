<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Team;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeagueApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Team::create(['name' => 'Chelsea', 'strength' => 5]);
        Team::create(['name' => 'Arsenal', 'strength' => 4]);
        Team::create(['name' => 'Manchester City', 'strength' => 4]);
        Team::create(['name' => 'Liverpool', 'strength' => 3]);
    }

    public function test_get_standings()
    {
        $response = $this->getJson('/api/league/standings');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id','name','strength','points','wins','draws','losses','goals_for','goals_against']]]);
        $this->assertCount(4, $response->json('data'));
    }

    public function test_simulate_week_creates_games()
    {
        $response = $this->postJson('/api/league/simulate-week');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['message']]);
        $this->assertEquals(2, Game::count());
        $this->assertEquals(1, Game::max('week'));
    }

    public function test_simulate_all_returns_results()
    {
        $response = $this->postJson('/api/league/simulate-all');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['Week 1','Week 2','Week 3','Week 4','Week 5','Week 6']]);
        $this->assertEquals(12, Game::count());
    }

    public function test_get_games()
    {
        $team1 = Team::first();
        $team2 = Team::find(2);
        Game::create(['team1_id' => $team1->id, 'team2_id' => $team2->id, 'team1_goals' => 2, 'team2_goals' => 1, 'week' => 1, 'played' => true]);
        $response = $this->getJson('/api/league/games');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [['id','team1_id','team2_id','team1_goals','team2_goals','week','played','team1','team2']]]);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_update_game()
    {
        $team1 = Team::first();
        $team2 = Team::find(2);
        $game = Game::create(['team1_id' => $team1->id, 'team2_id' => $team2->id, 'team1_goals' => 2, 'team2_goals' => 1, 'week' => 1, 'played' => true]);
        $response = $this->putJson('/api/league/games', [
            'game_id' => $game->id,
            'team1_goals' => 3,
            'team2_goals' => 0
        ]);
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['message']]);
        $game->refresh();
        $this->assertEquals([3,0], [$game->team1_goals, $game->team2_goals]);
    }

    public function test_get_current_week()
    {
        $response = $this->getJson('/api/league/current-week');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['current_week','total_weeks','remaining_weeks']]);
        $data = $response->json('data');
        $this->assertEquals([0,6,6], [$data['current_week'],$data['total_weeks'],$data['remaining_weeks']]);
    }

    public function test_reset_clears_data()
    {
        $team1 = Team::first();
        $team2 = Team::find(2);
        Game::create(['team1_id' => $team1->id, 'team2_id' => $team2->id, 'team1_goals' => 2, 'team2_goals' => 1, 'week' => 1, 'played' => true]);
        $response = $this->postJson('/api/league/reset');
        $response->assertStatus(200)
            ->assertJsonStructure(['data' => ['message']]);
        $this->assertEquals(0, Game::count());
        $team1->refresh();
        $this->assertEquals([0,0,0,0], [$team1->points, $team1->wins, $team1->draws, $team1->losses]);
    }
} 
<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            ['name' => 'Chelsea', 'strength' => 5],
            ['name' => 'Arsenal', 'strength' => 4],
            ['name' => 'Manchester City', 'strength' => 4],
            ['name' => 'Liverpool', 'strength' => 3],
        ];

        foreach ($teams as $team) {
            Team::create(array_merge($team, [
                'points' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
            ]));
        }
    }
}

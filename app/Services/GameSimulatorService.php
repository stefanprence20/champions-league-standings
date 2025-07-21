<?php

namespace App\Services;

use App\Models\Team;
use App\Repositories\Interfaces\TeamRepositoryInterface;
use App\Repositories\Interfaces\GameRepositoryInterface;

class GameSimulatorService
{
    protected TeamRepositoryInterface $teamRepo;
    protected GameRepositoryInterface $matchRepo;

    public function __construct(
        TeamRepositoryInterface $teamRepo,
        GameRepositoryInterface $matchRepo
    ) {
        $this->teamRepo = $teamRepo;
        $this->matchRepo = $matchRepo;
    }

    /**
     * Simulate a match and update team stats.
     */
    public function simulateMatch(Team $team1, Team $team2, int $week)
    {
        $baseGoals1 = $this->calculateBaseGoals($team1->strength);
        $baseGoals2 = $this->calculateBaseGoals($team2->strength);
        $goals1 = max(0, $baseGoals1 + rand(-1, 1));
        $goals2 = max(0, $baseGoals2 + rand(-1, 1));
        if ($goals1 === 0 && $goals2 === 0) {
            if (rand(1, 10) <= 7) {
                if ($team1->strength > $team2->strength) {
                    $goals1 = 1;
                } else {
                    $goals2 = 1;
                }
            }
        }
        $match = $this->matchRepo->create([
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'team1_goals' => $goals1,
            'team2_goals' => $goals2,
            'week' => $week,
            'played' => true,
        ]);
        $this->updateStats($team1, $team2, $goals1, $goals2);
        return $match;
    }

    /**
     * Update stats for both teams after a match.
     */
    public function updateStats(Team $team1, Team $team2, int $g1, int $g2): void
    {
        $this->adjustTeam($team1, $g1, $g2);
        $this->adjustTeam($team2, $g2, $g1);
        if ($g1 > $g2) {
            $team1->points += 3;
            $team1->wins++;
            $team2->losses++;
        } elseif ($g1 < $g2) {
            $team2->points += 3;
            $team2->wins++;
            $team1->losses++;
        } else {
            $team1->points++;
            $team2->points++;
            $team1->draws++;
            $team2->draws++;
        }
        $this->teamRepo->update($team1, $team1->toArray());
        $this->teamRepo->update($team2, $team2->toArray());
    }

    private function calculateBaseGoals(int $strength): int
    {
        switch ($strength) {
            case 1:
                return rand(0, 1);
            case 2:
                return rand(0, 2);
            case 3:
                return rand(0, 2);
            case 4:
                return rand(1, 3);
            case 5:
                return rand(1, 4);
            default:
                return rand(0, 2);
        }
    }

    private function adjustTeam(Team $team, int $gf, int $ga): void
    {
        $team->goals_for += $gf;
        $team->goals_against += $ga;
    }
}

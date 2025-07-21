<?php

namespace App\Services;

use App\Repositories\Interfaces\TeamRepositoryInterface;
use App\Repositories\Interfaces\GameRepositoryInterface;
use App\Services\GameSimulatorService;
use Throwable;

class LeagueService
{
    protected TeamRepositoryInterface $teamRepo;
    protected GameRepositoryInterface $gameRepo;
    protected GameSimulatorService $simulator;

    private ?string $error = null;

    public function __construct(
        TeamRepositoryInterface $teamRepo,
        GameRepositoryInterface $gameRepo,
        GameSimulatorService $simulator
    ) {
        $this->teamRepo = $teamRepo;
        $this->gameRepo = $gameRepo;
        $this->simulator = $simulator;
    }

    public function getError(): array
    {
        return [$this->error ?? 'Unknown error.', 400];
    }

    /**
     * Get league standings
     */
    public function standings(): mixed
    {
        try {
            $teams = $this->teamRepo->all()->sortByDesc('points')->values();
            $currentWeek = $this->gameRepo->all()->max('week') ?? 0;

            $rawChances = $teams->mapWithKeys(fn($team) => [
                $team->id => $this->calculateChampionshipChance($team, $teams, $currentWeek)
            ]);
            $total = $rawChances->sum();

            $teams = $teams->map(function ($team) use ($rawChances, $total) {
                $team->championship_chance = $total > 0 ? round($rawChances[$team->id] * 100 / $total, 1) : 0.0;
                return $team;
            });
            return $teams;
        } catch (Throwable $e) {
            $this->error = "Failed to get league standings: {$e->getMessage()}";
            return false;
        }
    }

    /**
     * Simulate the next week of matches.
     */
    public function simulateNextWeek(): false|string
    {
        try {
            $teams = $this->teamRepo->all()->all();
            $currentWeek = $this->gameRepo->all()->max('week') ?? 0;
            $nextWeek = $currentWeek + 1;
            $weekMap = $this->generateWeekMatchMap($teams);

            if (!isset($weekMap[$nextWeek])) {
                $this->error = "No matches scheduled for week $nextWeek.";
                return false;
            }

            foreach ($weekMap[$nextWeek] as [$home, $away]) {
                $this->simulator->simulateMatch($home, $away, $nextWeek);
            }

            return "Week $nextWeek simulated";
        } catch (Throwable $e) {
            $this->error = "Simulation failed: {$e->getMessage()}";
            return false;
        }
    }

    /**
     * Simulate all remaining weeks
     */
    public function simulateAll()
    {
        try {
            $teams = $this->teamRepo->all()->all();
            $weekMap = $this->generateWeekMatchMap($teams);
            $currentWeek = $this->gameRepo->all()->max('week') ?? 0;
            $resultsByWeek = [];

            foreach ($weekMap as $week => $matches) {
                if ($week <= $currentWeek) {
                    continue;
                }
                $weekResults = [];
                foreach ($matches as [$home, $away]) {
                    $game = $this->simulator->simulateMatch($home, $away, $week);
                    $weekResults[] = [
                        'home' => $home->name,
                        'away' => $away->name,
                        'score' => "{$game->team1_goals} - {$game->team2_goals}",
                    ];
                }
                $resultsByWeek["Week $week"] = $weekResults;
            }
            return $resultsByWeek;
        } catch (Throwable $e) {
            $this->error = "Simulation failed: {$e->getMessage()}";
            return false;
        }
    }

    /**
     * Get all games with teams loaded.
     */
    public function getGames(): mixed
    {
        try {
            return $this->gameRepo->all()->load(['team1', 'team2'])->sortBy('week');
        } catch (Throwable $e) {
            $this->error = "Failed to load games: {$e->getMessage()}";
            return false;
        }
    }

    /**
     * Update a game's result and adjust stats.
     */
    public function updateGame(int $gameId, int $team1Goals, int $team2Goals): bool
    {
        try {
            $game = $this->gameRepo->find($gameId);
            if (!$game) {
                $this->error = "Game not found";
                return false;
            }
            $this->revertGameStats($game);
            $game->update([
                'team1_goals' => $team1Goals,
                'team2_goals' => $team2Goals,
            ]);
            $this->simulator->updateStats($game->team1, $game->team2, $team1Goals, $team2Goals);
            return true;
        } catch (Throwable $e) {
            $this->error = "Failed to update game: {$e->getMessage()}";
            return false;
        }
    }

    /**
     * Reset all games and team stats.
     */
    public function reset(): bool
    {
        try {
            $this->gameRepo->all()->each->delete();
            $this->teamRepo->all()->each(function ($team) {
                $team->update([
                    'points' => 0,
                    'wins' => 0,
                    'draws' => 0,
                    'losses' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                ]);
            });
            return true;
        } catch (Throwable $e) {
            $this->error = "Reset failed: {$e->getMessage()}";
            return false;
        }
    }

    public function getCurrentWeek(): int
    {
        return $this->gameRepo->all()->max('week') ?? 0;
    }

    public function getTotalWeeks(): int
    {
        $teams = $this->teamRepo->all()->count();
        return ($teams * ($teams - 1)) / 2;
    }

    private function generateWeekMatchMap(array $teams): array
    {
        $weekMap = [];
        $allMatches = [];
        $teamCount = count($teams);
        for ($i = 0; $i < $teamCount; $i++) {
            for ($j = 0; $j < $teamCount; $j++) {
                if ($i !== $j) {
                    $allMatches[] = [$teams[$i], $teams[$j]];
                }
            }
        }
        shuffle($allMatches);
        $week = 1;
        $usedTeamsInWeek = [];
        foreach ($allMatches as $match) {
            [$home, $away] = $match;
            $weekFound = false;
            for ($w = 1; $w <= 6; $w++) {
                if (!isset($usedTeamsInWeek[$w])) {
                    $usedTeamsInWeek[$w] = [];
                }
                if (!in_array($home->id, $usedTeamsInWeek[$w]) && !in_array($away->id, $usedTeamsInWeek[$w])) {
                    if (!isset($weekMap[$w])) {
                        $weekMap[$w] = [];
                    }
                    if (count($weekMap[$w]) < 2) {
                        $weekMap[$w][] = $match;
                        $usedTeamsInWeek[$w][] = $home->id;
                        $usedTeamsInWeek[$w][] = $away->id;
                        $weekFound = true;
                        break;
                    }
                }
            }
            if (!$weekFound) {
                $week++;
                if (!isset($weekMap[$week])) {
                    $weekMap[$week] = [];
                    $usedTeamsInWeek[$week] = [];
                }
                $weekMap[$week][] = $match;
                $usedTeamsInWeek[$week][] = $home->id;
                $usedTeamsInWeek[$week][] = $away->id;
            }
        }
        return $weekMap;
    }

    private function calculateChampionshipChance($team, $teams, $currentWeek): float
    {
        if ($currentWeek === 0) {
            $totalStrength = $teams->sum('strength');
            if ($totalStrength == 0) {
                return 100.0 / max(1, $teams->count());
            }
            return ($team->strength / $totalStrength) * 100;
        }
        $totalPoints = $teams->sum('points');
        if ($totalPoints == 0) {
            $totalStrength = $teams->sum('strength');
            if ($totalStrength == 0) {
                return 100.0 / max(1, $teams->count());
            }
            return ($team->strength / $totalStrength) * 100;
        }
        return ($team->points / $totalPoints) * 100;
    }

    private function revertGameStats($game): void
    {
        $team1 = $game->team1;
        $team2 = $game->team2;
        $team1->goals_for -= $game->team1_goals;
        $team1->goals_against -= $game->team2_goals;
        $team2->goals_for -= $game->team2_goals;
        $team2->goals_against -= $game->team1_goals;
        if ($game->team1_goals > $game->team2_goals) {
            $team1->points -= 3;
            $team1->wins--;
            $team2->losses--;
        } elseif ($game->team1_goals < $game->team2_goals) {
            $team2->points -= 3;
            $team2->wins--;
            $team1->losses--;
        } else {
            $team1->points--;
            $team2->points--;
            $team1->draws--;
            $team2->draws--;
        }
        $this->teamRepo->update($team1, $team1->toArray());
        $this->teamRepo->update($team2, $team2->toArray());
    }
}

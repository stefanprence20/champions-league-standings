<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateGameLeagueRequest;
use App\Services\LeagueService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class LeagueController extends Controller
{
    use ApiResponseTrait;

    protected LeagueService $service;

    public function __construct(LeagueService $service)
    {
        $this->service = $service;
    }

    /**
     * Show the current league standings.
     */
    public function standings(): JsonResponse
    {
        $result = $this->service->standings();
        if (!$result) {
            return $this->generalError(...$this->service->getError());
        }
        return $this->successResponse($result);
    }

    /**
     * Simulate the next week of matches.
     */
    public function simulateWeek(): JsonResponse
    {
        $result = $this->service->simulateNextWeek();
        if (!$result) {
            return $this->generalError(...$this->service->getError());
        }
        return $this->successResponse(['message' => $result]);
    }

    /**
     * Simulate all remaining matches
     */
    public function simulateAll(): JsonResponse
    {
        $result = $this->service->simulateAll();
        if (!$result) {
            return $this->generalError(...$this->service->getError());
        }
        return $this->successResponse($result);
    }

    /**
     * Get all games with results
     */
    public function getGames(): JsonResponse
    {
        $result = $this->service->getGames();
        if (!$result) {
            return $this->generalError(...$this->service->getError());
        }
        return $this->successResponse($result);
    }

    /**
     * Update a game result
     */
    public function updateGame(UpdateGameLeagueRequest $request): JsonResponse
    {
        $data = $request->validated();
        $result = $this->service->updateGame(
            $data['game_id'],
            $data['team1_goals'],
            $data['team2_goals']
        );
        if (!$result) {
            return $this->generalError(...$this->service->getError());
        }
        return $this->successResponse(['message' => 'Game updated successfully']);
    }

    /**
     * Get current week and total weeks info.
     */
    public function getCurrentWeek(): JsonResponse
    {
        $currentWeek = $this->service->getCurrentWeek();
        $totalWeeks = $this->service->getTotalWeeks();
        return $this->successResponse([
            'current_week' => $currentWeek,
            'total_weeks' => $totalWeeks,
            'remaining_weeks' => $totalWeeks - $currentWeek,
        ]);
    }

    /**
     * Reset the league
     */
    public function reset(): JsonResponse
    {
        $result = $this->service->reset();
        if (!$result) {
            return $this->generalError(...$this->service->getError());
        }
        return $this->successResponse(['message' => 'League has been reset.']);
    }
}

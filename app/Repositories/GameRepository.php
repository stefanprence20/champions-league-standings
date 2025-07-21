<?php

namespace App\Repositories;

use App\Models\Game;
use App\Repositories\Interfaces\GameRepositoryInterface;
use Illuminate\Support\Collection;

class GameRepository implements GameRepositoryInterface
{
    public function create(array $data): Game
    {
        return Game::create($data);
    }

    public function find(int $id): ?Game
    {
        return Game::find($id);
    }

    public function getByWeek(int $week): Collection
    {
        return Game::where('week', $week)->get();
    }

    public function all(): Collection
    {
        return Game::all();
    }
}


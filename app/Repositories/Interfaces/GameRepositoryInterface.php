<?php

namespace App\Repositories\Interfaces;

use App\Models\Game;
use Illuminate\Support\Collection;

interface GameRepositoryInterface
{
    public function create(array $data): Game;
    public function getByWeek(int $week): Collection;
    public function all(): Collection;
}

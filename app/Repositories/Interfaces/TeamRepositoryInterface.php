<?php

namespace App\Repositories\Interfaces;

use App\Models\Team;
use Illuminate\Support\Collection;

interface TeamRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Team;
    public function update(Team $team, array $data): void;
}


<?php

namespace App\Repositories;

use App\Models\Team;
use App\Repositories\Interfaces\TeamRepositoryInterface;
use Illuminate\Support\Collection;

class TeamRepository implements TeamRepositoryInterface
{
    /**
     * Get all teams.
     */
    public function all(): Collection
    {
        return Team::all();
    }

    /**
     * Find a team by ID.
     */
    public function find(int $id): ?Team
    {
        return Team::find($id);
    }

    /**
     * Update a team's data.
     */
    public function update(Team $team, array $data): void
    {
        $team->update($data);
    }
}


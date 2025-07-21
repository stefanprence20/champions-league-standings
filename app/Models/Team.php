<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'strength', 'points', 'wins', 'draws', 'losses',
        'goals_for', 'goals_against'
    ];

    public function goalDifference(): int
    {
        return $this->goals_for - $this->goals_against;
    }
}


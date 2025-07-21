<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGameLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => ['required', 'integer'],
            'team1_goals' => ['required', 'integer', 'min:0'],
            'team2_goals' => ['required', 'integer', 'min:0'],
        ];
    }
} 
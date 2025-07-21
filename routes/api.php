<?php

use App\Http\Controllers\Api\LeagueController;
use Illuminate\Support\Facades\Route;

Route::prefix('league')->group(function () {
    Route::get('/standings', [LeagueController::class, 'standings']);
    Route::post('/simulate-week', [LeagueController::class, 'simulateWeek']);
    Route::post('/simulate-all', [LeagueController::class, 'simulateAll']);
    Route::get('/games', [LeagueController::class, 'getGames']);
    Route::put('/games', [LeagueController::class, 'updateGame']);
    Route::get('/current-week', [LeagueController::class, 'getCurrentWeek']);
    Route::post('/reset', [LeagueController::class, 'reset']);
});

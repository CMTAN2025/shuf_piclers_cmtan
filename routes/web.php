<?php

use App\Http\Controllers\MatchController;
use App\Http\Controllers\PlayerController;

// UI pages
Route::get('/', [PlayerController::class, 'index']);
Route::get('/matches', [MatchController::class, 'matchesView']);
Route::get('/leaderboard', [MatchController::class, 'leaderboardView']);

// Players API
Route::post('/players', [PlayerController::class, 'store']);
Route::delete('/players/{player}', [PlayerController::class, 'destroy']);
Route::get('/players/{player}/history', [MatchController::class, 'ratingHistory']);

// Legacy shuffle
Route::post('/shuffle', [PlayerController::class, 'shuffle']);

// Matchmaking API
Route::post('/pairings', [MatchController::class, 'generatePairings']);
Route::post('/matches/{match}/result', [MatchController::class, 'submitResult']);

// JSON API
Route::get('/api/matches', [MatchController::class, 'index']);
Route::get('/api/leaderboard', [MatchController::class, 'leaderboard']);

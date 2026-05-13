<?php

use App\Http\Controllers\PlayerController;

Route::get('/', [PlayerController::class, 'index']);
Route::post('/players', [PlayerController::class, 'store']);
Route::post('/shuffle', [PlayerController::class, 'shuffle']);
Route::delete('/players/{player}', [PlayerController::class, 'destroy']);

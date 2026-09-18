<?php
use App\Http\Controllers\StatsController;
use App\Http\Controllers\DpsController;
Route::get('/', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'TERA DPS Meter Database API is running.'
    ]);
});
Route::get('/stats', [StatsController::class, 'index']);
Route::get('/dps', [DpsController::class, 'index']);

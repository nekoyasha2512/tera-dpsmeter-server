<?php
use App\Http\Controllers\StatsController;
Route::get('/', function () {
    return response()->json([
        'status' => 'online',
        'message' => 'TERA DPS Meter Database API is running.'
    ]);
});
Route::get('/stats', [StatsController::class, 'index']);

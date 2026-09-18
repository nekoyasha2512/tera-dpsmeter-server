<?php
use App\Http\Controllers\StatsController;

Route::get('/stats', [StatsController::class, 'index']);

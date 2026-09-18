<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\GlyphController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
Route::get('/', 'StatsController@index');
Route::post('/dps', 'StatsController@store');
Route::post('/glyph', 'GlyphController@store');

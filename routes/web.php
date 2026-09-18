<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'StatsController@index');
Route::post('/dps', 'StatsController@store');
Route::post('/glyph', 'GlyphController@store');

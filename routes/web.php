<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'DpsController@overviewPage');
// 接收 TERA DPS Meter 戰鬥數據的 POST 路由
Route::post('/dps', 'DpsController@store');
Route::get('/dps', function () {
    return response()->json(['status' => 'DPS Server Active']);
});

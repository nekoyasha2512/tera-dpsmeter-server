<?php

use Illuminate\Support\Facades\Route;

// 首頁 GET 請求顯示網頁
Route::get('/', function () {
    return view('welcome');
});

// 接收 TERA DPS Meter 的 POST 上傳請求（直接對應根目錄）
Route::post('/', 'DpsController@store');

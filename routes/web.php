<?php

use App\Stat;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'DpsController@overviewPage');

Route::get('/encounter/{stat}', function (Stat $stat) {
    return view('encounter', ['stat' => $stat]);
})->name('statDetail');

Route::get('/shared/servertime', function () {
    return response()->json(['serverTime' => time()]);
});
Route::get('/init-db', function () {
    try {
        // 強制清除快取
        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');
        
        // 強制執行 Migration 建表
        \Artisan::call('migrate', ['--force' => true]);
        
        return '<h1>資料庫連線與 Migration 成功！</h1><pre>' . \Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<h1>失敗原因：</h1><pre>' . $e->getMessage() . '</pre>';
    }
});

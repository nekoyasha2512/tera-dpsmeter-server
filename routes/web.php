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
Route::get('/init-db', function () {
    try {
        // 動態覆蓋連線設定，確保讀取 Render 環境變數
        config([
            'database.connections.mysql.host' => env('DB_HOST'),
            'database.connections.mysql.port' => env('DB_PORT'),
            'database.connections.mysql.database' => env('DB_DATABASE'),
            'database.connections.mysql.username' => env('DB_USERNAME'),
            'database.connections.mysql.password' => env('DB_PASSWORD'),
        ]);

        \Artisan::call('config:clear');
        \Artisan::call('cache:clear');
        \Artisan::call('migrate', ['--force' => true]);

        return '<h1>資料庫建表成功！</h1><pre>' . \Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return '<h1>失敗原因：</h1><pre>' . $e->getMessage() . '</pre>';
    }
});

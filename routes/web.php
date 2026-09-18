<?php

use Illuminate\Support\Facades\Route;

Route::get('/init-db', function () {
    $host = 'mysql-30976c8f-rneko-tera-dps-database.i.aivencloud.com';
    $port = 16885;
    $db   = 'defaultdb';
    $user = 'avnadmin';
    $pass = 'AVNS_M21uXaWU19m7ty5wHd9';

    try {
        \DB::purge('mysql');
        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'driver' => 'mysql',
                'host' => $host,
                'port' => $port,
                'database' => $db,
                'username' => $user,
                'password' => $pass,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => false,
                'options' => [
                    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                ],
            ]
        ]);
        \DB::reconnect('mysql');

        // 關鍵：關閉 Aiven 的主鍵強制要求，允許 password_resets 建表
        \DB::statement('SET SESSION sql_require_primary_key = 0;');

        // 執行 Migration 建表
        \Artisan::call('migrate', ['--force' => true]);

        return '<h1>恭喜！Aiven 所有資料表建立成功！</h1><pre>' . \Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return "<h1>Laravel Migration 失敗：</h1><pre>" . $e->getMessage() . "</pre>";
    }
});

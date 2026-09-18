<?php

use Illuminate\Support\Facades\Route;

Route::get('/init-db', function () {
    $host = 'mysql-30976c8f-rneko-tera-dps-database.i.aivencloud.com';
    $port = 16885;
    $db   = 'defaultdb';
    $user = 'avnadmin';
    $pass = 'AVNS_M21uXaWU19m7ty5wHd9';

    // 建立 PDO 連線並關閉 SSL 憑證核對 (ALLOW UNSAFE SSL)
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // 停用伺服器憑證嚴格檢查
        ]);
    } catch (\PDOException $e) {
        return "<h1>Aiven PDO 連線失敗！</h1><pre>" . $e->getMessage() . "</pre>";
    }

    // 注入連線並執行 Migration 建表
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

        \Artisan::call('migrate', ['--force' => true]);

        return '<h1>Aiven 資料庫 Migration 建表成功！</h1><pre>' . \Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return "<h1>Laravel Migration 失敗：</h1><pre>" . $e->getMessage() . "</pre>";
    }
});

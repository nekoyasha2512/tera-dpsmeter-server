<?php

use Illuminate\Support\Facades\Route;

Route::get('/init-db', function () {
    $host = 'mysql-30976c8f-rneko-tera-dps-database.i.aivencloud.com';
    $port = 16885;
    $db   = 'defaultdb';
    $user = 'avnadmin';
    $pass = 'AVNS_M21uXaWU19m7ty5wHd9';

    // 1. 先用原生 PHP 測試 TCP 埠號通訊
    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    if (!$connection) {
        return "<h1>TCP 連線失敗！</h1><p>無法連線至 {$host}:{$port}</p><p>錯誤訊息：{$errstr} ({$errno})</p>";
    }
    fclose($connection);

    // 2. 用原生 PDO 測試 Aiven 認證
    try {
        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => true, // 忽略或啟用 SSL
        ]);
    } catch (\PDOException $e) {
        return "<h1>Aiven PDO 連線失敗！</h1><pre>" . $e->getMessage() . "</pre>";
    }

    // 3. 強制注入至 Laravel PDO 執行 Migration
    try {
        // 動態更換框架的預設連線物件
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
            ]
        ]);
        \DB::reconnect('mysql');

        \Artisan::call('migrate', ['--force' => true]);

        return '<h1>原生 PDO 與 Migration 均執行成功！</h1><pre>' . \Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return "<h1>Laravel Migration 失敗：</h1><pre>" . $e->getMessage() . "</pre>";
    }
});

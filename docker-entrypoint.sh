#!/bin/sh
set -e

echo "===> 正在初始化 Tera DPS Server 容器環境..."

# 1. 重新產生 Package Manifest 與 Autoload 映射 (解決 PackageManifest 報錯)
echo "===> 重新建立 Composer Autoload 映射..."
composer dump-autoload --optimize --no-dev

# 2. 清除舊有快取 (加上 || true 避免快取檔不存在時中斷)
echo "===> 清除舊有設定與路由快取..."
php artisan config:clear || true
php artisan route:clear || true
php artisan cache:clear || true

# 3. 自動執行資料庫 Migration
echo "===> 執行資料庫 Migration..."
php artisan migrate --force || echo "警告: 資料庫 Migration 執行失敗，請檢查 DB 連線設定。"

echo "===> 初始化完成，啟動 Web Server..."

# 執行 Dockerfile CMD 預設指令
exec "$@"

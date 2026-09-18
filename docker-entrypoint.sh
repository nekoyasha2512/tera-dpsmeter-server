#!/bin/sh
set -e

echo "===> 正在初始化 Tera DPS Server 容器環境..."

# 1. 僅清除舊快取，不重構 Composer Autoload 檔案
php artisan config:clear || true
php artisan route:clear || true
php artisan cache:clear || true

# 2. 自動執行 Migration
php artisan migrate --force || echo "警告: 資料庫 Migration 執行失敗。"

# 3. 寫入正式環境快取，確保效能與隱藏警告生效
php artisan config:cache || true
php artisan route:cache || true

echo "===> 初始化完成，啟動 Web Server..."
exec "$@"

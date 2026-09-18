#!/bin/sh
set -e

echo "===> 正在初始化 Tera DPS Server 容器環境..."

# 1. 強制清除舊快取，確保重新讀取 Render 傳入的真實環境變數
echo "===> 清除舊有設定與路由快取..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 2. 自動執行資料庫 Migration（建立/更新資料表）
echo "===> 執行資料庫 Migration..."
php artisan migrate --force || echo "警告: 資料庫 Migration 執行失敗，請檢查 DB 連線設定。"

# 3. 重新建立路由與設定快取以優化執行效能
echo "===> 建立正式環境快取..."
php artisan config:cache
php artisan route:cache

echo "===> 初始化完成，啟動 Web Server..."

# 執行 Dockerfile 中 CMD 所定義的預設啟動指令 (例如 apache2-foreground)
exec "$@"

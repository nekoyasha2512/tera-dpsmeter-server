#!/bin/sh
set -e

# 清除打包階段殘留的靜態 Config 快取（讓它重新讀取 Render 的動態 ENV）
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 啟動主服務 (如 Apache / PHP-FPM / Artisan Serve)
exec "$@"

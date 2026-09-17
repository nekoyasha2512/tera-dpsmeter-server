#A Dockerfile for Render
# 使用自帶 Composer 的 PHP 8.1 映像檔
FROM php:8.1-fpm-alpine as base

# 安裝系統依賴與 PHP 擴充功能（Laravel、MySQL、PostgreSQL 驅動）
RUN apk add --no-base-layout --no-cache \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd

# 複製 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 複製專案檔案
COPY . .

# 執行安裝指令（包含處理子模組 icons 與 key 生成）
RUN composer install --no-dev -o \
    && cp .env.example .env \
    && php artisan key:generate \
    && mkdir -p public/img/icons \
    && if [ -f "teradata/icons.zip" ]; then unzip teradata/icons.zip -d public/img/icons/; fi \
    && if [ -d "teradata/class-icons" ]; then ln -s ../../teradata/class-icons public/img/class-icons; fi

# Render 的 Web Service 需要對外提供 Port，我們用 PHP 內建伺服器跑在 10000 埠口
EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000

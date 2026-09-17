# A Dockerfile for Render
# 降級使用最相容舊版專案的 PHP 7.4 映像檔
FROM php:7.4-fpm-alpine as base

# 安裝舊版專案所需的系統依賴與 PHP 擴充功能
RUN apk add --no-cache \
    git \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd

# 複製與 PHP 7.4 相容的 Composer 版本
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 複製專案檔案
COPY . .

# 執行安裝指令，並強制加上 --no-scripts 避免舊版 Laravel 提前報錯
RUN composer install --no-dev -o --no-scripts \
    && cp .env.example .env \
    && php artisan key:generate \
    && mkdir -p public/img/icons \
    && if [ -f "teradata/icons.zip" ]; then unzip teradata/icons.zip -d public/img/icons/; fi \
    && if [ -d "teradata/class-icons" ]; then ln -s ../../teradata/class-icons public/img/class-icons; fi

# Render 的 Web Service 埠口對接設定
EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000

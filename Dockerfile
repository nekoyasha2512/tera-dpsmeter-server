#A Dockerfile for Render
# 使用最相容舊版專案的 PHP 7.4 映像檔
FROM php:7.4-fpm-alpine as base

# 安裝系統基本依賴
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

# 執行安裝指令，並妥善處理資料夾建立與解壓縮警告
RUN composer install --no-dev -o --no-scripts \
    && cp .env.example .env \
    && php artisan key:generate \
    && mkdir -p public/img/icons \
    && if [ -f "teradata/icons.zip" ]; then unzip -o teradata/icons.zip -d public/img/icons/ || true; fi \
    && if [ -d "teradata/class-icons" ]; then ln -s ../../teradata/class-icons public/img/class-icons || true; fi

# Render 的 Web Service 埠口對接設定
EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000

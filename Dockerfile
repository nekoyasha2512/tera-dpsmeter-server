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

# 步驟 1：建立基礎設定檔
RUN cp .env.example .env

# 步驟 2：忽略平台限制強制安裝 Composer 依賴套件
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# 步驟 3：產生金鑰
RUN php artisan key:generate

# 步驟 4：處理圖標解壓縮（獨立執行，絕對不影響前後步驟）
RUN mkdir -p public/img/icons
RUN if [ -f "teradata/icons.zip" ]; then unzip -o teradata/icons.zip -d public/img/icons/ || true; fi
RUN if [ -d "teradata/class-icons" ]; then ln -s ../../teradata/class-icons public/img/class-icons || true; fi

# Render 的 Web Service 埠口對接設定
EXPOSE 10000
CMD php artisan serve --host=0.0.0.0 --port=10000

# 使用帶有 Apache 的官方 PHP 8.2 映像檔
FROM php:8.2-apache

# 1. 安裝系統依賴套件與 PHP 擴充套件 (MySQL PDO, Zip, GD, OPcache 等)
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache
RUN echo "opcache.validate_timestamps=1" >> /usr/local/etc/php/conf.d/docker-php-ext-opcache.ini
# 2. 啟用 Apache mod_rewrite 模組 (Laravel 網址重寫必須)
RUN a2enmod rewrite
# 套用 PHP 正式環境設定 (關閉 display_errors)
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && sed -i 's/display_errors = On/display_errors = Off/g' "$PHP_INI_DIR/php.ini" \
    && sed -i 's/error_reporting = E_ALL/error_reporting = E_ALL \& ~E_DEPRECATED \& ~E_STRICT/g' "$PHP_INI_DIR/php.ini"
# 3. 修改 Apache Document Root 指向 Laravel 的 public 目錄
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/conf-available/*.conf

# 4. 安裝 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. 設定工作目錄
WORKDIR /var/www/html

# 6. 複製專案檔案並安裝 Composer 依賴套件
COPY . .

RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 7. 設定 Laravel 相關目錄權限
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 8. 複製並配置啟動腳本 (docker-entrypoint.sh)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 暴露 Render 預設的 80 Port
EXPOSE 80

# 指定 Entrypoint 與預設啟動命令
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

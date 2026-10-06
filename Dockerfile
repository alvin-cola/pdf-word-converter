FROM php:8.3-apache

# LibreOffice does the actual document conversion (headless)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libreoffice-writer libreoffice-draw fonts-dejavu fonts-liberation \
        unzip git libzip-dev \
    && docker-php-ext-install zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Serve Laravel's /public folder
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN printf "upload_max_filesize=20M\npost_max_size=25M\nmax_execution_time=180\nmemory_limit=512M\n" \
    > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && cp -n .env.example .env \
    && php artisan key:generate --force \
    && mkdir -p storage/app/conversions storage/app/livewire-tmp \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    SESSION_DRIVER=file \
    CACHE_STORE=file \
    QUEUE_CONNECTION=sync

EXPOSE 80

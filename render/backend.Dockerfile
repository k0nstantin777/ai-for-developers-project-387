FROM php:8.5-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    sqlite-dev \
    git \
    unzip \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/cache/apk/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY render/nginx.conf /etc/nginx/http.d/default.conf
COPY render/supervisord.conf /etc/supervisord.conf
COPY render/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY src/backend /app

WORKDIR /app

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p /app/storage/logs \
    && mkdir -p /app/storage/framework/views \
    && mkdir -p /app/storage/framework/cache \
    && mkdir -p /app/storage/framework/sessions \
    && chown -R www-data:www-data /app/storage \
    && chown -R www-data:www-data /app/database

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]

FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libpq-dev libzip-dev zip unzip git \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql bcmath zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Autorise les fichiers volumineux (objectif : 1 Go)
RUN { \
    echo "upload_max_filesize=1024M"; \
    echo "post_max_size=1024M"; \
    echo "memory_limit=512M"; \
    echo "max_execution_time=300"; \
    } > /usr/local/etc/php/conf.d/datashare.ini

WORKDIR /var/www/html

EXPOSE 8000
CMD ["sh", "-c", "composer install --no-interaction && php artisan serve --host=0.0.0.0 --port=8000"]

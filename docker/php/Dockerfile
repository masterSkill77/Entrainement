FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip git curl libzip-dev libonig-dev libpng-dev libicu-dev \
    mariadb-client \
    && docker-php-ext-install pdo pdo_mysql zip mbstring intl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiez votre entrypoint après l'installation des dépendances
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

FROM php:8.2-fpm

# Installer dépendances système
RUN apt-get update && apt-get install -y unzip git zip curl libicu-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Installer Symfony CLI (vérifier le chemin)
RUN curl -sS https://get.symfony.com/cli/installer | bash

# Ajouter symfony CLI au PATH (selon install, souvent ~/.symfony/bin)
ENV PATH="$PATH:/root/.symfony/bin"

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000

CMD ["symfony", "serve", "--no-tls", "--port=8000", "--dir=public"]

FROM php:8.2-fpm

USER root

# Installer les dépendances
RUN apt-get update && apt-get install -y \
    unzip git zip curl libicu-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
 && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql zip opcache \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    apt-get update && apt-get install -y symfony-cli && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Travailler dans /app
WORKDIR /app
COPY . .

# Créer utilisateur non-root
RUN useradd -m appuser && chown -R appuser:appuser /app
USER appuser

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN composer install --no-dev --optimize-autoloader

# Exposer le port
EXPOSE 8000

# Lancer le serveur interne PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

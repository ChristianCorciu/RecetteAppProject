FROM php:8.2-fpm

USER root

# Installer les dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    unzip git zip curl libicu-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
 && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql zip opcache \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer globalement
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Symfony CLI (optionnel si tu ne t’en sers pas en prod)
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    apt-get update && apt-get install -y symfony-cli && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .

# Créer un utilisateur non-root
RUN useradd -m appuser && chown -R appuser:appuser /app
USER appuser

ENV APP_ENV=prod

# Installer les dépendances PHP sans dev
RUN rm -rf var/cache/* && composer install --no-dev --optimize-autoloader

# Exposer le port (utilisé pour le serveur web)
EXPOSE 8000

# Démarrer le serveur Symfony sur 0.0.0.0:$PORT (important pour Render)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/index.php"]


FROM php:8.2-fpm

# Utiliser root pour l'installation des dépendances
USER root

# Installer dépendances système
RUN apt-get update && apt-get install -y \
    unzip git zip curl libicu-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libssl-dev \
 && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql zip opcache \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    apt-get update && apt-get install -y symfony-cli && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Créer dossier de l'app
WORKDIR /app

# Copier les fichiers du projet
COPY . .

# Créer un utilisateur non-root
RUN useradd -m appuser && chown -R appuser:appuser /app
USER appuser

# Variables d'environnement
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV PORT=10000

# Installer les dépendances PHP sans dev
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Nettoyer le cache Symfony pour l’environnement de prod
RUN php bin/console cache:clear --env=prod && php bin/console cache:warmup --env=prod

# Exposer le port attendu par Render
EXPOSE 10000

# Lancer le serveur PHP intégré (Render attend :0.0.0.0:$PORT)
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]

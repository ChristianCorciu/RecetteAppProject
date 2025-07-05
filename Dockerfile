FROM php:8.2-fpm

USER root

# Installer les dépendances système et extensions PHP nécessaires, dont pdo_pgsql
RUN apt-get update && apt-get install -y \
    unzip git zip curl libicu-dev libonig-dev libxml2-dev libzip-dev libpq-dev \
 && docker-php-ext-install intl pdo pdo_mysql pdo_pgsql zip opcache \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer globalement
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installer Symfony CLI via le dépôt officiel
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash && \
    apt-get update && apt-get install -y symfony-cli && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Créer et configurer le dossier de l'app
WORKDIR /app
COPY . .

# Créer un utilisateur non-root et donner la propriété du dossier /app
RUN useradd -m appuser && chown -R appuser:appuser /app

# Passer à l'utilisateur non-root
USER appuser

# Installer les dépendances PHP avec Composer (sans dev, optimisé)
RUN rm -rf var/cache/* \
 && composer install --no-dev --optimize-autoloader


# Exposer le port 8000
EXPOSE 8000

# Commande de démarrage
CMD ["symfony", "serve", "--no-tls", "--port=8000", "--dir=public"]

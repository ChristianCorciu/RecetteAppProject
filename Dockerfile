FROM php:8.1-cli

# Installer les dépendances système utiles
RUN apt-get update && apt-get install -y unzip git zip

# Installer Composer globalement
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Copier les fichiers de ton projetsss
WORKDIR /app
COPY . .

# Installer les dépendances PHP (sans dev)
RUN composer install --no-dev --optimize-autoloader

# Commande par défaut, à adapter selon ton projet
CMD ["php", "bin/console", "server:run", "0.0.0.0:8000"]

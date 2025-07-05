# Image de base avec PHP, Apache et extensions nécessaires
FROM php:8.1-apache

# Activer les extensions PHP requises
RUN apt-get update && apt-get install -y \
    unzip \
    libicu-dev \
    libpq-dev \
    git \
    zip \
    && docker-php-ext-install pdo pdo_mysql intl opcache

# Activer mod_rewrite (Symfony en a besoin)
RUN a2enmod rewrite

# Copier le code de l'application
COPY . /var/www/html

# Définir le répertoire de travail
WORKDIR /var/www/html

# Installer Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Installer les dépendances Symfony (sans dev)
RUN composer install --no-dev --optimize-autoloader

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

# Configurer Apache pour Symfony (public/ comme racine)
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Exposer le port Apache
EXPOSE 80

# Lancer Apache
CMD ["apache2-foreground"]

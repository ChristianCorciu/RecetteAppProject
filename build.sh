#!/usr/bin/env bash

# Installer Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Installer les dépendances PHP
composer install --no-dev --optimize-autoloader

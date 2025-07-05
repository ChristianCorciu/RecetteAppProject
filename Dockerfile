FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y unzip git zip curl libicu-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash

# Add Symfony CLI to PATH for all users (move it to /usr/local/bin)
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /app
COPY . .

# Create a non-root user and give ownership of /app
RUN useradd -m appuser && chown -R appuser:appuser /app

# Switch to non-root user
USER appuser

# Run composer install as non-root user
RUN composer install --no-dev --optimize-autoloader

EXPOSE 8000

# Run Symfony server as non-root user
CMD ["symfony", "serve", "--no-tls", "--port=8000", "--dir=public"]

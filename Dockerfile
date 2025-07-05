FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    unzip git zip curl libicu-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && rm -rf /var/lib/apt/lists/*

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Symfony CLI directly to /usr/local/bin
RUN curl -sSLo /usr/local/bin/symfony https://get.symfony.com/cli/installer \
    && chmod +x /usr/local/bin/symfony

# Create app directory and set permissions
WORKDIR /app
COPY . .

# Create a non-root user and give ownership of /app
RUN useradd -m appuser && chown -R appuser:appuser /app

# Switch to non-root user
USER appuser

# Install PHP dependencies via Composer (no dev, optimized autoloader)
RUN composer install --no-dev --optimize-autoloader

# Expose port 8000
EXPOSE 8000

# Run Symfony server on container start
CMD ["symfony", "serve", "--no-tls", "--port=8000", "--dir=public"]

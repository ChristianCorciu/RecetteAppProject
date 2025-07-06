#!/usr/bin/env bash

set -e

echo "🧰 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🧹 Clearing and warming up Symfony cache..."
php bin/console cache:clear
php bin/console cache:warmup

echo "🔐 Checking CSRF and secrets..."
php bin/console secrets:decrypt-to-env --force --env=prod || true

echo "📦 Installing and building frontend assets (if Webpack Encore is used)..."
if [ -f yarn.lock ]; then
    yarn install
    yarn encore production
fi

echo "🚀 Build completed for Render!"

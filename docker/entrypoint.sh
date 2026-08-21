#!/bin/sh
set -e

echo "🚀 Starting Pilketos application..."

# Wait for database file to be accessible
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "📁 Creating SQLite database file..."
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
    chmod 664 /var/www/html/database/database.sqlite
fi

# Ensure proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/database \
    /var/www/html/public/storage

chmod -R 775 \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache \
    /var/www/html/database

# Create storage symlink if it doesn't exist
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Creating storage symlink..."
    php artisan storage:link --force
fi

# Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force --no-interaction

# Cache configuration for production
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Application ready!"

# Execute the main command
exec "$@"

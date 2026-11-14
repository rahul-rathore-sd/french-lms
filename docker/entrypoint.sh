#!/bin/bash
set -e

# Default PORT to 80 if not set by Render
PORT="${PORT:-80}"
echo "==> Configuring Académie de Français for PORT ${PORT} on Render..."

# Configure Apache listening port
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:.*>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/*.conf

# Ensure required storage and database directories exist with permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/database

# Setup SQLite database if needed
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "==> Initializing SQLite database..."
    touch /var/www/html/database/database.sqlite
fi

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod 664 /var/www/html/database/database.sqlite

# Run Database Migrations
echo "==> Running Database Migrations..."
php artisan migrate --force

# Run Database Seed if no users exist
USER_COUNT=$(php -r "require '/var/www/html/vendor/autoload.php'; \$app = require_once '/var/www/html/bootstrap/app.php'; \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class); \$kernel->bootstrap(); echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "==> Seeding Classical Curriculum, Users, and Vocabulary..."
    php artisan db:seed --force
else
    echo "==> Database already populated with $USER_COUNT users. Skipping seed."
fi

# Optimize Laravel for Production
echo "==> Caching Configuration and Routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Launching Apache Web Server on port ${PORT}..."
exec apache2-foreground

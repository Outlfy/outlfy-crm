#!/bin/bash

# Wait for database to be ready
echo "Waiting for database connection..."
while ! php -r "try { new PDO('mysql:host=db;dbname=outlfy_crm', 'outlfy_user', 'secret'); echo 'connected'; } catch (PDOException \$e) { exit(1); }" > /dev/null 2>&1; do
  echo "Database connection not available, waiting..."
  sleep 2
done
echo "Database connection established"

# Generate application key if not already set
if grep -q "APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" .env; then
  php artisan key:generate --force
  echo "Application key generated"
else
  echo "Application key already exists"
fi

# Run migrations
php artisan migrate --force
echo "Database migrations completed"

# Clear cache
php artisan optimize:clear
echo "Cache cleared"

# Start PHP-FPM
php-fpm

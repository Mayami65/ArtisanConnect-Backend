#!/usr/bin/env bash
set -e

echo "Starting deployment script..."

# 1. Create the firebase-credentials.json from the environment variable
if [ ! -z "$FIREBASE_CREDENTIALS_JSON" ]; then
    echo "Writing Firebase credentials to file..."
    echo "$FIREBASE_CREDENTIALS_JSON" > /var/www/html/firebase-credentials.json
else
    echo "Warning: FIREBASE_CREDENTIALS_JSON is not set!"
fi

# 2. Run Laravel optimizations and migrations
echo "Running Laravel artisan commands..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

echo "Startup script completed successfully!"

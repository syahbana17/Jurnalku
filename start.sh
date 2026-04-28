#!/bin/bash
set -e

PORT=${PORT:-8080}

cd /var/www/html

# Generate .env dari Railway environment variables
cat > .env <<EOF
APP_NAME="${APP_NAME:-Jurnalku}"
APP_ENV=production
APP_KEY="${APP_KEY}"
APP_DEBUG=false
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_DRIVER=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

GOOGLE_CLIENT_ID="${GOOGLE_CLIENT_ID}"
GOOGLE_CLIENT_SECRET="${GOOGLE_CLIENT_SECRET}"
GOOGLE_REDIRECT_URI="${GOOGLE_REDIRECT_URI}"
EOF

# Laravel setup
php artisan key:generate --force
php artisan config:cache
echo "=== Running migrations ==="
php artisan migrate --force --verbose
echo "=== Migrations done ==="
php artisan storage:link 2>/dev/null || true

echo "Starting Laravel on port ${PORT}..."
# Jalankan scheduler di background
php artisan schedule:work &
exec php artisan serve --host=0.0.0.0 --port=${PORT}

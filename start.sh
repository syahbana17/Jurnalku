#!/bin/bash

PORT=${PORT:-8080}

cd /var/www/html

# Generate .env
cat > .env <<EOF
APP_NAME="${APP_NAME:-Jurnalku}"
APP_ENV=production
APP_KEY="${APP_KEY}"
APP_DEBUG=true
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL=debug

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

FONNTE_TOKEN="${FONNTE_TOKEN:-}"
EOF

echo "=== ENV created ==="
echo "PORT: ${PORT}"
echo "DB_HOST: ${DB_HOST}"
echo "APP_KEY length: ${#APP_KEY}"

# Clear cache
php artisan config:clear 2>&1 || true
php artisan cache:clear 2>&1 || true

# Generate key
php artisan key:generate --force 2>&1

# Cache config
php artisan config:cache 2>&1

# Migrate
echo "=== Migrating ==="
php artisan migrate --force 2>&1 || echo "Migration warning (non-fatal)"

# Storage
php artisan storage:link 2>/dev/null || true

echo "=== Starting on port ${PORT} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT}

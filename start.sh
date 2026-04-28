#!/bin/bash

PORT=${PORT:-3000}

cd /var/www/html

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

php artisan config:clear || true
php artisan key:generate --force || true
php artisan config:cache || true
php artisan migrate --force || true
php artisan storage:link || true

echo "=== STARTING SERVER ON PORT ${PORT} ==="
php artisan serve --host=0.0.0.0 --port=${PORT}

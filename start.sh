#!/bin/bash
set -e

PORT=${PORT:-8080}

cd /var/www/html

# Generate .env dari environment variables Railway
cat > .env <<EOF
APP_NAME="${APP_NAME:-Jurnalku}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
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

# Configure php-fpm unix socket
sed -i 's|listen = 127.0.0.1:9000|listen = /var/run/php-fpm.sock|g' /usr/local/etc/php-fpm.d/www.conf
echo "listen.owner = www-data" >> /usr/local/etc/php-fpm.d/www.conf
echo "listen.group = www-data" >> /usr/local/etc/php-fpm.d/www.conf
echo "listen.mode = 0660" >> /usr/local/etc/php-fpm.d/www.conf

# Nginx config
cat > /etc/nginx/sites-available/default <<NGINX
server {
    listen ${PORT};
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
NGINX

# Laravel setup
php artisan key:generate --force
php artisan config:cache
php artisan migrate --force
php artisan storage:link 2>/dev/null || true

# Start services
php-fpm -D
sleep 1

echo "Starting on port ${PORT}..."
nginx -g 'daemon off;'

#!/bin/bash
set -e

# Use Railway's PORT or default to 8080
PORT=${PORT:-8080}

# Write nginx config with dynamic port
cat > /etc/nginx/sites-available/default <<EOF
server {
    listen ${PORT};
    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

# Laravel setup
php artisan key:generate --force 2>/dev/null || true
php artisan config:cache
php artisan migrate --force
php artisan storage:link 2>/dev/null || true

# Start php-fpm in background
php-fpm -D

# Start nginx in foreground
echo "Starting on port ${PORT}..."
nginx -g 'daemon off;'

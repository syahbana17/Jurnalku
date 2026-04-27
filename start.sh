#!/bin/bash
set -e

PORT=${PORT:-8080}

# Configure php-fpm to use unix socket
sed -i 's|listen = 127.0.0.1:9000|listen = /var/run/php-fpm.sock|g' /usr/local/etc/php-fpm.d/www.conf
sed -i 's|;listen.owner = www-data|listen.owner = www-data|g' /usr/local/etc/php-fpm.d/www.conf
sed -i 's|;listen.group = www-data|listen.group = www-data|g' /usr/local/etc/php-fpm.d/www.conf
sed -i 's|;listen.mode = 0660|listen.mode = 0660|g' /usr/local/etc/php-fpm.d/www.conf

# Write nginx config using unix socket
cat > /etc/nginx/sites-available/default <<EOF
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

    location ~ /\.ht {
        deny all;
    }
}
EOF

cd /var/www/html

# Clear cached config
php artisan config:clear 2>/dev/null || true
php artisan key:generate --force 2>/dev/null || true
php artisan config:cache
php artisan migrate --force
php artisan storage:link 2>/dev/null || true

# Start php-fpm in background
php-fpm -D

# Wait for socket
sleep 1

echo "Starting nginx on port ${PORT}..."
nginx -g 'daemon off;'

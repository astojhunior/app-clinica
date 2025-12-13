# Build stage
FROM php:8.2-fpm-bullseye as builder

RUN apt-get update && apt-get install -y \
    git curl libzip-dev zip unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo mbstring zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . /app

RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Production stage
FROM php:8.2-fpm-bullseye

RUN apt-get update && apt-get install -y \
    nginx bash curl libzip5 \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo mbstring zip

RUN mkdir -p /app /var/run/nginx /var/run/php-fpm /app/storage/logs /app/bootstrap/cache

WORKDIR /app

COPY --from=builder --chown=www-data:www-data /app /app

RUN chown -R www-data:www-data /app && chmod -R 755 /app/storage /app/bootstrap/cache

# PHP-FPM config
RUN cat > /usr/local/etc/php-fpm.d/zz-custom.conf << 'PHPEOF'
[www]
user = www-data
group = www-data
listen = 127.0.0.1:9000
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 10
pm.start_servers = 3
pm.min_spare_servers = 2
pm.max_spare_servers = 5
clear_env = no
PHPEOF

# Nginx config - crear archivo separado
RUN cat > /etc/nginx/conf.d/laravel.conf << 'NGINXEOF'
server {
listen 80 default_server;
server_name _;
root /app/public;
index index.php;

client_max_body_size 20M;
access_log /var/log/nginx/access.log;
error_log /var/log/nginx/error.log warn;

gzip on;
gzip_types text/plain text/css text/xml application/json application/javascript;

location = /health {
access_log off;
return 200 "OK\n";
add_header Content-Type text/plain;
}

location ~ /\. {
deny all;
access_log off;
}

location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|eot)$ {
expires 30d;
log_not_found off;
}

location / {
try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
try_files $uri =404;
fastcgi_pass 127.0.0.1:9000;
fastcgi_index index.php;
fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
fastcgi_param PATH_INFO $fastcgi_path_info;
include fastcgi_params;
}
}
NGINXEOF

RUN rm -f /etc/nginx/conf.d/default.conf

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/docker-entrypoint.sh"]

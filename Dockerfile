# Build stage
FROM php:8.2-fpm-alpine as builder

# Instalar dependencias
RUN apk add --no-cache \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    zip \
    unzip

# Instalar extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
COPY . /app

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs 2>&1 | tail -20

# Production stage
FROM php:8.2-fpm-alpine

# Instalar nginx y bash
RUN apk add --no-cache nginx bash

# Instalar extensiones PHP necesarias
RUN apk add --no-cache libpq libzip
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip

# Crear directorios necesarios
RUN mkdir -p /app /var/run/nginx /var/run/php-fpm

WORKDIR /app

# Copiar aplicación desde builder
COPY --from=builder --chown=www-data:www-data /app /app

# Configurar PHP-FPM
RUN cat > /usr/local/etc/php-fpm.d/zz-custom.conf << 'EOF'
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
EOF

# Crear directorio de logs y configurar permisos
RUN mkdir -p /app/storage/logs /app/bootstrap/cache && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app/storage /app/bootstrap/cache

# Configurar Nginx
RUN cat > /etc/nginx/conf.d/default.conf << 'EOF'
server {
listen 80 default_server;
server_name _;

root /app/public;
index index.php index.html;

client_max_body_size 20M;

access_log /var/log/nginx/access.log;
error_log /var/log/nginx/error.log warn;

# Gzip compression
gzip on;
gzip_vary on;
gzip_min_length 1000;
gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

# Security headers
add_header X-Frame-Options "SAMEORIGIN" always;
add_header X-Content-Type-Options "nosniff" always;
add_header X-XSS-Protection "1; mode=block" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;

# Health check
location = /health {
access_log off;
return 200 "OK";
add_header Content-Type text/plain;
}

# Deny access to dot files
location ~ /\. {
deny all;
access_log off;
log_not_found off;
}

# Static files caching
location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot|otf)$ {
expires 30d;
add_header Cache-Control "public, immutable";
log_not_found off;
}

# Laravel routing
location / {
try_files $uri $uri/ /index.php?$query_string;
}

# PHP handling
location ~ \.php$ {
try_files $uri =404;

fastcgi_pass 127.0.0.1:9000;
fastcgi_index index.php;

fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
fastcgi_param PATH_INFO $fastcgi_path_info;
fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
fastcgi_param QUERY_STRING $query_string;
fastcgi_param REQUEST_METHOD $request_method;
fastcgi_param CONTENT_TYPE $content_type;
fastcgi_param CONTENT_LENGTH $content_length;
fastcgi_param SERVER_NAME $server_name;
fastcgi_param SERVER_PORT $server_port;
fastcgi_param SERVER_ADDR $server_addr;
fastcgi_param SERVER_PROTOCOL $server_protocol;
fastcgi_param REMOTE_ADDR $remote_addr;
fastcgi_param REMOTE_PORT $remote_port;
fastcgi_param REMOTE_USER $remote_user;
fastcgi_param REQUEST_FILENAME $request_filename;
fastcgi_param REQUEST_URI $request_uri;
fastcgi_param DOCUMENT_URI $document_uri;
fastcgi_param HTTPS $https;

fastcgi_connect_timeout 60s;
fastcgi_send_timeout 60s;
fastcgi_read_timeout 60s;
fastcgi_buffer_size 128k;
fastcgi_buffers 256 16k;
}
}
EOF

# Copiar script de entrada
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

CMD ["/usr/local/bin/docker-entrypoint.sh"]

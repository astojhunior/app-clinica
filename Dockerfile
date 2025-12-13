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
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Production stage
FROM php:8.2-fpm-alpine

# Instalar nginx
RUN apk add --no-cache nginx curl supervisor

# Instalar extensiones PHP necesarias
RUN apk add --no-cache \
    libpq \
    libzip
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip

# Crear directorios necesarios
RUN mkdir -p /app /var/log/supervisor /var/run/nginx /var/run/php-fpm

WORKDIR /app

# Copiar aplicación desde builder
COPY --from=builder --chown=www-data:www-data /app /app

# Configurar PHP-FPM
RUN echo "[www]\nuser = www-data\ngroup = www-data\nlisten = 127.0.0.1:9000\npm.max_children = 10\npm.start_servers = 3\npm.min_spare_servers = 2\npm.max_spare_servers = 5\n" > /usr/local/etc/php-fpm.d/zz-custom.conf

# Crear directorio de logs y configurar permisos
RUN mkdir -p /app/storage/logs && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app/storage /app/bootstrap/cache

# Configurar Nginx
RUN rm -f /etc/nginx/conf.d/default.conf
RUN cat > /etc/nginx/conf.d/default.conf << 'EOF'
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    
    server_name _;
    
    root /app/public;
    index index.php;
    
    # Logs
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
    
    # Gzip compression
    gzip on;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Laravel rewrite rules
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP handling
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
    
    # Static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
    
    # Deny access to hidden files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
    
    # Health check endpoint
    location = /health {
        access_log off;
        return 200 "OK\n";
        add_header Content-Type text/plain;
    }
}
EOF

# Configurar supervisor para manejar php-fpm y nginx
RUN cat > /etc/supervisor/conf.d/supervisord.conf << 'EOF'
[supervisord]
nodaemon=true
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm -F -R
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/php-fpm.log
stdout_logfile=/var/log/supervisor/php-fpm.log

[program:nginx]
command=/usr/sbin/nginx -g "daemon off;"
autostart=true
autorestart=true
stderr_logfile=/var/log/supervisor/nginx.log
stdout_logfile=/var/log/supervisor/nginx.log
EOF

# Copiar script de entrada
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

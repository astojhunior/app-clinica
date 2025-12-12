# Usar imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libmcrypt-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    zip \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    mbstring \
    zip \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# Habilitar mod_rewrite para Laravel
RUN a2enmod rewrite headers

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . /app

# Dar permisos a los directorios de Laravel
RUN mkdir -p /app/storage /app/bootstrap/cache && \
    chown -R www-data:www-data /app && \
    chmod -R 755 /app/storage /app/bootstrap/cache

# Instalar dependencias de composer
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Configurar Apache para Laravel
RUN rm -f /etc/apache2/sites-available/000-default.conf && \
    echo '<VirtualHost *:80>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    ServerName localhost' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    DocumentRoot /app/public' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    <Directory /app/public>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Options Indexes FollowSymLinks' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        <IfModule mod_rewrite.c>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '            RewriteEngine On' >> /etc/apache2/sites-available/000-default.conf && \
    echo '            RewriteBase /' >> /etc/apache2/sites-available/000-default.conf && \
    echo '            RewriteCond %{REQUEST_FILENAME} !-f' >> /etc/apache2/sites-available/000-default.conf && \
    echo '            RewriteCond %{REQUEST_FILENAME} !-d' >> /etc/apache2/sites-available/000-default.conf && \
    echo '            RewriteRule ^(.*)$ index.php?$1 [QSA,L]' >> /etc/apache2/sites-available/000-default.conf && \
    echo '        </IfModule>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    ErrorLog ${APACHE_LOG_DIR}/error.log' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    CustomLog ${APACHE_LOG_DIR}/access.log combined' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/000-default.conf

# Asegurar que escucha en 0.0.0.0:80 (para Railway)
RUN echo "Listen 80" >> /etc/apache2/apache2.conf

# Copiar script de entrada
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Exponer puerto 80
EXPOSE 80

# Comando para iniciar
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

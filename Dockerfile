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
RUN a2enmod rewrite

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

# Configurar Apache
RUN echo '<Directory /app/public>' > /etc/apache2/sites-available/000-default.conf && \
    echo '    Options Indexes FollowSymLinks' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf && \
    echo '    Require all granted' >> /etc/apache2/sites-available/000-default.conf && \
    echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf && \
    echo 'DocumentRoot /app/public' >> /etc/apache2/sites-available/000-default.conf

# Exponer puerto
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Comando para iniciar Apache
CMD ["apache2-foreground"]

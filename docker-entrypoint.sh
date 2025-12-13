#!/bin/bash

set -e

echo "🚀 Iniciando aplicación Laravel..."

# Crear directorios si no existen
mkdir -p /app/storage/logs
mkdir -p /app/bootstrap/cache

# Permisos
chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/bootstrap

# Si no existe APP_KEY, generarlo
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generando APP_KEY..."
    php artisan key:generate --force
fi

# Cache de config
echo "💾 Cacheando configuración..."
php artisan config:cache --quiet 2>/dev/null || echo "⚠️  No se pudo cachear config"
php artisan route:cache --quiet 2>/dev/null || echo "⚠️  No se pudo cachear rutas"

# Intentar migrar base de datos (si está disponible)
echo "🔄 Verificando migraciones..."
php artisan migrate --force --quiet 2>/dev/null || echo "⚠️  Las migraciones no se pudieron ejecutar (DB podría no estar disponible)"

echo ""
echo "✅ Aplicación lista!"
echo "🌐 Iniciando nginx + php-fpm..."
echo ""

# Iniciar supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf




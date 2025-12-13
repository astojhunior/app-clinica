#!/bin/bash

set -e

echo "🚀 Iniciando aplicación Laravel..."

# Crear directorios si no existen
mkdir -p /app/storage/logs
mkdir -p /app/bootstrap/cache

# Permisos
chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Si no existe APP_KEY, generarlo
if [ -z "$APP_KEY" ]; then
    echo "⚙️  Generando APP_KEY..."
    php artisan key:generate --force
fi

# Cache de config
echo "💾 Cacheando configuración..."
php artisan config:cache --quiet 2>/dev/null || echo "⚠️  No se pudo cachear config"
php artisan route:cache --quiet 2>/dev/null || echo "⚠️  No se pudo cachear rutas"
php artisan view:cache --quiet 2>/dev/null || echo "⚠️  No se pudo cachear vistas"

# Intentar migrar base de datos (si está disponible)
echo "🔄 Verificando migraciones..."
if [ "$APP_ENV" = "production" ] || [ "$APP_ENV" = "staging" ]; then
    php artisan migrate --force --quiet 2>/dev/null || echo "⚠️  Las migraciones no se pudieron ejecutar (DB podría no estar disponible)"
else
    php artisan migrate --quiet 2>/dev/null || echo "⚠️  Las migraciones no se pudieron ejecutar"
fi

echo "✅ Aplicación lista!"
echo "🌐 Apache iniciando en puerto 80..."
echo ""

# Verificar que Apache está bien configurado
echo "📋 Validando configuración de Apache..."
apache2ctl configtest

echo ""
echo "🔥 Iniciando Apache..."

# Iniciar Apache en foreground
exec apache2-foreground



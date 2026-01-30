#!/bin/bash
set -e

# Asegurar permisos correctos
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Generar key de aplicación si no existe
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    if grep -q "^APP_KEY=$" /var/www/html/.env 2>/dev/null; then
        php artisan key:generate --no-interaction
    fi
fi

# Crear base de datos SQLite si no existe (para desarrollo)
if [ "$DB_CONNECTION" = "sqlite" ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Ejecutar migraciones (opcional, comentar si no se desea)
# php artisan migrate --force

# Limpiar y cachear configuración
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Ejecutar comando pasado (apache2-foreground por defecto)
exec "$@"

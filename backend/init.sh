#!/bin/bash
set -e

echo "Iniciando configuración de Laravel..."

# Ir al directorio de trabajo
cd /var/www/html

# Mostrar información de conexión (sin mostrar password)
echo "Configuración de base de datos:"
echo "   Host: $DB_HOST"
echo "   Port: $DB_PORT"
echo "   Database: $DB_DATABASE"
echo "   Username: $DB_USERNAME"

# Crear enlace simbólico del storage
echo "Creando enlace simbólico del storage..."
php artisan storage:link --force || true

# Ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders (solo si la variable está activada)
if [ "$RUN_SEEDERS" = "true" ]; then
    echo "Ejecutando seeders..."
    php artisan db:seed --force
fi

# Limpiar y optimizar cache
echo "Optimizando aplicación..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

echo "Configuración completada!"

# Iniciar supervisor
echo "Iniciando servicios..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
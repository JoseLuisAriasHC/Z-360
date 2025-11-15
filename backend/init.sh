#!/bin/bash
set -e

echo "Iniciando configuración de Laravel..."

# Ir al directorio de trabajo
cd /var/www/html

# Esperar a que la base de datos esté lista
echo "Esperando base de datos..."
until php artisan db:show 2>/dev/null; do
    echo "Base de datos no disponible, esperando..."
    sleep 3
done

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
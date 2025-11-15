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

# Esperar a que la base de datos esté lista con timeout
echo "Esperando base de datos..."
MAX_RETRIES=30
RETRY_COUNT=0

until php artisan db:show 2>/dev/null; do
    RETRY_COUNT=$((RETRY_COUNT + 1))
    if [ $RETRY_COUNT -ge $MAX_RETRIES ]; then
        echo "ERROR: No se pudo conectar a la base de datos después de $MAX_RETRIES intentos"
        echo "Verificando variables de entorno..."
        env | grep DB_ || true
        exit 1
    fi
    echo "Base de datos no disponible, reintento $RETRY_COUNT/$MAX_RETRIES..."
    sleep 5
done

echo "Conexión a base de datos establecida"

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
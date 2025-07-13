FROM php:8.2-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev libonig-dev libxml2-dev zip unzip git curl && \
    rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Crear grupo y usuario con UID/GID 1000 (ajustar si en mi host son distintos)
RUN groupadd -g 1000 laravelgroup && \
    useradd -u 1000 -g laravelgroup -m -s /bin/bash devuser

# Copiar imagen Composer
COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Usar usuario no root para evitar problemas de permisos
USER devuser

# Comando para arrancar Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

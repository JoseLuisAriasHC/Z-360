FROM php:8.2-fpm

# Instalar dependencias necesarias para PHP, GD y Imagick
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl \
    libmagickwand-dev imagemagick \
    && rm -rf /var/lib/apt/lists/*

# Configurar GD con soporte para JPEG y FreeType
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath

# Instalar la extensión Imagick para PHP
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Crear grupo y usuario con UID/GID 1000 (ajustar si en mi host son distintos)
RUN groupadd -g 1000 laravelgroup && \
    useradd -u 1000 -g laravelgroup -m -s /bin/bash devuser

# Copiar imagen de Composer
COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de dependencias y instalar
COPY src/composer.json src/composer.lock ./
RUN composer install --no-scripts --no-autoloader

# Copiar el resto de la aplicación
COPY src/. .
RUN composer dump-autoload --optimize

# Usar usuario no root para evitar problemas de permisos
USER devuser

# Comando para arrancar Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

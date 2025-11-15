FROM php:8.2-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip git curl \
    libmagickwand-dev imagemagick \
    nginx \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Configurar extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo_mysql mbstring exif pcntl bcmath

RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Copiar Composer
COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiar código fuente
COPY src/. .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Configurar permisos
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configuración de Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Configuración de Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000

CMD ["/usr/local/bin/init.sh"]
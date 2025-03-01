FROM php:8.1-fpm

# Instalar dependencias y extensiones de PHP
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    && docker-php-ext-install \
    pdo_mysql \
    zip \
    intl

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Configurar Git para evitar errores de propiedad
RUN git config --global --add safe.directory /var/www/html

# Instalar PHP-FPM y configurar para desarrollo
RUN apt-get install -y nginx && \
    rm -rf /var/lib/apt/lists/*

# Configurar Nginx
COPY nginx.conf /etc/nginx/sites-available/default

# Agregar script de inicio
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Crear directorios para logs, caché y asegurar permisos correctos
RUN mkdir -p /var/www/html/var/logs \
    && mkdir -p /var/www/html/var/cache/doctrine/proxies \
    && chown -R www-data:www-data /var/www/html/var \
    && chmod -R 777 /var/www/html/var

CMD ["/usr/local/bin/start.sh"]

EXPOSE 8000
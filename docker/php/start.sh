#!/bin/bash

# Asegurar que los directorios necesarios existan y tengan los permisos correctos
echo "Verificando directorios y permisos..."
mkdir -p /var/www/html/var/logs
mkdir -p /var/www/html/var/cache/doctrine/proxies
chown -R www-data:www-data /var/www/html/var
chmod -R 777 /var/www/html/var
echo "Directorios verificados."

# Iniciar PHP-FPM
echo "Iniciando PHP-FPM..."
php-fpm -D

# Iniciar Nginx
echo "Iniciando Nginx..."
nginx -g "daemon off;"
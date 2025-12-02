FROM php:8.2-fpm AS build

WORKDIR /app

# Instalar dependencias del sistema necesarias para Symfony
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip gd

# Copiar composer desde imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos de configuración
COPY composer.json composer.lock ./

# Permitir plugins aunque sea root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copiar el resto del código
COPY . .

# Instalar dependencias
RUN composer install --no-dev --optimize-autoloader

# Etapa 2: runtime
FROM php:8.2-fpm

WORKDIR /var/www/html

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev libonig-dev \
    && docker-php-ext-install pdo pdo_mysql intl zip gd

# Copiar dependencias y código desde la etapa build
COPY --from=build /app .


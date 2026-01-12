# Partimos de PHP 8.2 (FPM es ideal para entornos web Symfony)
FROM php:8.2-fpm

# Instalamos las librerías del sistema necesarias
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Configuramos e instalamos extensiones de PHP (MySQL, Intl, Zip y GD para imágenes)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql intl zip gd

# Copiamos Composer desde la imagen oficial (indispensable para DAW)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecemos el directorio de trabajo (coincidiendo con tu docker-compose)
WORKDIR /var/www/html

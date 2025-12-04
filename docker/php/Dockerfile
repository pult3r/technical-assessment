FROM php:8.4-fpm

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip \
        pdo \
        pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Instalacja Composera (z oficjalnego obrazu)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Opcjonalnie: zwiększenie limitów dla PHP (często potrzebne przy Laravel)
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini \
    && echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" > /usr/local/etc/php/conf.d/posts.ini

WORKDIR /var/www/html

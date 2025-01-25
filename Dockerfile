FROM php:8.2-fpm

LABEL authors="Omidreza Salari"
LABEL version="1.0"
LABEL description="This is a dockerfile to run app for match ordering"
LABEL environment="development"

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www


RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache


EXPOSE 9000

CMD ["php-fpm"]

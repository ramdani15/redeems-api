FROM php:8-fpm

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    wget \
    ntp \
    gnupg \
    nodejs npm 


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"

WORKDIR /var/www/html
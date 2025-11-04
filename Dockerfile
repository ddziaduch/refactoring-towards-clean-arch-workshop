FROM php:8.3-cli

# install required system packages
RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# set workdir
WORKDIR /opt/project

# copy app files
COPY . .

# install deps
RUN composer install

# set port
EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

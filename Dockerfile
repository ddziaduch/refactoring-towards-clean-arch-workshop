FROM php:8.3-cli

# Instalacja niezbędnych pakietów systemowych
RUN apt-get update && apt-get install -y libpq-dev
RUN docker-php-ext-install pdo pdo_pgsql
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Instalacja Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Ustawienie katalogu roboczego
WORKDIR /opt/project

# Kopiowanie plików aplikacji
COPY . .

# Instalacja zależności
RUN composer install

# Eksponowanie portu
EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

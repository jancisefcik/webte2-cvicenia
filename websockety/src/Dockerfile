FROM php:8.3-fpm

RUN docker-php-ext-install pdo_mysql mysqli pcntl
RUN docker-php-ext-configure pcntl --enable-pcntl

# COPY . /var/www/node8.webte.fei.stuba.sk
# WORKDIR /var/www/node8.webte.fei.stuba.sk

# CMD [ "php", "./wsserver.php", "start", "-d" ]
# CMD ["php-fpm"]